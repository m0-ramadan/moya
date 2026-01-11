<?php

namespace App\Events;

use App\Models\OrderOffer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $offer;
    public $reason;

    public function __construct(OrderOffer $offer, string $reason = 'driver_cancelled')
    {
        $this->offer = $offer;
        $this->reason = $reason;
    }

    public function broadcastOn()
    {
        return [
            new Channel('user.' . $this->offer->order->user_id),
            new Channel('driver.' . $this->offer->driver_id),
            new Channel('order.' . $this->offer->order_id),
        ];
    }

    public function broadcastAs()
    {
        return 'OfferCancelled';
    }

    public function broadcastWith()
    {
        return [
            'offer_id' => $this->offer->id,
            'order_id' => $this->offer->order_id,
            'driver_id' => $this->offer->driver_id,
            'status' => 'cancelled',
            'reason' => $this->reason,
            'cancelled_at' => now()->toDateTimeString(),
        ];
    }
}
