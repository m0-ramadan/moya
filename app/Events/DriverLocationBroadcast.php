<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driverId;
    public $lat;
    public $lng;
    public $speed;
    public $heading;
    public $timestamp;

    public function __construct($driverId, $lat, $lng, $speed, $heading, $timestamp)
    {
        $this->driverId = $driverId;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->speed = $speed;
        $this->heading = $heading;
        $this->timestamp = $timestamp;
    }

    public function broadcastOn()
    {
        return new Channel('driver.' . $this->driverId . '.location');
    }

    public function broadcastAs()
    {
        return 'location.updated';
    }

    public function broadcastWith()
    {
        return [
            'driver_id' => $this->driverId,
            'location' => [
                'lat' => $this->lat,
                'lng' => $this->lng,
                'speed' => $this->speed,
                'heading' => $this->heading,
            ],
            'timestamp' => $this->timestamp,
        ];
    }
}
