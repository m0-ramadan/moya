<?php

namespace App\Http\Resources\WebsiteUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleCommentResource extends JsonResource
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
            'content' => $this->content,
            'status' => $this->status,
            'likes_count' => $this->likes_count,
            'replies_count' => $this->replies_count,
            'is_edited' => $this->is_edited,
            'is_guest' => $this->isGuest(),
            'user' => $this->when(!$this->isGuest(), new UserResource($this->user)),
            'guest_name' => $this->when($this->isGuest(), $this->guest_name),
            'guest_email' => $this->when($this->isGuest(), $this->guest_email),
            'replies' => ArticleCommentResource::collection($this->whenLoaded('replies')),
            // 'created_at' => $this->created_at->format('Y-m-d H:i:s'),
             'created_at_human' => $this->created_at->diffForHumans(),
            // 'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            // 'links' => [
            //     'self' => route('comments.show', $this->id),
            //     'like' => route('comments.like', $this->id),
            //     'reply' => route('comments.reply', $this->id)
            // ]
        ];
    }
}
