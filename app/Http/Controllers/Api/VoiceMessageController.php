<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use App\Models\Chat;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VoiceMessageController extends Controller
{
    private $allowedMimeTypes = [
        'audio/mpeg' => 'mp3',
        'audio/wav' => 'wav',
        'audio/ogg' => 'ogg',
        'audio/aac' => 'aac',
        'audio/x-m4a' => 'm4a',
        'audio/webm' => 'webm'
    ];

    private $maxFileSize = 10240; // 10MB in KB

    public function uploadVoiceMessage(Request $request, Chat $chat)
    {
        $this->authorize('send', $chat);

        $request->validate([
            'voice' => 'required|file|max:' . $this->maxFileSize,
            'duration' => 'required|integer|min:1|max:600', // Max 10 minutes
            'message' => 'nullable|string|max:500'
        ]);

        try {
            // Validate audio file
            $audioFile = $request->file('voice');
            $mimeType = $audioFile->getMimeType();

            if (!array_key_exists($mimeType, $this->allowedMimeTypes)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid audio format. Allowed: MP3, WAV, OGG, AAC, M4A, WEBM'
                ], 400);
            }

            // Generate unique filename
            $extension = $this->allowedMimeTypes[$mimeType];
            $fileName = 'voice_' . Str::uuid() . '_' . time() . '.' . $extension;

            // Store in storage/app/public/voice-messages
            $path = $audioFile->storeAs('voice-messages', $fileName, 'public');

            // Get file size
            $fileSize = $audioFile->getSize();
            $readableSize = $this->formatBytes($fileSize);

            // Create message
            $message = $chat->messages()->create([
                'sender_id' => Auth::id(),
                'sender_type' => get_class(Auth::user()),
                'message' => $request->input('message', 'Voice message'),
                'message_type' => 'voice',
                'file_url' => Storage::url($path),
                'file_name' => $fileName,
                'file_size' => $readableSize,
                'duration' => $request->duration,
                'metadata' => [
                    'original_name' => $audioFile->getClientOriginalName(),
                    'mime_type' => $mimeType,
                    'duration_seconds' => $request->duration,
                    'uploaded_at' => now()->toIso8601String()
                ]
            ]);

            // Update chat last message
            $chat->update([
                'last_message' => '🎤 Voice message',
                'last_message_at' => now()
            ]);

            // Broadcast event
            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'status' => 'success',
                'message' => $message->load('sender')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload voice message: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadChunkedVoiceMessage(Request $request, Chat $chat)
    {
        $this->authorize('send', $chat);

        $request->validate([
            'chunk' => 'required|file',
            'chunk_index' => 'required|integer',
            'total_chunks' => 'required|integer',
            'file_name' => 'required|string',
            'duration' => 'required|integer|min:1|max:600',
            'file_size' => 'required|integer'
        ]);

        try {
            $chunk = $request->file('chunk');
            $chunkIndex = $request->chunk_index;
            $totalChunks = $request->total_chunks;
            $originalFileName = $request->file_name;
            $duration = $request->duration;
            $totalFileSize = $request->file_size;

            // Validate total file size
            if ($totalFileSize > $this->maxFileSize * 1024) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File size too large. Maximum: 10MB'
                ], 400);
            }

            // Create temporary directory for chunks
            $tempDir = storage_path('app/temp/voice-chunks/' . md5($originalFileName));
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            // Save chunk
            $chunk->move($tempDir, $chunkIndex);

            // Check if all chunks are uploaded
            $uploadedChunks = glob($tempDir . '/*');

            if (count($uploadedChunks) == $totalChunks) {
                // All chunks uploaded, combine them
                $finalFileName = 'voice_' . Str::uuid() . '_' . time() . '.webm';
                $finalPath = storage_path('app/public/voice-messages/' . $finalFileName);

                // Ensure directory exists
                Storage::disk('public')->makeDirectory('voice-messages');

                // Combine chunks
                $finalFile = fopen($finalPath, 'wb');
                for ($i = 0; $i < $totalChunks; $i++) {
                    $chunkPath = $tempDir . '/' . $i;
                    $chunkContent = file_get_contents($chunkPath);
                    fwrite($finalFile, $chunkContent);
                    unlink($chunkPath); // Delete chunk
                }
                fclose($finalFile);

                // Clean up temp directory
                rmdir($tempDir);

                // Create message
                $readableSize = $this->formatBytes($totalFileSize);

                $message = $chat->messages()->create([
                    'sender_id' => Auth::id(),
                    'sender_type' => get_class(Auth::user()),
                    'message' => 'Voice message',
                    'message_type' => 'voice',
                    'file_url' => Storage::url('voice-messages/' . $finalFileName),
                    'file_name' => $finalFileName,
                    'file_size' => $readableSize,
                    'duration' => $duration,
                    'metadata' => [
                        'original_name' => $originalFileName,
                        'mime_type' => 'audio/webm',
                        'duration_seconds' => $duration,
                        'chunked_upload' => true,
                        'chunks' => $totalChunks,
                        'uploaded_at' => now()->toIso8601String()
                    ]
                ]);

                // Update chat
                $chat->update([
                    'last_message' => '🎤 Voice message',
                    'last_message_at' => now()
                ]);

                // Broadcast event
                broadcast(new MessageSent($message))->toOthers();

                return response()->json([
                    'status' => 'success',
                    'message' => 'File uploaded successfully',
                    'data' => $message->load('sender')
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Chunk uploaded',
                'chunk_index' => $chunkIndex,
                'total_chunks' => $totalChunks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload chunk: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteVoiceMessage(Message $message)
    {
        $this->authorize('delete', $message);

        if (!$message->isVoiceMessage()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not a voice message'
            ], 400);
        }

        try {
            // Delete file from storage
            $filePath = 'voice-messages/' . $message->file_name;
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Delete message from database (soft delete)
            $message->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Voice message deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete voice message'
            ], 500);
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
