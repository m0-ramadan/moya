<?php

namespace App\Events\Order;

use App\Models\OrderOffer;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
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
        // 🔴 لازم نحمل العلاقات صراحة (queue-safe)
        $this->offer = $offer->load([
            'driver.user',
            'order',
        ]);

        $this->remainingDriversCount = $this->getRemainingDriversCount();
    }

    /**
     * ❌ امنع البث لو البيانات ناقصة
     */
    public function broadcastWhen(): bool
    {
        return
            $this->offer?->driver !== null &&
            $this->offer?->driver?->user !== null &&
            $this->offer?->order !== null;
    }

    public function broadcastOn()
    {
        return new Channel('user.' . $this->offer->order->user_id);
    }

    public function broadcastAs()
    {
        return 'DriverAcceptedOrder';
    }

    public function broadcastWith()
    {
        $driver = $this->offer->driver;
        $user   = $driver?->user;

        // 🧯 حماية إضافية (عمره ما يكسر production)
        if (!$driver || !$user) {
            Log::warning('DriverAcceptedOrder skipped بسبب بيانات ناقصة', [
                'offer_id' => $this->offer->id,
                'driver_exists' => (bool) $driver,
                'user_exists' => (bool) $user,
            ]);

            return [];
        }

        return [
            'offer' => [
                'id' => $this->offer->id,
                'driver_id' => $driver->id,
                'driver_name' => $user->name,
                'driver_phone' => $user->phone ?? null,
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
