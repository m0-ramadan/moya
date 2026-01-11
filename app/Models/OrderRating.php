<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'driver_id',
        'user_id',
        'rated_by', // 'user' أو 'driver'
        'rating',
        'comment',
        'aspects' // تقييم الجوانب المختلفة
    ];

    protected $casts = [
        'rating' => 'integer',
        'aspects' => 'array',
    ];

    protected $attributes = [
        'aspects' => '[]',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // تنقية التعليقات من المحتوى غير اللائق
    public function setCommentAttribute($value)
    {
        $this->attributes['comment'] = $this->sanitizeComment($value);
    }

    private function sanitizeComment($comment)
    {
        // يمكن إضافة قائمة بالكلمات الممنوعة
        $badWords = ['bad_word1', 'bad_word2'];
        return str_ireplace($badWords, '****', $comment);
    }
}
