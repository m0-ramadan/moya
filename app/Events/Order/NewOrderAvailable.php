<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderAvailable implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $availableDriversCount;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->availableDriversCount = $this->getAvailableDriversCount();
    }

    public function broadcastOn()
    {
        return new Channel('new.orders');
    }

    public function broadcastAs()
    {
        return 'NewOrderAvailable';
    }

    public function broadcastWith()
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'user_name' => $this->order->user->name,
                'user_phone' => $this->order->user->phone,
                'service_name' => $this->order->service->name,
                'water_type' => $this->order->waterType->name ?? null,
                'location' => [
                    'address' => $this->order->location->address_details,
                    'latitude' => $this->order->location->latitude,
                    'longitude' => $this->order->location->longitude,
                ],
                'created_at' => $this->order->created_at,
                'estimated_price' => $this->order->price ?? null,
            ],
            'available_drivers_count' => $this->availableDriversCount,
            'expires_at' => optional($this->order->expires_at)->toDateTimeString(),
        ];
    }

    private function getAvailableDriversCount()
    {
        // عدد السائقين المتاحين (ليس لديهم طلبات نشطة)
        return \App\Models\Driver::where('is_active', true)
            ->where('status', 'active')
            ->whereDoesntHave('orders', function ($query) {
                $query->whereIn('order_status_id', [1, 2, 3, 4]); // الطلبات النشطة
            })
            ->count();
    }
}
