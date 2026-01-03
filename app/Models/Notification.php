<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'title',
        'message',
        'type',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'read_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        if (is_null($this->read_at)) {
            $this->update([
                'is_read' => true,
                'read_at' => $this->freshTimestamp(),
            ]);

            return true;
        }

        return false;
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): bool
    {
        if (!is_null($this->read_at)) {
            $this->update([
                'is_read' => false,
                'read_at' => null,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return !$this->is_read;
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include notifications of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include notifications for a specific notifiable.
     */
    public function scopeForNotifiable($query, $notifiable)
    {
        return $query->where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id);
    }
}
