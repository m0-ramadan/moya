<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderExpired implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $expiredAt;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->expiredAt = now();
    }

    public function broadcastOn()
    {
        $channels = [
            new Channel('user.' . $this->order->user_id),
            new Channel('order.' . $this->order->id),
        ];

        // لكل السائقين الذين تقدموا للطلب
        $drivers = \App\Models\OrderOffer::where('order_id', $this->order->id)
            ->pluck('driver_id');

        foreach ($drivers as $driverId) {
            $channels[] = new Channel('driver.' . $driverId);
        }

        return $channels;
    }

    public function broadcastAs()
    {
        return 'OrderExpired';
    }

    public function broadcastWith()
    {
        return [
            'order_id' => $this->order->id,
            'status' => 'expired',
            'message' => 'انتهت صلاحية الطلب',
            'expired_at' => $this->expiredAt->toDateTimeString(),
            'reason' => 'انتهت مدة الانتظار بدون تأكيد من المستخدم',
        ];
    }
}
