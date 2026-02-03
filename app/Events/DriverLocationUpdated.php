<?php

namespace App\Events;

use App\Models\DriverLocation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $location;
    public $order;
    public $driver;

    public function __construct($driver, $order, $location)
    {
        $this->driver = $driver;
        $this->order = $order;
        $this->location = $location;
    }

    public function broadcastOn()
    {
        return [
            new Channel('order.' . $this->order->id . '.tracking'),
            new Channel('user.' . $this->order->user_id . '.tracking'),
        ];
    }

    public function broadcastAs()
    {
        return 'DriverLocationUpdated';
    }

    public function broadcastWith()
    {
        return [
            'location' => [
                'latitude' => $this->location->latitude,
                'longitude' => $this->location->longitude,
                'address' => $this->location->address,
                'speed' => $this->location->speed,
                'heading' => $this->location->heading,
                'is_moving' => $this->location->is_moving,
                'estimated_arrival_time' => $this->location->estimated_arrival_time,
                'distance_to_destination' => $this->location->distance_to_destination,
            ],
            'driver' => [
                'id' => $this->driver->id,
                'name' => $this->driver->user?->name,
                'vehicle' => $this->driver->vehicle->type ?? null,
                'plate_number' => $this->driver->vehicle->plate_number ?? null,
            ],
            'order_id' => $this->order->id,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
