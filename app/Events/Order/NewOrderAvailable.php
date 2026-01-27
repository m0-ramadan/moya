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
        // Load required relationships if not already loaded
        $this->order->loadMissing([
            'user',
            'service',
            'waterType',
            'location',
            'user.savedLocations' // Add this to get the saved location
        ]);

        // Get the saved location
        $savedLocation = $this->order->location;

        return [
            'order' => [
                'id' => $this->order->id,
                'user_name' => $this->order->user->name,
                'user_phone' => $this->order->user->phone, // Ensure phone is populated
                'service_name' => $this->order->service->name,
                'water_type' => $this->order->waterType ? $this->order->waterType->name : 'غير محدد', // Fix null case
                'location' => [
                    'address' => $savedLocation ? $savedLocation->address_details : null,
                    'latitude' => $savedLocation ? $savedLocation->latitude : null,
                    'longitude' => $savedLocation ? $savedLocation->longitude : null,
                ],
                'created_at' => $this->order->created_at->toIso8601String(),
                'estimated_price' => $this->order->price,
            ],
            'available_drivers_count' => $this->availableDriversCount,
            'expires_at' => $this->order->expires_at ? $this->order->expires_at->toIso8601String() : null, // Fix date format
        ];
    }

    private function getAvailableDriversCount()
    {
        return \App\Models\Driver::where('is_active', true)
            ->where('is_verified', 1)
            ->whereDoesntHave('orders', function ($query) {
                $query->whereIn('order_status_id', [1, 2, 3, 4]);
            })
            ->count();
    }
}
