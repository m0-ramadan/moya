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
        'price',
    ];

    // المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
