<?php

namespace App\Jobs;


use App\Models\Order;
use App\Models\OrderOffer;
use App\Events\Order\OrderExpired;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class ExpireOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        // التحقق إذا كان الطلب لا يزال معلقاً
        if ($this->order->order_status_id != 1) {
            return;
        }

        DB::transaction(function () {
            // تحديث حالة الطلب
            $this->order->update([
                'order_status_id' => 7, // expired
            ]);

            // تحديث جميع العروض
            OrderOffer::where('order_id', $this->order->id)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);

            // إرسال إشعار للمستخدم
            $this->notifyUser();

            // إرسال إشعار للسائقين
            $this->notifyDrivers();

            // Broadcast Event
            event(new OrderExpired($this->order));
        });
    }

    private function notifyUser()
    {
        $user = $this->order->user;
        $tokens = $user->activeDeviceTokens->pluck('token')->toArray();

        if (!empty($tokens)) {
            app(\App\Services\FirebaseNotificationService::class)
                ->sendToMultipleDevices($tokens, [
                    'title' => 'انتهاء صلاحية الطلب',
                    'body' => 'انتهت صلاحية الطلب. يمكنك إنشاء طلب جديد.',
                    'image' => null,
                ], [
                    'order_id' => $this->order->id,
                    'type' => 'order_expired',
                ]);
        }
    }

    private function notifyDrivers()
    {
        $offers = OrderOffer::where('order_id', $this->order->id)
            ->with('driver.user.activeDeviceTokens')
            ->get();

        foreach ($offers as $offer) {
            $tokens = $offer->driver->user->activeDeviceTokens->pluck('token')->toArray();

            if (!empty($tokens)) {
                app(\App\Services\FirebaseNotificationService::class)
                    ->sendToMultipleDevices($tokens, [
                        'title' => 'طلب منتهي',
                        'body' => 'انتهت صلاحية الطلب الذي قدمت عليه.',
                        'image' => null,
                    ], [
                        'order_id' => $this->order->id,
                        'type' => 'offer_expired',
                    ]);
            }
        }
    }
}
