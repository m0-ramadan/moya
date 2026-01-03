<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the notification.
     */
    public function view(User $user, Notification $notification): bool
    {
        return $notification->notifiable_id === $user->id
            && $notification->notifiable_type === get_class($user);
    }

    /**
     * Determine if the user can update the notification.
     */
    public function update(User $user, Notification $notification): bool
    {
        return $notification->notifiable_id === $user->id
            && $notification->notifiable_type === get_class($user);
    }

    /**
     * Determine if the user can delete the notification.
     */
    public function delete(User $user, Notification $notification): bool
    {
        return $notification->notifiable_id === $user->id
            && $notification->notifiable_type === get_class($user);
    }
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    public function send(User $user): bool
    {
        return $user->hasPermission('send_notifications');
    }

    public function sendToUser(User $user, User $targetUser): bool
    {
        // يمكن للمستخدم إرسال إشعارات لنفسه فقط إلا إذا كان مدير
        return $user->id === $targetUser->id || $user->hasPermission('send_notifications');
    }
}
