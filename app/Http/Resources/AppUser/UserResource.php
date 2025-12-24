<?php

namespace App\Http\Resources\AppUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'full_phone'   => $this->full_phone,
            'phone_number' => $this->phone_number,
            'country_code' => $this->country_code,
            'avatar'       => get_user_image($this->avatar),
        ];
    }
}
