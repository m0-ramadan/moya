<?php

namespace App\Http\Resources\WebsiteUser;

use App\Http\Resources\Driver\DriverShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,

            'service' => [
                'id'   => $this->service->id,
                'name' => $this->service->name,
                'image' => $this->service->image,
            ],

            'water_type' => $this->waterType ? [
                'id'   => $this->waterType->id,
                'name' => $this->waterType->name,
                'image' => $this->waterType->image,
            ] : null,

            'location' => $this->location ? [
                'id'        => $this->location->id,
                'name'      => $this->location->name,
                'address'   => $this->location->address,
                'latitude'  => $this->location->latitude,
                'longitude' => $this->location->longitude,
            ] : null,

            'status' => $this->status ? [
                'id'    => $this->status->id,
                'name'  => $this->status->name,
                'label' => $this->status->label,
            ] : null,

            'driver' => $this->driverOrder ? new DriverShortResource($this->driverOrder) : null,


            'user' => $this->user ? [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'phone' => $this->user->full_phone,
                'image' => get_user_image($this->user->avatar),

            ] : null,

            'price' => optional($this->acceptedOffer)->price,

            'order_date' => $this->order_date,

            'code_confirmation' => $this->code_confirmation,

            'payment_status' => $this->payment_status,

            'created_at' => $this->created_at->format('Y-m-d H:i'),

            'expires_at' => $this->expires_at,
        ];
    }
}
