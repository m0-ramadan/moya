<?php

namespace App\Http\Resources\WebsiteUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->when($request->route()->getName() === 'articles.show', $this->content),
            'summary' => $this->summary,
            'featured_image' => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'reading_time' => $this->reading_time,
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            'shares_count' => $this->shares_count,
            'is_featured' => $this->is_featured,
            'is_sponsored' => $this->is_sponsored,
            'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            'published_at_human' => $this->published_at?->diffForHumans(),
            'author' => new UserResource($this->author),
            'category' => new ArticleCategoryResource($this->category),
            'tags' => $this->tags,
            'meta_description' => $this->meta_description,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            // 'links' => [
            //     'self' => route('articles.show', $this->slug),
            //     'comments' => route('articles.comments.index', $this->id),
            //     'like' => route('articles.like', $this->id),
            //     'share' => route('articles.share', $this->id)
            // ]
        ];
    }
}
