<?php

namespace App\Http\Resources\AppUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            //'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'image_url' => 'https://the-elitee.com/wp-content/uploads/2024/02/%D9%88%D8%A7%D9%8A%D8%AA-%D9%85%D8%A7%D8%A1-%D8%AD%D9%84%D9%88-%D8%A8%D8%A7%D9%84%D8%AF%D9%85%D8%A7%D9%85-%D9%88%D8%A7%D9%84%D8%AE%D8%A8%D8%B1-1-1024x576.webp',
            'link' => $this->link,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
