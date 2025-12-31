<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedLocation extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'city',
        'area',
        'latitude',
        'longitude',
        'type',
        'is_favorite',
        'additional_info'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_favorite' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
