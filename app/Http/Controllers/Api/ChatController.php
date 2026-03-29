<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Driver;
use App\Models\FileChunk;
use App\Models\Message;
use getID3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = (int) $user->id;

        $chats = Chat::with(['messages' => function ($query) {
            $query->latest()->limit(1);
        }])
            ->forParticipant($userId)
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        // جمع IDs للطرف الآخر (في user_user فقط)
        $otherUserIds = collect($chats->items())
            ->filter(fn($chat) => $chat->type === 'user_user')
            ->map(function ($chat) use ($userId) {
                $participants = collect($chat->participants ?? [])
                    ->map(fn($id) => (int) $id); // مهم جدًا

                return $participants->first(fn($id) => $id !== $userId);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $otherUsers = \App\Models\User::query()
            ->whereIn('id', $otherUserIds)
            ->get(['id', 'name', 'avatar'])
            ->keyBy('id');

        // إضافة بيانات الطرف الآخر لكل Chat
        $chats->getCollection()->transform(function ($chat) use ($userId, $otherUsers) {
            $participants = collect($chat->participants ?? [])
                ->map(fn($id) => (int) $id);

            $otherId = $participants->first(fn($id) => $id !== $userId);

            $chat->other_participant = null;

            if ($chat->type === 'user_user' && $otherId) {
                $u = $otherUsers->get($otherId);

                $chat->other_participant = $u ? [
                    'id' => $u->id,
                    'name' => $u->name,
                    'avatar' => $u->avatar ? asset('storage/' . ltrim($u->avatar, '/')) : null,
                ] : null;
            }

            return $chat;
        });

        return response()->json([
            'status' => 'success',
            'chats' => $chats,
        ]);
    }

    public function getOrCreateChat(Request $request)
    {
        $messages = [
            'participant_id.required' => 'المرسل اليه مطلوب',
            'participant_id.integer' => 'المرسل اليه لازم يكون رقم صحيح',
            'participant_id.exists' => 'المرسل اليه غير موجود',
            'type.required' => 'نوع الدردشة مطلوب',
            'type.in' => 'نوع الدردشة غير مسموح',
        ];

        $validated = $request->validate([
            'participant_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $existsInUsers = \App\Models\User::where('id', $value)->exists();
                    $existsInAdmins = \App\Models\Admin::where('id', $value)->exists();

                    if (! $existsInUsers && ! $existsInAdmins) {
                        $fail('المستخدم غير موجود.');
                    }
                },
            ],
            'type' => 'required|in:user_user,user_driver,driver_driver',
        ], $messages);

        $user = Auth::user();
        $participantId = (int) $validated['participant_id'];

        // if (!Driver::where('user_id', $participantId)->exists() && !$user->type == 'user') {
        //     $participantId = (int) ('888888' . $participantId);
        // }
        $participants = [$user->id, $participantId];
        sort($participants);

        $chat = Chat::where('type', $validated['type'])
            ->whereJsonContains('participants', $participants[0])
            ->whereJsonContains('participants', $participants[1])
            ->first();

        if (! $chat) {
            $chat = Chat::create([
                'chat_uuid' => Str::uuid(),
                'type' => $request->type,
                'participants' => $participants,
                'last_message_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'chat' => $chat->load('messages.sender'),
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
                'read_at' => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
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
            'file' => 'nullable|max:102400',
            'duration' => 'nullable|integer|min:1|max:600',
            'file_size' => 'nullable|string',
            'file_name' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $messageData = [
            'sender_id' => Auth::id(),
            'sender_type' => get_class(Auth::user()),
            'message' => $request->input('message', $this->getDefaultMessage($request->message_type)),
            'message_type' => $request->message_type,
            'metadata' => $request->metadata,
        ];

        if (in_array($request->message_type, ['voice', 'image', 'file'])) {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $mime = $file->getMimeType();

                if ($request->message_type === 'voice') {
                    $extension = match ($mime) {
                        'audio/mpeg', 'audio/mp3' => 'mp3',
                        'audio/wav', 'audio/x-wav' => 'wav',
                        'audio/webm' => 'webm',
                        'audio/ogg' => 'ogg',
                        'audio/aac' => 'aac',
                        'audio/x-m4a' => 'm4a',
                        'audio/mp4', 'video/mp4' => 'm4a',
                        default => $file->getClientOriginalExtension() ?: 'audio',
                    };

                    $fileName = uniqid('voice_') . '.' . $extension;
                    $path = $file->storeAs('chat_media/' . $chat->id, $fileName, 'public');
                } else {
                    $path = $file->store('chat_media/' . $chat->id, 'public');
                }

                $messageData['file_url'] = asset('storage/' . $path);
                $messageData['file_name'] = $fileName ?? $file->getClientOriginalName();
                $messageData['file_size'] = $file->getSize();
                $messageData['metadata'] = array_merge($request->metadata ?? [], [
                    'mime_type' => $mime,
                ]);
            } else {
                $messageData['file_url'] = $request->file_url;
                $messageData['file_name'] = $request->file_name;
                $messageData['file_size'] = $request->file_size;
            }

            if ($request->message_type === 'voice') {
                $messageData['duration'] = $request->duration;
            }
        }

        $message = $chat->messages()->create($messageData);

        $chat->update([
            'last_message' => $this->getLastMessagePreview($message),
            'last_message_at' => now(),
        ]);

        Log::info('Attempting to broadcast message', [
            'message_id' => $message->id,
            'chat_uuid' => $chat->chat_uuid,
            'channel' => 'chat.' . $chat->chat_uuid,
            'event' => 'MessageSent',
        ]);

        broadcast(new MessageSent($message))->toOthers();

        Log::info('Broadcast event fired', [
            'message_id' => $message->id,
            'chat_uuid' => $chat->chat_uuid,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $message->load('sender'),
            'broadcast_channel' => 'chat.' . $chat->chat_uuid,
            'broadcast_event' => 'MessageSent',
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
            'message_type' => 'required|in:image,voice,file',
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
                'user_id' => $user->id,
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
            'is_complete' => false,
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

            if (! $finalFile) {
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
                'duration' => $duration,
            ];

            $message = $chat->messages()->create($messageData);

            // Update chat last message
            $chat->update([
                'last_message' => $this->getLastMessagePreview($message),
                'last_message_at' => now(),
            ]);

            // Cleanup chunks
            $this->cleanupChunks($uploadId);

            // Broadcast event
            broadcast(new MessageSent($message))->toOthers();

            Log::info('File combined and message created', [
                'upload_id' => $uploadId,
                'message_id' => $message->id,
                'file_size' => $totalSize,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded and combined successfully',
                'data' => [
                    'message' => $message->load('sender'),
                    'file_url' => $fileUrl,
                    'file_name' => $originalName,
                    'file_size' => $totalSize,
                    'is_complete' => true,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error combining chunks: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to combine chunks: ' . $e->getMessage(),
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
            'upload_id' => 'required|string',
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
                'exists' => false,
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
            'is_complete' => count($uploadedChunks) == $firstChunk->total_chunks,
        ]);
    }

    /**
     * دالة مساعدة للحصول على مدة الصوت
     */
    private function getAudioDurationFromFile($filePath)
    {
        try {
            if (! file_exists($filePath)) {
                return null;
            }

            $getID3 = new getID3;
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
