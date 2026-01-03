<?php

namespace App\Http\Resources\WebsiteUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class OrderOfferResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'price' => (float) $this->price,
            'estimated_time' => $this->estimated_time,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // العلاقات
            'driver' => new UserResource($this->whenLoaded('driver')),
            'order' => new OrderResource($this->whenLoaded('order')),
        ];
    }
}
