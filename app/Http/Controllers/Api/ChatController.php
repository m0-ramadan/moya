<?php

namespace App\Http\Controllers\Api;

use getID3;
use App\Models\Chat;
use App\Models\Message;
use App\Models\FileChunk;
use App\Events\MessageRead;
use App\Events\MessageSent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            ->orderBy('last_message_at', 'asc')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'chats' => $chats
        ]);
    }

    public function getOrCreateChat(Request $request)
    {
        $request->validate([
            'participant_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->orWhere(function ($query) {
                    $query->from('admins');
                })
            ],
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

    /**
     * إرسال رسالة نصية
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        $this->authorize('send', $chat);

        $request->validate([
            'message' => 'nullable|string',
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
            'broadcast_channel' => 'chat.' . $chat->chat_uuid,
            'broadcast_event' => 'MessageSent'
        ]);
    }

    /**
     * رفع جزء من ملف (Chunk Upload)
     */
    public function uploadChunk(Request $request, Chat $chat)
    {
        $this->authorize('send', $chat);

        $request->validate([
            'file' => 'required|file',
            'chunk_number' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
            'original_name' => 'required|string',
            'total_size' => 'required|integer|min:1',
            'upload_id' => 'required|string|size:32', // MD5 hash of original name + timestamp
            'message_type' => 'required|in:image,voice,file'
        ]);

        $user = Auth::user();
        $chunk = $request->file('file');
        $uploadId = $request->upload_id;

        // Create upload directory if not exists
        $uploadPath = "chunks/{$uploadId}";
        Storage::disk('local')->makeDirectory($uploadPath);

        // Save chunk
        $chunkFileName = "chunk_{$request->chunk_number}.part";
        $chunk->storeAs($uploadPath, $chunkFileName, 'local');

        // Save chunk info to database
        FileChunk::updateOrCreate(
            [
                'upload_id' => $uploadId,
                'chunk_number' => $request->chunk_number,
                'user_id' => $user->id
            ],
            [
                'original_name' => $request->original_name,
                'total_chunks' => $request->total_chunks,
                'total_size' => $request->total_size,
                'message_type' => $request->message_type,
                'chat_id' => $chat->id,
                'file_size' => $chunk->getSize(),
                'mime_type' => $chunk->getMimeType(),
            ]
        );

        // Check if all chunks are uploaded
        $uploadedChunks = FileChunk::where('upload_id', $uploadId)->count();
        $isComplete = $uploadedChunks == $request->total_chunks;

        if ($isComplete) {
            // Combine all chunks
            return $this->combineChunks($uploadId, $chat, $request);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Chunk uploaded successfully',
            'upload_id' => $uploadId,
            'chunk_number' => $request->chunk_number,
            'uploaded_chunks' => $uploadedChunks,
            'total_chunks' => $request->total_chunks,
            'is_complete' => false
        ]);
    }

    /**
     * دمج جميع الأجزاء وإنشاء ملف كامل
     */
    private function combineChunks($uploadId, $chat, $request)
    {
        try {
            $chunks = FileChunk::where('upload_id', $uploadId)
                ->orderBy('chunk_number')
                ->get();

            if ($chunks->isEmpty()) {
                throw new \Exception('No chunks found');
            }

            $firstChunk = $chunks->first();
            $originalName = $firstChunk->original_name;
            $messageType = $firstChunk->message_type;

            // Generate unique filename
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $fileName = Str::uuid() . '.' . $extension;

            // Final file path
            $folder = 'chat_media/' . $chat->id . '/' . date('Y/m');
            $finalPath = "{$folder}/{$fileName}";

            // Create directory if not exists
            Storage::disk('public')->makeDirectory($folder);

            // Open final file for writing
            $finalFilePath = Storage::disk('public')->path($finalPath);
            $finalFile = fopen($finalFilePath, 'wb');

            if (!$finalFile) {
                throw new \Exception('Cannot create final file');
            }

            // Combine all chunks
            foreach ($chunks as $chunk) {
                $chunkPath = Storage::disk('local')->path("chunks/{$uploadId}/chunk_{$chunk->chunk_number}.part");

                if (file_exists($chunkPath)) {
                    $chunkContent = file_get_contents($chunkPath);
                    fwrite($finalFile, $chunkContent);
                }
            }

            fclose($finalFile);

            // Calculate total file size
            $totalSize = filesize($finalFilePath);

            // Get file duration for voice messages
            $duration = null;
            if ($messageType === 'voice') {
                $duration = $this->getAudioDurationFromFile($finalFilePath);
            }

            // Create file URL
            $fileUrl = asset('storage/' . $finalPath);

            // Create message
            $messageData = [
                'sender_id' => Auth::id(),
                'sender_type' => get_class(Auth::user()),
                'message' => $this->getDefaultMessage($messageType),
                'message_type' => $messageType,
                'file_url' => $fileUrl,
                'file_name' => $originalName,
                'file_size' => $totalSize,
                'duration' => $duration
            ];

            $message = $chat->messages()->create($messageData);

            // Update chat last message
            $chat->update([
                'last_message' => $this->getLastMessagePreview($message),
                'last_message_at' => now()
            ]);

            // Cleanup chunks
            $this->cleanupChunks($uploadId);

            // Broadcast event
            broadcast(new MessageSent($message))->toOthers();

            Log::info('File combined and message created', [
                'upload_id' => $uploadId,
                'message_id' => $message->id,
                'file_size' => $totalSize
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded and combined successfully',
                'data' => [
                    'message' => $message->load('sender'),
                    'file_url' => $fileUrl,
                    'file_name' => $originalName,
                    'file_size' => $totalSize,
                    'is_complete' => true
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error combining chunks: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to combine chunks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تنظيف الأجزاء المؤقتة
     */
    private function cleanupChunks($uploadId)
    {
        try {
            // Delete chunks from storage
            $chunkPath = "chunks/{$uploadId}";
            if (Storage::disk('local')->exists($chunkPath)) {
                Storage::disk('local')->deleteDirectory($chunkPath);
            }

            // Delete from database
            FileChunk::where('upload_id', $uploadId)->delete();
        } catch (\Exception $e) {
            Log::error('Error cleaning up chunks: ' . $e->getMessage());
        }
    }

    /**
     * التحقق من حالة الرفع
     */
    public function checkUploadStatus(Request $request)
    {
        $request->validate([
            'upload_id' => 'required|string'
        ]);

        $uploadId = $request->upload_id;
        $user = Auth::user();

        $chunks = FileChunk::where('upload_id', $uploadId)
            ->where('user_id', $user->id)
            ->get();

        if ($chunks->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'upload_id' => $uploadId,
                'exists' => false
            ]);
        }

        $firstChunk = $chunks->first();
        $uploadedChunks = $chunks->pluck('chunk_number')->toArray();

        return response()->json([
            'status' => 'success',
            'upload_id' => $uploadId,
            'exists' => true,
            'original_name' => $firstChunk->original_name,
            'total_chunks' => $firstChunk->total_chunks,
            'total_size' => $firstChunk->total_size,
            'message_type' => $firstChunk->message_type,
            'uploaded_chunks' => $uploadedChunks,
            'uploaded_count' => count($uploadedChunks),
            'is_complete' => count($uploadedChunks) == $firstChunk->total_chunks
        ]);
    }

    /**
     * دالة مساعدة للحصول على مدة الصوت
     */
    private function getAudioDurationFromFile($filePath)
    {
        try {
            if (!file_exists($filePath)) {
                return null;
            }

            $getID3 = new getID3();
            $fileInfo = $getID3->analyze($filePath);

            if (isset($fileInfo['playtime_seconds'])) {
                return (int) ceil($fileInfo['playtime_seconds']);
            }
        } catch (\Exception $e) {
            Log::error('Error getting audio duration: ' . $e->getMessage());
        }

        return null;
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
