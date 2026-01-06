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


    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    public function unreadMessages()
    {
        return $this->hasMany(Message::class)->where('is_read', false);
    }

    // علاقة للحصول على معلومات المشاركين
    public function participantDetails()
    {
        return $this->hasManyThrough(
            User::class,
            Driver::class,
            'id',
            'id',
            'participants',
            'participants'
        );
    }

    // دالة للحصول على تسمية النوع
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'user_user' => 'مستخدم - مستخدم',
            'user_driver' => 'مستخدم - سائق',
            'driver_driver' => 'سائق - سائق',
            default => $this->type
        };
    }

    // دالة للتحقق إذا كانت المحادثة نشطة (آخر رسالة قبل أقل من 10 دقائق)
    public function getIsActiveAttribute()
    {
        if (!$this->last_message_at) {
            return false;
        }

        return $this->last_message_at->diffInMinutes(now()) < 10;
    }
}
