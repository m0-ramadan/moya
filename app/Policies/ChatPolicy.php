<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;
use App\Models\Message;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Chat $chat)
    {
        return in_array($user->id, $chat->participants);
    }

    public function send(User $user, Chat $chat)
    {
        return in_array($user->id, $chat->participants);
    }
}

// app/Policies/MessagePolicy.php
class MessagePolicy
{
    public function delete(User $user, Message $message)
    {
        return $message->sender_id === $user->id
            && $message->sender_type === get_class($user);
    }
}
