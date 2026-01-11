<?php

namespace App\Http\Resources\WebsiteUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->full_phone,
            'avatar'       => get_user_image($this->avatar),
            // 'role' => $this->whenLoaded('roles', function () {
            //     return $this->roles->first()?->name;
            // }),
            // 'is_verified' => (bool) $this->phone_verified_at,
            'allow_notifications' => (bool) $this->allow_notifications,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
