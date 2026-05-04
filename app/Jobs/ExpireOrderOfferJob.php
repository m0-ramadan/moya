<?php

namespace App\Jobs;

use App\Events\Order\OfferExpired;
use App\Models\OrderOffer;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpireOrderOfferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $offerId;

    /**
     * Create a new job instance.
     */
    public function __construct($offerId)
    {
        $this->offerId = $offerId;
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseNotificationService $firebaseService): void
    {
        try {
            $offer = OrderOffer::with(['order.user', 'driver.user'])
                ->find($this->offerId);

            // التحقق من وجود العرض وأنه لا يزال pending
            if (!$offer || $offer->status !== 'pending') {
                Log::info("Offer {$this->offerId} is no longer pending, skipping expiration");
                return;
            }

            // التحقق من أن وقت الانتهاء قد حان
            if (!$offer->expired_at || Carbon::now()->lessThan($offer->expired_at)) {
                Log::info("Offer {$this->offerId} hasn't expired yet");
                return;
            }

            // تحديث حالة العرض إلى expired
            $offer->update([
                'status' => 'expired'
            ]);

            // تحميل العلاقات المطلوبة
            $offer->load(['order.user', 'driver.user']);

            Log::info("Offer {$this->offerId} has been expired", [
                'order_id' => $offer->order_id,
                'driver_id' => $offer->driver_id,
                'expired_at' => $offer->expired_at,
            ]);

            // إرسال إشعار للسائق بأن عرضه انتهى
            $this->notifyDriverAboutExpiredOffer($offer, $firebaseService);

            // إرسال إشعار للمستخدم (اختياري)
            $this->notifyUserAboutExpiredOffer($offer, $firebaseService);

            // إطلاق Event للـ Broadcasting
            event(new OfferExpired($offer));

            // التحقق من حالة الطلب - إذا كانت كل العروض منتهية
            $this->checkOrderStatus($offer->order);

        } catch (\Exception $e) {
            Log::error("Error in ExpireOrderOfferJob for offer {$this->offerId}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // إعادة المحاولة مرة واحدة بعد 5 ثوانٍ في حالة الفشل
            if ($this->attempts() < 2) {
                $this->release(5);
            }
        }
    }

    /**
     * إشعار السائق بأن عرضه انتهى
     */
    private function notifyDriverAboutExpiredOffer($offer, $firebaseService): void
    {
        try {
            $driver = $offer->driver;
            if (!$driver || !$driver->user) {
                return;
            }

            $user = $driver->user;

            // إنشاء إشعار في قاعدة البيانات
            $notification = $user->createNotification([
                'title' => 'انتهاء صلاحية العرض',
                'message' => 'انتهت صلاحية عرضك للطلب رقم #' . $offer->order_id . ' دون استجابة من المستخدم',
                'type' => 'offer_expired',
                'data' => [
                    'offer_id' => $offer->id,
                    'order_id' => $offer->order_id,
                    'driver_id' => $driver->id,
                    'click_action' => 'OFFER_EXPIRED_ACTION',
                ],
            ]);

            // إرسال إشعار Firebase
            $tokens = $user->activeDeviceTokens
                ->pluck('token')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (!empty($tokens)) {
                $firebaseService->sendToMultipleDevices(
                    $tokens,
                    [
                        'title' => 'انتهاء صلاحية العرض',
                        'body' => 'انتهت صلاحية عرضك للطلب رقم #' . $offer->order_id . ' دون استجابة من المستخدم',
                        'image' => null,
                    ],
                    [
                        'offer_id' => (string) $offer->id,
                        'order_id' => (string) $offer->order_id,
                        'type' => 'offer_expired',
                        'notification_id' => (string) ($notification->id ?? ''),
                        'click_action' => 'OFFER_EXPIRED_ACTION',
                    ]
                );
            }

            Log::info("Expired offer notification sent to driver {$driver->id}");
        } catch (\Exception $e) {
            Log::error("Failed to notify driver about expired offer: " . $e->getMessage());
        }
    }

    /**
     * إشعار المستخدم بأن عرضاً ما قد انتهى
     */
    private function notifyUserAboutExpiredOffer($offer, $firebaseService): void
    {
        try {
            $order = $offer->order;
            if (!$order || !$order->user) {
                return;
            }

            $user = $order->user;

            // حساب العروض المتبقية
            $remainingOffers = OrderOffer::where('order_id', $order->id)
                ->where('status', 'pending')
                ->count();

            // إرسال إشعار فقط إذا كانت هناك عروض أخرى متبقية
            if ($remainingOffers > 0) {
                $notification = $user->createNotification([
                    'title' => 'انتهاء عرض',
                    'message' => "انتهت صلاحية أحد العروض لطلبك. لا يزال لديك {$remainingOffers} عروض متاحة",
                    'type' => 'offer_expired_for_user',
                    'data' => [
                        'offer_id' => $offer->id,
                        'order_id' => $order->id,
                        'remaining_offers' => $remainingOffers,
                        'click_action' => 'VIEW_ORDER_OFFERS',
                    ],
                ]);

                $tokens = $user->activeDeviceTokens
                    ->pluck('token')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                if (!empty($tokens)) {
                    $firebaseService->sendToMultipleDevices(
                        $tokens,
                        [
                            'title' => 'انتهاء عرض',
                            'body' => "انتهت صلاحية أحد العروض لطلبك. لا يزال لديك {$remainingOffers} عروض متاحة",
                            'image' => null,
                        ],
                        [
                            'order_id' => (string) $order->id,
                            'remaining_offers' => (string) $remainingOffers,
                            'type' => 'offer_expired_for_user',
                            'notification_id' => (string) ($notification->id ?? ''),
                            'click_action' => 'VIEW_ORDER_OFFERS',
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to notify user about expired offer: " . $e->getMessage());
        }
    }

    /**
     * التحقق من حالة الطلب - إذا كانت كل العروض منتهية
     */
    private function checkOrderStatus($order): void
    {
        if (!$order) {
            return;
        }

        // التحقق من وجود أي عروض نشطة
        $hasActiveOffers = OrderOffer::where('order_id', $order->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();

        // إذا لم تكن هناك عروض نشطة والطلب لم يتم تأكيده
        if (!$hasActiveOffers && $order->order_status_id == 1) { // pending status
            Log::info("All offers for order {$order->id} have expired. Order may need to be cancelled or re-notified.");
            
            // يمكنك هنا إضافة منطق إضافي مثل:
            // - إرسال إشعار للمستخدم
            // - إلغاء الطلب تلقائياً
            // - إعادة إشعار سائقين جدد
        }
    }
}