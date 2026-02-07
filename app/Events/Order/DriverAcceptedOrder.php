<?php

namespace App\Events\Order;

use App\Models\OrderOffer;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DriverAcceptedOrder implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $offer;
    public $remainingDriversCount;

public function __construct(OrderOffer $offer)
{
    $this->offer = $offer->load([
        'driver.user',
        'order'
    ]);

    $this->remainingDriversCount = $this->getRemainingDriversCount();
}


    public function broadcastOn()
    {
        // 🔴 Guard مهم جدًا
        if (!$this->offer->order) {
            Log::error('DriverAcceptedOrder: order is null', [
                'offer_id' => $this->offer->id,
            ]);

            return [];
        }

        return new PrivateChannel('user.' . $this->offer->order->user_id);
    }

    public function broadcastAs()
    {
        return 'DriverAcceptedOrder';
    }

public function broadcastWith()
{
    $driver = $this->offer->driver;
    $driverUser = $driver?->user;

    return [
        'offer' => [
            'id' => $this->offer->id,
            'driver_id' => $driver?->id,
            'driver_name' => $driverUser?->full_name 
                ?? $driverUser?->name 
                ?? 'غير معروف',
            'driver_phone' => $driverUser?->phone,
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
