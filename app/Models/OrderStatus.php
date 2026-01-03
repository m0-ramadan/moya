<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $fillable = ['name', 'label'];
    public function orders()
    {
        return $this->hasMany(Order::class, 'order_status_id');
    }
}
