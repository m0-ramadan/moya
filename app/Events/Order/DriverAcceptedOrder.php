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
        // التحقق من وجود الطلب
        if (!$this->offer->order) {
            Log::error('DriverAcceptedOrder: order is null', [
                'offer_id' => $this->offer->id,
            ]);
            return [];
        }

        // التحقق من معرف المستخدم في الطلب
        if (!$this->offer->order->user_id) {
            Log::error('DriverAcceptedOrder: user_id is null', [
                'offer_id' => $this->offer->id,
                'order_id' => $this->offer->order->id,
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
        // التحقق الشامل لجميع العلاقات
        $driver = $this->offer->driver ?? null;
        $driverUser = $driver?->user ?? null;
        $order = $this->offer->order ?? null;

        // بيانات السائق
        $driverData = [];
        if ($driver) {
            $driverData = [
                'id' => $driver->id,
                'name' => $driverUser?->full_name 
                    ?? $driverUser?->name 
                    ?? $driver->name 
                    ?? 'غير معروف',
                'phone' => $driverUser?->phone ?? $driver->phone ?? null,
            ];
        }

        // بيانات العرض
        $offerData = [
            'id' => $this->offer->id,
            'price' => $this->offer->price ?? 0,
            'delivery_duration_minutes' => $this->offer->delivery_duration_minutes ?? 0,
            'created_at' => $this->offer->created_at?->toDateTimeString() ?? now()->toDateTimeString(),
        ];

        // بيانات الطلب
        $orderData = [
            'id' => $order?->id ?? $this->offer->order_id,
        ];

        return [
            'offer' => array_merge($offerData, [
                'driver' => !empty($driverData) ? $driverData : null
            ]),
            'order' => $orderData,
            'remaining_drivers_count' => $this->remainingDriversCount ?? 0,
        ];
    }

    private function getRemainingDriversCount()
    {
        if (!$this->offer->order_id) {
            Log::warning('DriverAcceptedOrder: order_id is null for offer', [
                'offer_id' => $this->offer->id,
            ]);
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
                'order_id' => $this->offer->order_id,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}