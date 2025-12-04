<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'otp',
        'purpose',
        'status',
        'expires_at',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}