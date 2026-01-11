<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileChunk extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_id',
        'chunk_number',
        'original_name',
        'total_chunks',
        'total_size',
        'file_size',
        'mime_type',
        'message_type',
        'user_id',
        'chat_id'
    ];

    protected $casts = [
        'chunk_number' => 'integer',
        'total_chunks' => 'integer',
        'total_size' => 'integer',
        'file_size' => 'integer'
    ];
}
