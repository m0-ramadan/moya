<?php

namespace App\Events\Order;

use App\Models\OrderOffer;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DriverAcceptedOrder implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $offer;
    public $remainingDriversCount;

    // public function __construct(OrderOffer $offer)
    // {
    //     $this->offer = $offer;
    //     $this->remainingDriversCount = $this->getRemainingDriversCount();
    // }
    public function __construct(OrderOffer $offer)
{
    $this->offer = $offer->loadMissing([
        'order',
        'driver.user'
    ]);

    $this->remainingDriversCount = $this->getRemainingDriversCount();
}


    // public function broadcastOn()
    // {
    //     // للـ User فقط
    //     return new Channel('user.' . $this->offer->order->user_id);
    // }
    public function broadcastOn()
{
       Log::info('Broadcasting DriverAcceptedOrder', [
        'channel' => 'user.' . $this->offer->order->user_id
    ]);
    return new PrivateChannel('user.' . $this->offer->order->user_id);
}

    public function broadcastAs()
    {
        return 'DriverAcceptedOrder';
    }

    public function broadcastWith()
    {
        return [
            'offer' => [
                'id' => $this->offer->id,
                'driver_id' => $this->offer->driver_id,
                'driver_name' => $this->offer->driver?->user?->name,
                'driver_phone' => $this->offer->driver?->user?->phone ?? null,
                'price' => $this->offer->price,
                'delivery_duration_minutes' => $this->offer->delivery_duration_minutes,
                'created_at' => $this->offer->created_at,
            ],
            'order_id' => $this->offer->order_id,
            'remaining_drivers_count' => $this->remainingDriversCount,
        ];
    }

    private function getRemainingDriversCount()
    {
        return OrderOffer::where('order_id', $this->offer->order_id)
            ->where('id', '!=', $this->offer->id)
            ->where('status', 'pending')
            ->count();
    }
}
