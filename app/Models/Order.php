<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'driver_id',
        'service_id',
        'water_type_id',
        'saved_location_id',
        'order_status_id',
        'order_date',
        'contract_id',
    ];

    // المستخدم
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // السواق
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // الخدمة
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // نوع المويه
    public function waterType()
    {
        return $this->belongsTo(WaterType::class, 'water_type_id');
    }

    // مكان المستخدم
    public function location()
    {
        return $this->belongsTo(SavedLocation::class, 'saved_location_id');
    }

    // حالة الطلب
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function offers()
    {
        return $this->hasMany(OrderOffer::class);
    }

    public function acceptedOffer()
    {
        return $this->hasOne(OrderOffer::class)
            ->where('status', 'accepted');
    }

    // في Order Model إضافة هذه العلاقات
    public function driverLocations()
    {
        return $this->hasMany(DriverLocation::class);
    }

    public function latestDriverLocation()
    {
        return $this->hasOne(DriverLocation::class)->latest();
    }

    public function ratings()
    {
        return $this->hasMany(OrderRating::class);
    }

    public function userRating()
    {
        return $this->hasOne(OrderRating::class)->where('rated_by', 'user');
    }
}
