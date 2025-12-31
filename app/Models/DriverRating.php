<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// DriverRating.php
class DriverRating extends Model
{
    protected $guarded = [];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
