<?php

namespace App\Http\Resources\WebsiteUser;

use App\Http\Resources\Driver\DriverShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $driver = $this->driverOrder ?? $this->driver;

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

            'driver' => $driver ? new DriverShortResource($driver) : null,

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
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'contract_ref' => 'moya_' . $this->id,
            'expires_at' => $this->expires_at,
        ];
    }
}
