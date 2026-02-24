<?php

namespace App\Http\Resources;

use App\Http\Resources\WebsiteUser\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverRatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'driver_id'  => $this->driver_id,
            'user_id'    => $this->user_id,
            'order_id'   => $this->order_id,
            'rating'     => (float) $this->rating,
            'comment'    => $this->comment,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),

            'user'  => new UserResource($this->user),
            'order' => $this->whenLoaded('order', fn() => [
                'id' => $this->order->id,
            ]),
        ];
    }
}
