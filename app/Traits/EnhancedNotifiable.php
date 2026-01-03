<?php

namespace App\Traits;

use App\Models\Notification;

trait EnhancedNotifiable
{
    /**
     * Send a notification to database (not Laravel's default).
     */
    public function sendDatabaseNotification(array $notificationData): Notification
    {
        return $this->notifications()->create([
            'title' => $notificationData['title'] ?? null,
            'message' => $notificationData['message'] ?? null,
            'type' => $notificationData['type'] ?? 'info',
            'data' => $notificationData['data'] ?? [],
            'is_read' => false,
        ]);
    }

    /**
     * Get database notifications (custom).
     */
    public function databaseNotifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }

    /**
     * Get unread database notifications.
     */
    public function unreadDatabaseNotifications()
    {
        return $this->databaseNotifications()->unread();
    }

    /**
     * Get read database notifications.
     */
    public function readDatabaseNotifications()
    {
        return $this->databaseNotifications()->read();
    }

    /**
     * Mark all database notifications as read.
     */
    public function markAllDatabaseNotificationsAsRead(): int
    {
        return $this->unreadDatabaseNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Clear all database notifications.
     */
    public function clearDatabaseNotifications(): int
    {
        $count = $this->databaseNotifications()->count();
        $this->databaseNotifications()->delete();
        return $count;
    }

    /**
     * Check if user has unread database notifications.
     */
    public function hasUnreadDatabaseNotifications(): bool
    {
        return $this->unreadDatabaseNotifications()->exists();
    }
}
