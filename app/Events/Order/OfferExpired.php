<?php

namespace App\Events\Order;

use App\Models\OrderOffer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferExpired implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $offer;
    public $expiredAt;

    public function __construct(OrderOffer $offer)
    {
        $this->offer = $offer;
        $this->expiredAt = now();

        // تحميل العلاقات المطلوبة
        $this->offer->load([
            'order.user', 
            'driver.user'
        ]);
    }

    public function broadcastOn()
    {
        $channels = [];
        
        // قناة السائق صاحب العرض
        if ($this->offer->driver && $this->offer->driver->user) {
            $channels[] = new Channel('driver.' . $this->offer->driver->user->id);
        }
        
        // قناة المستخدم صاحب الطلب
        if ($this->offer->order && $this->offer->order->user) {
            $channels[] = new Channel('user.' . $this->offer->order->user->id);
        }
        
        // قناة الطلب العامة
        $channels[] = new Channel('order.' . $this->offer->order_id);

        return $channels;
    }

    public function broadcastAs()
    {
        return 'OfferExpired';
    }

    public function broadcastWith()
    {
        return [
            'offer_id' => $this->offer->id,
            'order_id' => $this->offer->order_id,
            'driver_id' => $this->offer->driver_id,
            'status' => 'expired',
            'message' => 'انتهت صلاحية عرض السائق',
            'message_ar' => 'انتهت صلاحية العرض دون استجابة من المستخدم',
            'expired_at' => $this->expiredAt->format('Y-m-d H:i:s'),
            'reason' => 'انتهت مدة الانتظار المحددة للعرض',
            'price' => $this->offer->price,
            'delivery_duration' => $this->offer->delivery_duration_minutes,
        ];
    }
}