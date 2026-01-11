<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_ar',
        'name_en',
        'name_urdu',
        'code',
        'dial_code',
        'flag_emoji',
        'is_active',
        'sort_order'
    ];
}
