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
}
