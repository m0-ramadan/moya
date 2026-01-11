<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RatingRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $attempt;
    public $result;
    public $timestamp;

    public function __construct(Order $order, $attempt, $result = null)
    {
        $this->order = $order;
        $this->attempt = $attempt;
        $this->result = $result;
        $this->timestamp = now();
    }

    public function broadcastOn()
    {
        return [
            new Channel('admin.ratings'),
            new Channel('order.' . $this->order->id . '.rating-request'),
        ];
    }

    public function broadcastAs()
    {
        return 'RatingRequested';
    }

    public function broadcastWith()
    {
        return [
            'order_id' => $this->order->id,
            'user_id' => $this->order->user_id,
            'driver_id' => $this->order->driver_id,
            'attempt_number' => $this->attempt,
            'status' => $this->result ? 'sent' : 'failed',
            'sent_at' => $this->timestamp->toDateTimeString(),
            'expires_at' => $this->timestamp->addHours(24)->toDateTimeString(),
            'metrics' => [
                'total_notifications_sent' => $this->attempt,
                'successful_deliveries' => $this->result['successful'] ?? 0,
                'failed_deliveries' => $this->result['failed'] ?? 0,
            ],
            'order_details' => [
                'order_number' => $this->order->id,
                'order_date' => $this->order->created_at->format('Y-m-d H:i'),
                'service_name' => $this->order->service->name ?? 'N/A',
                'total_price' => $this->order->price,
            ],
            'user_details' => [
                'name' => $this->order->user->name,
                'phone' => $this->order->user->phone,
                'total_orders' => $this->order->user->orders()->count(),
            ],
            'driver_details' => [
                'name' => $this->order->driver->full_name,
                'rating' => $this->order->driver->average_rating,
                'total_orders' => $this->order->driver->total_orders,
            ],
        ];
    }

    public function broadcastWhen()
    {
        // بث فقط للمسؤولين (يمكن تعديله حسب احتياجاتك)
        return app()->environment('local') || auth()->user()?->is_admin;
    }
}
