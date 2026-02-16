<?php

namespace App\Events\Order;

use App\Http\Resources\WebsiteUser\OrderResource;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Log;

class TripStartedForUser implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public array $orderData;

    public int $userId;

    public int $orderId;

    public function __construct(Order $order)
    {
        Log::info('TripStartedForUser fired', ['order_id' => $order->id]);
        //$order->load(['driver.user']);
        $this->orderId = $order->id;
        $driver = $order->driverOrder;

        if (! $driver) {
            Log::warning('TripStartedForUser: لا يوجد سائق للطلب', [
                'order_id' => $order->id,
            ]);

            $this->orderData = [];
            $this->userId = 0;

            return;
        }

        $this->userId = $driver->id;

        $this->orderData = [
            'order' => new OrderResource($order),
            'message' => 'تم الدفع والرحلة بدأت الآن',
            'paid_at' => $order->paid_at?->toDateTimeString(),
        ];
    }

    public function broadcastOn()
    {
        if (! $this->userId) {
            return [];
        }

        return new Channel('order.'.$this->orderId);
    }

    public function broadcastAs()
    {
        return 'TripStartedForUser';
    }

    public function broadcastWith()
    {
        if (empty($this->orderData)) {
            return [];
        }

        return $this->orderData;
    }
}
