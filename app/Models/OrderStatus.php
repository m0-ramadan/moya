<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $fillable = ['name', 'label'];

    /* ==============================
     | Status Constants
     ============================== */

    public const PENDING   = 'pending';
    public const IN_ROAD   = 'in-road';
    public const SCHEDULED = 'scheduled';
    public const DELIVERED = 'delivered';
    public const CANCELLED = 'cancelled';

    /* ==============================
     | Relationships
     ============================== */

    public function orders()
    {
        return $this->hasMany(Order::class, 'order_status_id');
    }

    /* ==============================
     | Helper Methods
     ============================== */

    public static function getAllStatuses(): array
    {
        return [
            self::PENDING,
            self::IN_ROAD,
            self::SCHEDULED,
            self::DELIVERED,
            self::CANCELLED,
        ];
    }
}
