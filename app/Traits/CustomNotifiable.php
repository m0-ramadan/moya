<?php

namespace App\Traits;

use App\Models\Notification;

trait CustomNotifiable
{
    /**
     * Get all notifications for this model.
     */
    public function customNotifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->latest();
    }

    /**
     * Get unread notifications.
     */
    public function customUnreadNotifications()
    {
        return $this->customNotifications()->unread();
    }

    /**
     * Get read notifications.
     */
    public function customReadNotifications()
    {
        return $this->customNotifications()->read();
    }

    /**
     * Send a notification.
     */
    public function createNotification(array $notificationData): Notification
    {
        return $this->customNotifications()->create([
            'title' => $notificationData['title'] ?? null,
            'message' => $notificationData['message'] ?? null,
            'type' => $notificationData['type'] ?? 'info',
            'data' => $notificationData['data'] ?? [],
            'is_read' => false,
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead()
    {
        return $this->customUnreadNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Clear all notifications (delete them).
     */
    public function clearCustomNotifications(): int
    {
        $count = $this->customNotifications()->count();
        $this->customNotifications()->delete();
        return $count;
    }

    /**
     * Get latest notifications with pagination.
     */
    public function getLatestNotifications(int $limit = 10)
    {
        return $this->customNotifications()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get notifications by type.
     */
    public function getNotificationsByType(string $type)
    {
        return $this->customNotifications()
            ->where('type', $type)
            ->get();
    }

    /**
     * Check if user has unread notifications.
     */
    public function hasUnreadCustomNotifications(): bool
    {
        return $this->customUnreadNotifications()->exists();
    }

    /**
     * Get notifications from the last X days.
     */
    public function recentCustomNotifications(int $days = 7)
    {
        return $this->customNotifications()
            ->where('created_at', '>=', now()->subDays($days))
            ->get();
    }

    /**
     * Get unread notifications count by type.
     */
    public function unreadCountByType(string $type): int
    {
        return $this->customUnreadNotifications()
            ->where('type', $type)
            ->count();
    }

    /**
     * Mark notifications of a specific type as read.
     */
    public function markTypeAsRead(string $type): int
    {
        return $this->customUnreadNotifications()
            ->where('type', $type)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get total notifications count.
     */
    public function totalNotificationsCount(): int
    {
        return $this->customNotifications()->count();
    }
}
