<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'driver_id',
        'price',
        'status',
        'delivery_duration_minutes',
        'expired_at','expires_at'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'expires_at' => 'datetime',
        'price' => 'decimal:2',
        'delivery_duration_minutes' => 'integer',
    ];

    // ================== العلاقات ==================
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    // ================== التوابع المساعدة ==================

    /**
     * التحقق مما إذا كان العرض منتهي الصلاحية
     */
    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        if ($this->expired_at && Carbon::now()->greaterThan($this->expired_at)) {
            // تحديث الحالة تلقائياً
            $this->update(['status' => 'expired']);
            return true;
        }

        return false;
    }

    /**
     * التحقق مما إذا كان العرض نشطاً
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'accepted']) && !$this->isExpired();
    }

    /**
     * جلب الوقت المتبقي للعرض بالدقائق
     */
    public function getRemainingMinutes(): ?int
    {
        if (!$this->expired_at) {
            return null;
        }

        if ($this->isExpired()) {
            return 0;
        }

        return Carbon::now()->diffInMinutes($this->expired_at, false);
    }

    /**
     * نطاق للعروض النشطة
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'accepted'])
            ->where(function ($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>', Carbon::now());
            });
    }

    /**
     * نطاق للعروض منتهية الصلاحية
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere('expired_at', '<=', Carbon::now());
        });
    }
}