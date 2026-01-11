<?php

namespace App\Events\Order;

use App\Models\OrderRating;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderRated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rating;
    public $order;
    public $ratedBy;
    public $targetType;

    public function __construct(OrderRating $rating)
    {
        $this->rating = $rating;
        $this->order = $rating->order;
        $this->ratedBy = $rating->rated_by;
        $this->targetType = $rating->rated_by === 'user' ? 'driver' : 'user';
    }

    public function broadcastOn()
    {
        $channels = [
            new Channel('order.' . $this->order->id . '.ratings'),
        ];

        // إضافة قناة للسائق إذا تم تقييمه
        if ($this->ratedBy === 'user') {
            $channels[] = new Channel('driver.' . $this->rating->driver_id . '.ratings');
        }

        // إضافة قناة للمستخدم إذا تم تقييمه
        if ($this->ratedBy === 'driver') {
            $channels[] = new Channel('user.' . $this->rating->user_id . '.ratings');
        }

        return $channels;
    }

    public function broadcastAs()
    {
        return 'OrderRated';
    }

    public function broadcastWith()
    {
        $response = [
            'rating_id' => $this->rating->id,
            'order_id' => $this->order->id,
            'rated_by' => $this->ratedBy,
            'target_type' => $this->targetType,
            'rating_value' => $this->rating->rating,
            'comment' => $this->rating->comment,
            'aspects' => $this->rating->aspects,
            'created_at' => $this->rating->created_at->toDateTimeString(),
            'notification' => $this->getNotificationMessage(),
        ];

        // إضافة معلومات إضافية حسب نوع التقييم
        if ($this->ratedBy === 'user') {
            $response['driver'] = [
                'id' => $this->rating->driver_id,
                'name' => $this->rating->driver->full_name,
                'new_average_rating' => $this->rating->driver->average_rating,
            ];
            $response['user'] = [
                'id' => $this->rating->user_id,
                'name' => $this->rating->user->name,
            ];
        } else {
            $response['user'] = [
                'id' => $this->rating->user_id,
                'name' => $this->rating->user->name,
                'new_driver_rating' => $this->rating->user->driver_rating,
            ];
            $response['driver'] = [
                'id' => $this->rating->driver_id,
                'name' => $this->rating->driver->full_name,
            ];
        }

        return $response;
    }

    private function getNotificationMessage()
    {
        if ($this->ratedBy === 'user') {
            return [
                'title' => 'تقييم جديد',
                'body' => 'قام ' . $this->rating->user->name . ' بتقييمك بـ ' . $this->rating->rating . ' نجوم',
                'type' => 'driver_rated',
            ];
        } else {
            return [
                'title' => 'تقييم جديد',
                'body' => 'قام السائق ' . $this->rating->driver->full_name . ' بتقييمك بـ ' . $this->rating->rating . ' نجوم',
                'type' => 'user_rated',
            ];
        }
    }

    public function broadcastWhen()
    {
        // فقط بث إذا كان التقييم جديداً (تم إنشاؤه حديثاً)
        return $this->rating->created_at->diffInSeconds(now()) < 10;
    }
}
