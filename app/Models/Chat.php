<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_uuid',
        'type',
        'participants',
        'last_message',
        'last_message_at'
    ];

    protected $casts = [
        'participants' => 'array',
        'last_message_at' => 'datetime'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getOtherParticipant($currentUserId)
    {
        $participants = collect($this->participants)
            ->filter(fn($id) => $id != $currentUserId);

        return $participants->first();
    }

    public function scopeBetweenParticipants($query, $participant1, $participant2)
    {
        return $query->whereJsonContains('participants', $participant1)
            ->whereJsonContains('participants', $participant2);
    }

    public function scopeForParticipant($query, $participantId)
    {
        return $query->whereJsonContains('participants', $participantId);
    }
}
