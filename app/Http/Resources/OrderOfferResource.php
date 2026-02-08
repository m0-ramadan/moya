<?php

namespace App\Http\Resources;

use App\Http\Resources\WebsiteUser\OrderResource;
use App\Models\Driver;
use App\Http\Resources\Driver\DriverResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Driver\DriverShortResource;

class OrderOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $driver = Driver::findOrFail($this->driver_id);

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order' => new OrderResource($this->order),
            'driver_id' => $this->driver_id,
            'price' => $this->price,
            'status' => $this->status,
            'delivery_duration_minutes' => $this->delivery_duration_minutes,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            // Include related models if needed
            'driver' => new DriverShortResource($driver),
        ];
    }
}
