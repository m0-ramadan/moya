<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'notifications_enabled',
        'location_sharing_enabled',
        'language',
        'theme',
        'saved_addresses',
        'favorite_addresses',
        'account_temporarily_disabled'
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'location_sharing_enabled' => 'boolean',
        'saved_addresses' => 'array',
        'favorite_addresses' => 'array',
        'account_temporarily_disabled' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
