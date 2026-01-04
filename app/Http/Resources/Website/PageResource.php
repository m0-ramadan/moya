<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'key'      => $this->key,
            'title'    => $this->title,
            'sections' => PageSectionResource::collection($this->sections),
        ];
    }
}
