<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $oldStatus;
    public $newStatus;

    public function __construct(Order $order, $oldStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $order->order_status_id;
    }

    public function broadcastOn()
    {
        return [
            new Channel('order.' . $this->order->id),
            new Channel('user.' . $this->order->user_id),
        ];
    }

    public function broadcastAs()
    {
        return 'OrderStatusUpdated';
    }

    public function broadcastWith()
    {
        return [
            'order_id' => $this->order->id,
            'old_status_id' => $this->oldStatus,
            'new_status_id' => $this->newStatus,
            'new_status_name' => $this->order->status->name,
            'updated_at' => now()->toDateTimeString(),
        ];
    }
}
