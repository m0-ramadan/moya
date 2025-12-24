<?php

namespace App\Http\Resources\AppUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            //'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'image_url' => 'https://i.ibb.co/HfQygsxR/Whats-App-Image-2025-12-25-at-12-09-56-AM.jpg',
            'is_active' => $this->is_active,
        ];
    }
}
