<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Driver.php
class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone_number',
        'id_number',
        'license_number',
        'issue_date',
        'expiry_date',
        'is_active',
        'average_rating',
        'total_ratings',
        'total_orders',
        'national_id',
        'date_of_birth',
        'first_name',
        'father_name',
        'grandfather_name',
        'family_name',
        'blood_type',
        'status',
        'photo',
        'allow_notifications',
    ];


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
