<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->chat_uuid,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'participants' => $this->participants,
            'last_message' => $this->last_message,
            'last_message_at' => $this->last_message_at ? $this->last_message_at->toIso8601String() : null,
            'is_active' => $this->is_active,
            'unread_count' => $this->whenLoaded('unreadMessages', function () {
                return $this->unreadMessages->count();
            }, 0),

            // العلاقات
            'latest_message' => new MessageResource($this->whenLoaded('latestMessage')),
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'participant_details' => $this->whenLoaded('participantDetails', function () {
                return $this->participantDetails->map(function ($participant) {
                    return [
                        'id' => $participant->id,
                        'name' => $participant->name,
                        'avatar' => $participant->avatar,
                        'type' => class_basename($participant),
                        'online' => $participant->online_status ?? false
                    ];
                });
            }),

            // التواريخ
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

            // // روابط إضافية
            // 'links' => [
            //     'self' => route('api.chat.show', $this->id),
            //     'messages' => route('api.chat.messages', $this->id),
            //     'mark_read' => route('api.chat.mark-read', $this->id),
            // ]
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => [
                'version' => '1.0.0',
                'api_version' => 'v1',
                'timestamp' => now()->toIso8601String(),
                'authenticated_user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name
                ] : null
            ]
        ];
    }
}
