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
        $this->offer = $offer->loadMissing([
            'order',
            'driver.user',
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
        // 🔴 Guard إضافي
        if (!$this->offer->driver || !$this->offer->driver->user) {
            Log::warning('DriverAcceptedOrder: driver or user missing', [
                'offer_id' => $this->offer->id,
                'driver_id' => $this->offer->driver_id,
            ]);
        }

        return [
            'offer' => [
                'id' => $this->offer->id,
                'driver_id' => $this->offer->driver_id,
                'driver_name' => $this->offer->driver?->user?->name,
                'driver_phone' => $this->offer->driver?->user?->phone,
                'price' => $this->offer->price,
                'delivery_duration_minutes' => $this->offer->delivery_duration_minutes,
                'created_at' => $this->offer->created_at?->toIso8601String(),
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
