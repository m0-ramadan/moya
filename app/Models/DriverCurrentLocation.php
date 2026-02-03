<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverCurrentLocation extends Model
{
    use HasFactory;

    // اسم الجدول (لو الاسم مختلف عن convention)
    protected $table = 'driver_current_location';

    // الأعمدة اللي مسموح بالكتابة عليها (Mass Assignment)
    protected $fillable = [
        'driver_id',
        'lat',
        'lng',
        'speed',
        'heading',
        'last_updated_at',
    ];

    // علاقة بالسواق
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    // لتعطيك الـ timestamps بشكل صحيح
    protected $dates = [
        'created_at',
        'updated_at',
        'last_updated_at',
    ];
}
