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
        return new PresenceChannel('chat.' . $this->chat->chat_uuid);
    }

    public function broadcastAs()
    {
        // return 'message.sent';
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
