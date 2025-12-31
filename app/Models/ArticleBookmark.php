<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleBookmark extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'folder',
        'tags',
        'notes'
    ];

    protected $casts = [
        'tags' => 'array'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
