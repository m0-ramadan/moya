<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'name_urdu' => $this->name_urdu,

            'code' => $this->code,
            'dial_code' => $this->dial_code,
            'flag' => $this->flag_emoji,

            'is_active' => (bool) $this->is_active,
        ];
    }
}
