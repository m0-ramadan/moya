<?php

namespace App\Events\Order;

use App\Http\Resources\Driver\DriverShortResource;
use App\Models\OrderOffer;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class DriverAcceptedOrder implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public array $offerData;
    public int $orderId;
    public int $remainingDriversCount;

    public function __construct(OrderOffer $offer)
    {
        // تأكد من تحميل البيانات المطلوبة قبل serialization
        $offer->load(['driver.user', 'order']);

        $driver = $offer->driver;
        $user   = $driver?->user;

        if (!$driver || !$user || !$offer->order) {
            Log::warning('DriverAcceptedOrder: بيانات ناقصة، سيتم تخطي البث', [
                'offer_id' => $offer->id,
                'driver' => (bool) $driver,
                'user' => (bool) $user,
                'order' => (bool) $offer->order,
            ]);

            // بيانات فارغة لتجنب أي crash
            $this->offerData = [];
            $this->orderId = 0;
            $this->remainingDriversCount = 0;
            return;
        }

        // حفظ كل البيانات المهمة كـ array
        $this->offerData = [
            'id' => $offer->id,
            'driver_id' => $driver->id,
            'driver' => new DriverShortResource($driver),
            'driver_name' => $user->name,
            'driver_phone' => $user->phone,
            'price' => $offer->price,
            'delivery_duration_minutes' => $offer->delivery_duration_minutes,
            'created_at' => $offer->created_at->toDateTimeString(),
        ];

        $this->orderId = $offer->order_id;

        $this->remainingDriversCount = OrderOffer::where('order_id', $offer->order_id)
            ->where('id', '!=', $offer->id)
            ->where('status', 'pending')
            ->count();
    }

    public function broadcastOn()
    {
        if (!$this->orderId) {
            return [];
        }

        return new Channel('user.' . $this->orderId);
    }

    public function broadcastAs()
    {
        return 'DriverAcceptedOrder';
    }

    public function broadcastWith()
    {
        // إذا البيانات ناقصة يرجع array فارغ
        if (empty($this->offerData)) {
            return [];
        }

        return [
            'offer' => $this->offerData,
            'order_id' => $this->orderId,
            'remaining_drivers_count' => $this->remainingDriversCount,
        ];
    }
}
