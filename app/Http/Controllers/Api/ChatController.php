<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\Message;
use App\Events\MessageSent;
use App\Events\MessageRead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $chats = Chat::with(['messages' => function ($query) {
            $query->latest()->limit(1);
        }])
            ->forParticipant($userId)
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'chats' => $chats
        ]);
    }

    public function getOrCreateChat(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|string',
            'type' => 'required|in:user_user,user_driver,driver_driver'
        ]);

        $user = Auth::user();
        $participants = [$user->id, $request->participant_id];
        sort($participants);

        $chat = Chat::where('type', $request->type)
            ->whereJsonContains('participants', $participants[0])
            ->whereJsonContains('participants', $participants[1])
            ->first();

        if (!$chat) {
            $chat = Chat::create([
                'chat_uuid' => Str::uuid(),
                'type' => $request->type,
                'participants' => $participants,
                'last_message_at' => now()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'chat' => $chat->load('messages.sender')
        ]);
    }

    public function getMessages(Chat $chat)
    {
        $this->authorize('view', $chat);

        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Mark unread messages as read
        $chat->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        $this->authorize('send', $chat);

        $request->validate([
            'message' => 'required|string',
            'message_type' => 'in:text,image,location',
            'metadata' => 'nullable|array'
        ]);

        $message = $chat->messages()->create([
            'sender_id' => Auth::id(),
            'sender_type' => get_class(Auth::user()),
            'message' => $request->message,
            'message_type' => $request->message_type ?? 'text',
            'metadata' => $request->metadata
        ]);

        // Update chat last message
        $chat->update([
            'last_message' => Str::limit($request->message, 50),
            'last_message_at' => now()
        ]);

        // Broadcast event
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => $message->load('sender')
        ]);
    }

    public function markAsRead(Message $message)
    {
        $message->markAsRead();

        broadcast(new MessageRead($message))->toOthers();

        return response()->json(['status' => 'success']);
    }

    public function deleteMessage(Message $message)
    {
        $this->authorize('delete', $message);

        $message->delete();

        return response()->json(['status' => 'success']);
    }
}
