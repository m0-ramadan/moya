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
        // تحميل العلاقات مع تحقق null
        try {
            $this->offer = $offer->load([
                'driver.user',
                'order'
            ]);
        } catch (\Exception $e) {
            Log::error('DriverAcceptedOrder: Error loading relations', [
                'offer_id' => $offer->id,
                'error' => $e->getMessage()
            ]);
            $this->offer = $offer;
        }

        $this->remainingDriversCount = $this->getRemainingDriversCount();
    }

    public function broadcastOn()
    {
        // 🔴 تحقق شامل
        if (!$this->offer || !$this->offer->order || !$this->offer->order->user_id) {
            Log::error('DriverAcceptedOrder: Invalid data for broadcasting', [
                'offer_exists' => !empty($this->offer),
                'order_exists' => !empty($this->offer->order),
                'user_id' => $this->offer->order->user_id ?? null,
                'offer_id' => $this->offer->id ?? null,
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
        // 🔴 هذا هو السطر 42 الذي يسبب المشكلة - إصلاحه كالتالي:
        
        // تحقق متعدد المستويات
        $driver = $this->offer->driver ?? null;
        
        // حل المشكلة: تحقق إذا كان السائق موجوداً قبل محاولة الوصول إلى user
        $driverName = 'غير معروف';
        $driverPhone = null;
        
        if ($driver && $driver->user) {
            $driverName = $driver->user->full_name ?? $driver->user->name ?? 'غير معروف';
            $driverPhone = $driver->user->phone ?? null;
        } elseif ($driver) {
            // إذا كان هناك سائق ولكن بدون user
            $driverName = $driver->name ?? 'سائق';
        }

        return [
            'offer' => [
                'id' => $this->offer->id ?? null,
                'driver_id' => $driver->id ?? null,
                'driver_name' => $driverName,
                'driver_phone' => $driverPhone,
                'price' => $this->offer->price ?? 0,
                'delivery_duration_minutes' => $this->offer->delivery_duration_minutes ?? 0,
                'created_at' => $this->offer->created_at ? $this->offer->created_at->toDateTimeString() : now()->toDateTimeString(),
            ],
            'order_id' => $this->offer->order_id ?? null,
            'remaining_drivers_count' => $this->remainingDriversCount ?? 0,
        ];
    }

    private function getRemainingDriversCount()
    {
        if (!$this->offer || !$this->offer->order_id) {
            return 0;
        }

        try {
            return OrderOffer::where('order_id', $this->offer->order_id)
                ->where('id', '!=', $this->offer->id)
                ->where('status', 'pending')
                ->count();
        } catch (\Exception $e) {
            Log::error('DriverAcceptedOrder: Error counting remaining drivers', [
                'offer_id' => $this->offer->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}