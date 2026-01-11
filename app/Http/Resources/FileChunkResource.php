<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileChunkResource extends JsonResource
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
            'upload_id' => $this->upload_id,
            'chunk_number' => $this->chunk_number,
            'total_chunks' => $this->total_chunks,
            'original_name' => $this->original_name,
            'total_size' => $this->total_size,
            'total_size_formatted' => $this->formatSize($this->total_size),
            'current_size' => $this->file_size,
            'current_size_formatted' => $this->formatSize($this->file_size),
            'message_type' => $this->message_type,
            'mime_type' => $this->mime_type,
            'progress' => round(($this->chunk_number + 1) / $this->total_chunks * 100, 2),
            'status' => 'uploaded',

            // معلومات المستخدم
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user->name ?? null
            ],

            // معلومات المحادثة
            'chat' => [
                'id' => $this->chat_id,
                'uuid' => $this->chat->chat_uuid ?? null
            ],

            // التواريخ
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

            // روابط
            'links' => [
                'resume' => route('api.upload.resume', $this->upload_id),
                'cancel' => route('api.upload.cancel', $this->upload_id),
                'status' => route('api.upload.status', $this->upload_id)
            ]
        ];
    }

    /**
     * Format size
     */
    private function formatSize($bytes)
    {
        if ($bytes == 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }
}
