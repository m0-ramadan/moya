<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contract_number',
        'contract_type', // 'individual', 'company'
        'company_name',
        'applicant_name',
        'duration_type', // 'quarterly', 'semi_annual', 'annual', 'monthly'
        'start_date',
        'end_date',
        'renewal_date',
        'total_orders_limit',
        'remaining_orders',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status', // 'active', 'expired', 'pending', 'cancelled'
        'notes',
        'phone','payment_proof'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'renewal_date' => 'date',
    ];

    // علاقة العقد مع المستخدم
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // علاقة العقد مع مواقع التوصيل
    public function deliveryLocations(): HasMany
    {
        return $this->hasMany(ContractDeliveryLocation::class);
    }

    // علاقة العقد مع المدفوعات
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // علاقة العقد مع الطلبات
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // الحصول على العقد النشط للمستخدم
    public static function getActiveContract($userId)
    {
        return self::where('user_id', $userId)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
