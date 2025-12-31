<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterType extends Model
{
    protected $table = 'water_types';

    protected $fillable = [
        'name',
        'image',
    ];
}
