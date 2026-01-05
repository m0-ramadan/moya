<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $chat;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->chat = $message->chat;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('chat.' . $this->chat->chat_uuid);
    }

    public function broadcastAs()
    {
        return 'message.read';
    }

    public function broadcastWith()
    {
        return [
            'message_id' => $this->message->id,
            'read_at' => $this->message->read_at,
            'reader_id' => auth()->id()
        ];
    }
}
