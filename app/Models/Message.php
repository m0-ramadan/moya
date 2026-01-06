<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'sender_type',
        'message',
        'message_type',
        'file_url',
        'duration',
        'file_size',
        'file_name',
        'metadata',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'read_at' => 'datetime',
        'is_read' => 'boolean',
        'duration' => 'integer'
    ];


    public function isVoiceMessage()
    {
        return $this->message_type === 'voice';
    }

    public function isFileMessage()
    {
        return in_array($this->message_type, ['voice', 'image', 'file']);
    }

    public function getFileExtension()
    {
        if (!$this->file_name) return null;
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->morphTo();
    }

    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }
}
