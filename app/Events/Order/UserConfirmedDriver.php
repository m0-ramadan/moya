<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\OrderOffer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserConfirmedDriver implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $confirmedDriverId;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->confirmedDriverId = $order->acceptedOffer->driver_id ?? null;
    }

    public function broadcastOn()
    {
        // للـ Driver المختار
        $channels = [new Channel('driver.' . $this->confirmedDriverId)];

        // لكل السائقين الذين تقدموا ولم يتم اختيارهم
        $otherDrivers = OrderOffer::where('order_id', $this->order->id)
            ->where('driver_id', '!=', $this->confirmedDriverId)
            ->pluck('driver_id');

        foreach ($otherDrivers as $driverId) {
            $channels[] = new Channel('driver.' . $driverId);
        }

        return $channels;
    }

    public function broadcastAs()
    {
        return 'UserConfirmedDriver';
    }

    public function broadcastWith()
    {
        $isConfirmed = $this->order->driver_id == $this->confirmedDriverId;

        return [
            'order_id' => $this->order->id,
            'status' => $isConfirmed ? 'confirmed' : 'not_selected',
            'message' => $isConfirmed
                ? 'تم تأكيدك للطلب! ابدأ بالتوصيل الآن.'
                : 'تم اختيار سائق آخر لهذا الطلب.',
            'user_phone' => $isConfirmed ? $this->order->user->phone : null,
            'user_location' => $isConfirmed ? [
                'address' => $this->order->location->address_details,
                'latitude' => $this->order->location->latitude,
                'longitude' => $this->order->location->longitude,
            ] : null,
            'delivery_timer_start' => $isConfirmed ? now()->toDateTimeString() : null,
        ];
    }
}
