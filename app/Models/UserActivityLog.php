<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'description',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
