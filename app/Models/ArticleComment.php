<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'article_id',
        'user_id',
        'guest_name',
        'guest_email',
        'content',
        'parent_id',
        'likes_count',
        'replies_count',
        'status',
        'is_edited',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'is_edited' => 'boolean'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ArticleComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ArticleComment::class, 'parent_id')->orderBy('created_at');
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function isGuest()
    {
        return is_null($this->user_id);
    }
}
