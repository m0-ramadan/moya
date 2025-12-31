<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Driver.php
class Driver extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }

    public function ratings()
    {
        return $this->hasMany(DriverRating::class);
    }

    public function reports()
    {
        return $this->hasMany(DriverReport::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
