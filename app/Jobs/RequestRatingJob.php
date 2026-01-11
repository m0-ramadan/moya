<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderRating;
use App\Events\RatingRequested;
use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class RequestRatingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $attempt;

    public $tries = 3;
    public $backoff = [60, 300, 600]; // 1 دقيقة، 5 دقائق، 10 دقائق

    public function __construct(Order $order, $attempt = 1)
    {
        $this->order = $order;
        $this->attempt = $attempt;
    }

    public function handle(FirebaseNotificationService $firebaseService)
    {
        try {
            // التحقق إذا كان الطلب قد تم تقييمه بالفعل
            $existingRating = OrderRating::where('order_id', $this->order->id)
                ->where('rated_by', 'user')
                ->exists();

            if ($existingRating) {
                Log::info('Order already rated, skipping rating request', [
                    'order_id' => $this->order->id,
                ]);
                return;
            }

            // التحقق إذا كان المستخدم لديه جهاز نشط
            $user = $this->order->user;
            $tokens = $user->activeDeviceTokens->pluck('token')->toArray();

            if (empty($tokens)) {
                Log::warning('User has no active device tokens for rating request', [
                    'order_id' => $this->order->id,
                    'user_id' => $user->id,
                ]);

                // إعادة المحاولة بعد فترة إذا كان المستخدم ليس لديه أجهزة نشطة
                if ($this->attempt < $this->tries) {
                    $this->retryLater($this->attempt + 1);
                }
                return;
            }

            // إعداد بيانات الإشعار
            $notificationData = [
                'title' => 'كيف كانت تجربتك؟ ✨',
                'body' => 'ساعدنا بتحسين الخدمة من خلال تقييم السائق ' . $this->order->driver->full_name,
                'image' => $this->order->driver->photo ?? null,
            ];

            $firebaseData = [
                'order_id' => $this->order->id,
                'driver_id' => $this->order->driver_id,
                'driver_name' => $this->order->driver->full_name,
                'driver_photo' => $this->order->driver->photo ?? null,
                'driver_rating' => $this->order->driver->average_rating,
                'type' => 'rating_request',
                'action' => 'rate_driver',
                'attempt' => $this->attempt,
                'expires_at' => now()->addHours(24)->toDateTimeString(), // 24 ساعة للرد
            ];

            // إرسال الإشعار
            $result = $firebaseService->sendToMultipleDevices($tokens, $notificationData, $firebaseData);

            // تسجيل نتيجة الإرسال
            Log::info('Rating request sent', [
                'order_id' => $this->order->id,
                'user_id' => $user->id,
                'attempt' => $this->attempt,
                'successful_tokens' => $result['successful'] ?? 0,
                'failed_tokens' => $result['failed'] ?? 0,
            ]);

            // إرسال Event لتتبع طلب التقييم
            event(new RatingRequested($this->order, $this->attempt, $result));

            // جدولة إعادة المحاولة إذا لم يتم الرد
            if ($this->attempt < $this->tries) {
                $this->scheduleRetry();
            }
        } catch (\Exception $e) {
            Log::error('Failed to send rating request: ' . $e->getMessage(), [
                'order_id' => $this->order->id,
                'attempt' => $this->attempt,
                'error' => $e->getTraceAsString(),
            ]);

            // إعادة المحاولة في حالة الخطأ
            if ($this->attempt < $this->tries) {
                $this->retryLater($this->attempt + 1);
            }

            throw $e;
        }
    }

    /**
     * جدولة إعادة المحاولة
     */
    private function scheduleRetry()
    {
        $nextDelay = $this->backoff[$this->attempt] ?? 3600; // ساعة واحدة كحد أقصى

        self::dispatch($this->order, $this->attempt + 1)
            ->delay(now()->addSeconds($nextDelay))
            ->onQueue('rating-requests');
    }

    /**
     * إعادة المحاولة مع تأخير
     */
    private function retryLater($nextAttempt)
    {
        $delay = $this->backoff[$nextAttempt - 1] ?? 3600;

        self::dispatch($this->order, $nextAttempt)
            ->delay(now()->addSeconds($delay))
            ->onQueue('rating-requests');
    }

    /**
     * التعامل مع فشل Job
     */
    public function failed(\Throwable $exception)
    {
        Log::error('RequestRatingJob failed permanently', [
            'order_id' => $this->order->id,
            'attempt' => $this->attempt,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // يمكنك إضافة إجراءات إضافية هنا مثل:
        // - إرسال بريد إلكتروني للمسؤول
        // - تسجيل في قاعدة البيانات
        // - إشعار نظامي
    }
}
