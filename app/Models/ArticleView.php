<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleView extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'device_type',
        'browser',
        'platform',
        'duration_seconds',
        'is_unique'
    ];

    protected $casts = [
        'is_unique' => 'boolean'
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
