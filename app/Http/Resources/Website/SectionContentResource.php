<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'key'   => $this->key,
            'value' => $this->value,
        ];
    }
}
