<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment_id',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'ip_address' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // public function comment()
    // {
    //     return $this->belongsTo(Comment::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
