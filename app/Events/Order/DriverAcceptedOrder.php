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
        $this->offer = $offer;
        $this->remainingDriversCount = $this->getRemainingDriversCount();
    }

    public function broadcastOn()
    {
        if (!$this->offer->order) {
            try {
                $this->offer->load('order');
            } catch (\Exception $e) {
                Log::error('DriverAcceptedOrder: Failed to load order', [
                    'offer_id' => $this->offer->id,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        }

        if (!$this->offer->order || !$this->offer->order->user_id) {
            Log::warning('DriverAcceptedOrder: No order or user_id found', [
                'offer_id' => $this->offer->id,
                'has_order' => !empty($this->offer->order),
                'user_id' => $this->offer->order->user_id ?? null
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
        // تحميل العلاقات إذا لم تكن محملة
        if (!$this->offer->relationLoaded('driver')) {
            $this->offer->load('driver.user');
        }

        // تحقق آمن من السائق والمستخدم - هذا هو السطر 42 الآن
        $driverName = 'غير معروف';
        $driverPhone = null;
        $driverId = null;
        
        // تحقق وجود السائق أولاً
        if ($this->offer->driver) {
            $driverId = $this->offer->driver->id;
            
            // تحقق وجود المستخدم للسائق
            if ($this->offer->driver->user) {
                $driverName = $this->offer->driver->user->full_name 
                    ?? $this->offer->driver->user->name 
                    ?? 'غير معروف';
                $driverPhone = $this->offer->driver->user->phone ?? null;
            } else {
                // إذا كان السائق موجود ولكن بدون بيانات مستخدم
                $driverName = $this->offer->driver->name ?? 'سائق';
            }
        }

        return [
            'offer' => [
                'id' => $this->offer->id,
                'driver_id' => $driverId,
                'driver_name' => $driverName,
                'driver_phone' => $driverPhone,
                'price' => $this->offer->price ?? 0,
                'delivery_duration_minutes' => $this->offer->delivery_duration_minutes ?? 0,
                'created_at' => $this->offer->created_at ? $this->offer->created_at->toDateTimeString() : now()->toDateTimeString(),
            ],
            'order_id' => $this->offer->order_id,
            'remaining_drivers_count' => $this->remainingDriversCount ?? 0,
        ];
    }

    private function getRemainingDriversCount()
    {
        if (!$this->offer->order_id) {
            return 0;
        }

        try {
            return OrderOffer::where('order_id', $this->offer->order_id)
                ->where('id', '!=', $this->offer->id)
                ->where('status', 'pending')
                ->count();
        } catch (\Exception $e) {
            Log::error('DriverAcceptedOrder: Error counting remaining drivers', [
                'offer_id' => $this->offer->id,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}