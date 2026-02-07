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
        // 🔴 الإصلاح النهائي للسطر 42
        // استخدام الدالة المساعدة للحصول على بيانات السائق بشكل آمن
        $driverData = $this->extractDriverDataSafely();

        return [
            'offer' => [
                'id' => $this->offer->id,
                'driver_id' => $driverData['id'],
                'driver_name' => $driverData['name'],
                'driver_phone' => $driverData['phone'],
                'price' => $this->offer->price ?? 0,
                'delivery_duration_minutes' => $this->offer->delivery_duration_minutes ?? 0,
                'created_at' => $this->offer->created_at ? $this->offer->created_at->toDateTimeString() : now()->toDateTimeString(),
            ],
            'order_id' => $this->offer->order_id,
            'remaining_drivers_count' => $this->remainingDriversCount ?? 0,
        ];
    }

    /**
     * استخراج بيانات السائق بشكل آمن
     */
    private function extractDriverDataSafely()
    {
        $id = null;
        $name = 'غير معروف';
        $phone = null;
        
        // هذا هو المفتاح: تحقق عميق متعدد المستويات
        if ($this->offer->driver_id && $this->offer->driver) {
            $id = $this->offer->driver->id;
            
            // تحقق وجود user object
            $user = $this->offer->driver->user;
            if ($user) {
                $name = $user->full_name ?? $user->name ?? 'غير معروف';
                $phone = $user->phone ?? null;
            } else {
                $name = $this->offer->driver->name ?? 'سائق';
            }
        }
        
        return ['id' => $id, 'name' => $name, 'phone' => $phone];
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