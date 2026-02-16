<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Http\Resources\Driver\OrderResource as DriverOrderResource;

class TripStartedForDriver implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public array $orderData;

    public int $driverId;

    public function __construct(Order $order)
    {
        Log::info('TripStartedForDriver fired', ['order_id' => $order->id]);

       // $order->load(['driver.user']);

        $driver = $order->driverOrder;

        if (! $driver) {
            Log::warning('TripStartedForDriver: لا يوجد سائق للطلب', [
                'order_id' => $order->id,
            ]);

            $this->orderData = [];
            $this->driverId = 0;
            return;
        }

        $this->driverId = $driver->id;

        $this->orderData = [
            'order' => new DriverOrderResource($order),
            'message' => 'تم الدفع والرحلة بدأت الآن',
            'paid_at' => $order->paid_at?->toDateTimeString(),
        ];
    }

    public function broadcastOn()
    {
        if (! $this->driverId) {
            return [];
        }

        return new Channel('driver.' . $this->driverId);
    }

    public function broadcastAs()
    {
        return 'TripStartedForDriver';
    }

    public function broadcastWith()
    {
        if (empty($this->orderData)) {
            return [];
        }

        return $this->orderData;
    }
}
