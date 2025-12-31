<?php

namespace App\Http\Resources\WebsiteUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleCategoryResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'articles_count' => $this->whenLoaded('articles', function () {
                return $this->articles_count;
            }),
            'is_active' => $this->is_active,
            'show_in_menu' => $this->show_in_menu,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            // 'links' => [
            //     'self' => route('categories.show', $this->slug),
            //     'articles' => route('categories.articles', $this->slug)
            // ]
        ];
    }
}
