<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\Message;
use App\Events\MessageRead;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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


    // public function sendMessage(Request $request, Chat $chat)
    // {
    //     $this->authorize('send', $chat);

    //     $request->validate([
    //         'message' => 'nullable|string|max:1000',
    //         'message_type' => 'required|in:text,image,voice,location,file',
    //         'file_url' => 'nullable|url',
    //         'duration' => 'nullable|integer|min:1|max:600',
    //         'file_size' => 'nullable|string',
    //         'file_name' => 'nullable|string',
    //         'metadata' => 'nullable|array'
    //     ]);

    //     $messageData = [
    //         'sender_id' => Auth::id(),
    //         'sender_type' => get_class(Auth::user()),
    //         'message' => $request->input('message', $this->getDefaultMessage($request->message_type)),
    //         'message_type' => $request->message_type,
    //         'metadata' => $request->metadata
    //     ];

    //     // Add file details for voice/image/file messages
    //     if (in_array($request->message_type, ['voice', 'image', 'file'])) {
    //         $messageData['file_url'] = $request->file_url;
    //         $messageData['file_name'] = $request->file_name;
    //         $messageData['file_size'] = $request->file_size;

    //         if ($request->message_type === 'voice') {
    //             $messageData['duration'] = $request->duration;
    //         }
    //     }

    //     $message = $chat->messages()->create($messageData);

    //     // Update chat last message
    //     $chat->update([
    //         'last_message' => $this->getLastMessagePreview($message),
    //         'last_message_at' => now()
    //     ]);

    //     // Broadcast event
    //     broadcast(new MessageSent($message))->toOthers();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => $message->load('sender')
    //     ]);
    // }


    public function sendMessage(Request $request, Chat $chat)
    {
        $this->authorize('send', $chat);

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'message_type' => 'required|in:text,image,voice,location,file',
            'file_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:1|max:600',
            'file_size' => 'nullable|string',
            'file_name' => 'nullable|string',
            'metadata' => 'nullable|array'
        ]);

        $messageData = [
            'sender_id' => Auth::id(),
            'sender_type' => get_class(Auth::user()),
            'message' => $request->input('message', $this->getDefaultMessage($request->message_type)),
            'message_type' => $request->message_type,
            'metadata' => $request->metadata
        ];

        // Add file details for voice/image/file messages
        if (in_array($request->message_type, ['voice', 'image', 'file'])) {
            $messageData['file_url'] = $request->file_url;
            $messageData['file_name'] = $request->file_name;
            $messageData['file_size'] = $request->file_size;

            if ($request->message_type === 'voice') {
                $messageData['duration'] = $request->duration;
            }
        }

        $message = $chat->messages()->create($messageData);

        // Update chat last message
        $chat->update([
            'last_message' => $this->getLastMessagePreview($message),
            'last_message_at' => now()
        ]);

        // Log before broadcasting
        Log::info('Attempting to broadcast message', [
            'message_id' => $message->id,
            'chat_uuid' => $chat->chat_uuid,
            'channel' => 'chat.' . $chat->chat_uuid,
            'event' => 'MessageSent'
        ]);

        // Broadcast event
        broadcast(new MessageSent($message))->toOthers();

        Log::info('Broadcast event fired', [
            'message_id' => $message->id,
            'chat_uuid' => $chat->chat_uuid
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $message->load('sender'),
            'broadcast_channel' => 'chat.' . $chat->chat_uuid, // إرجاع اسم القناة للتdebug
            'broadcast_event' => 'MessageSent'
        ]);
    }
    private function getDefaultMessage($messageType)
    {
        return match ($messageType) {
            'voice' => '🎤 Voice message',
            'image' => '📷 Photo',
            'file' => '📄 File',
            'location' => '📍 Location',
            default => ''
        };
    }

    private function getLastMessagePreview(Message $message)
    {
        return match ($message->message_type) {
            'voice' => '🎤 Voice message',
            'image' => '📷 Photo',
            'file' => '📄 ' . $message->file_name,
            'location' => '📍 Location shared',
            default => Str::limit($message->message, 50)
        };
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
