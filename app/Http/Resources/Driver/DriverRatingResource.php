<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\WebsiteUser\UserResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class DriverRatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            // 'driver' => [
            //     'id' => $this->driver->id,
            //     'name' => $this->driver->name,
            //     // أي بيانات إضافية للـ driver
            // ],
            'user' => new UserResource($this->user),
            'order_id' => $this->order_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
