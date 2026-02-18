<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    protected $fillable = [
        'order_id',
        'cancelled_by_user_id',
        'cancelled_by_driver_id',
        'reason',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
