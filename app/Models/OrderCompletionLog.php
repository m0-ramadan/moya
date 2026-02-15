<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCompletionLog extends Model
{
    protected $fillable = [
        'order_id',
        'driver_id',
        'user_id',
        'completed_at',
        'delivery_duration_minutes',
        'total_distance_km',
        'final_price',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'delivery_duration_minutes' => 'integer',
        'total_distance_km' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
