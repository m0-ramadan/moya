<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'chat_id' => $this->chat_id,
            'chat_uuid' => $this->whenLoaded('chat', function () {
                return $this->chat->chat_uuid;
            }),

            // معلومات المرسل
            'sender' => $this->when($this->relationLoaded('sender'), function () {
                return [
                    'id' => $this->sender->id,
                    'name' => $this->sender->name,
                    'avatar' => $this->sender->avatar,
                    'type' => class_basename($this->sender)
                ];
            }),
            'sender_id' => $this->sender_id,
            'sender_type' => $this->sender_type,

            // محتوى الرسالة
            'message' => $this->message,
            'message_type' => $this->message_type,
            'message_type_label' => $this->getMessageTypeLabel(),

            // معلومات الملف (إذا كانت رسالة ملف)
            'file_info' => $this->when($this->isFileMessage(), function () {
                return [
                    'url' => $this->file_url,
                    'name' => $this->file_name,
                    'size' => $this->formatFileSize($this->file_size),
                    'size_bytes' => $this->file_size,
                    'extension' => $this->getFileExtension(),
                    'is_voice' => $this->isVoiceMessage(),
                    'duration' => $this->duration,
                    'duration_formatted' => $this->duration ? $this->formatDuration($this->duration) : null
                ];
            }),

            // معلومات الموقع (إذا كانت رسالة موقع)
            'location_info' => $this->when($this->message_type === 'location', function () {
                return $this->metadata ?: [];
            }),

            // metadata
            'metadata' => $this->metadata,

            // حالة الرسالة
            'is_read' => $this->is_read,
            'read_at' => $this->read_at ? $this->read_at->toIso8601String() : null,
            'is_mine' => $request->user() ? $this->sender_id == $request->user()->id : false,
            'is_deleted' => (bool) $this->deleted_at,

            // التواريخ
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'deleted_at' => $this->deleted_at ? $this->deleted_at->toIso8601String() : null,

            // روابط إضافية
            'links' => [
                'self' => route('api.messages.show', $this->id),
                'mark_read' => route('api.messages.mark-read', $this->id),
                'delete' => route('api.messages.destroy', $this->id),
                'download' => $this->file_url ? route('api.messages.download', $this->id) : null
            ],

            // ردود الأفعال (Reactions) - إذا كان لديك نظام ردود أفعال
            'reactions' => $this->whenLoaded('reactions', function () {
                return $this->reactions->groupBy('reaction')->map(function ($group) {
                    return [
                        'reaction' => $group->first()->reaction,
                        'count' => $group->count(),
                        'users' => $group->map(function ($reaction) {
                            return [
                                'id' => $reaction->user->id,
                                'name' => $reaction->user->name
                            ];
                        })
                    ];
                })->values();
            })
        ];
    }

    /**
     * Get message type label
     */
    private function getMessageTypeLabel()
    {
        return match ($this->message_type) {
            'text' => 'رسالة نصية',
            'image' => 'صورة',
            'voice' => 'رسالة صوتية',
            'file' => 'ملف',
            'location' => 'موقع',
            default => $this->message_type
        };
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes == 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    /**
     * Format duration (seconds to MM:SS)
     */
    private function formatDuration($seconds)
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
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
                'current_time' => now()->toIso8601String(),
                'message_status' => [
                    'can_delete' => $request->user() ? $this->sender_id == $request->user()->id : false,
                    'can_edit' => $request->user() ? $this->sender_id == $request->user()->id && $this->created_at->diffInMinutes(now()) < 5 : false,
                    'can_react' => true
                ]
            ]
        ];
    }
}
