<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleShare extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'platform',
        'method',
        'ip_address',
        'shared_with'
    ];

    protected $casts = [
        'shared_with' => 'array'
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
