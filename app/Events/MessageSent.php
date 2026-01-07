<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $chat;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->chat = $message->chat;

        // Don't broadcast sensitive data
        $this->message->makeHidden(['deleted_at', 'updated_at']);
    }

    public function broadcastOn()
    {
        // استخدم Channel بدلاً من PresenceChannel
        return new Channel('chat.' . $this->chat->chat_uuid);

        // أو إذا كنت تريد presence channel:
        // return new PresenceChannel('presence-chat.' . $this->chat->chat_uuid);
    }

    public function broadcastAs()
    {
        // تأكد أن الاسم مطابق لما يتوقعه Flutter
        return 'MessageSent';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->load('sender'),
            'chat' => $this->chat
        ];
    }
}
