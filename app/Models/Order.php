<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // ================== الثوابت ==================
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';
    const PAYMENT_STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    const PAYMENT_METHOD_WALLET = 'wallet';
    const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';
    const PAYMENT_METHOD_MADA = 'mada';
    const PAYMENT_METHOD_APPLE_PAY = 'apple_pay';
    const PAYMENT_METHOD_PAYMOB = 'paymob';

    const PAYMENT_STATUS_PROCESSING = 'processing';

    const PAYMENT_GATEWAY_WALLET = 'wallet';
    const PAYMENT_GATEWAY_PAYMOB = 'paymob';
    const PAYMENT_GATEWAY_TAMARA = 'tamara';
    const PAYMENT_GATEWAY_TABBY = 'tabby';

    const STATUS_IN_ROAD = 'in-road';
    // الحقول القابلة للتعبئة
    protected $fillable = [
        'user_id',
        'driver_id',
        'service_id',
        'water_type_id',
        'saved_location_id',
        'order_status_id',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'payment_details',
        'paid_at',
        'order_date',
        'contract_id',
        'payment_gateway',
        'expires_at',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'paid_at' => 'datetime',
        'order_date' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];



    // ================== الأساليب المساعدة ==================

    /**
     * التحقق من حالة الدفع المعلق
     */
    public function isPending(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PENDING;
    }

    /**
     * التحقق من حالة الاسترداد
     */
    public function isRefunded(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_REFUNDED;
    }



    // ================== العلاقات ==================

    // المستخدم الذي أنشأ الطلب
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // السائق المسؤول عن الطلب
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function driverOrder()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
    // الخدمة المطلوبة
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // نوع المياه
    public function waterType()
    {
        return $this->belongsTo(WaterType::class, 'water_type_id');
    }

    // مكان حفظ المستخدم
    public function location()
    {
        return $this->belongsTo(SavedLocation::class, 'saved_location_id');
    }

    // حالة الطلب
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    // العروض المقدمة للطلب
    public function offers()
    {
        return $this->hasMany(OrderOffer::class);
    }

    // العرض المقبول فقط
    public function acceptedOffer()
    {
        return $this->hasOne(OrderOffer::class)
            ->where('status', 'accepted');
    }

    // تتبع مواقع السائق أثناء الطلب
    public function driverLocations()
    {
        return $this->hasMany(DriverLocation::class);
    }

    // آخر موقع للسائق
    public function latestDriverLocation()
    {
        return $this->hasOne(DriverLocation::class)->latest();
    }

    // تقييمات الطلب
    public function ratings()
    {
        return $this->hasMany(OrderRating::class);
    }

    // تقييم المستخدم فقط
    public function userRating()
    {
        return $this->hasOne(OrderRating::class)->where('rated_by', 'user');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function isProcessing(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PROCESSING;
    }

    public function isPendingPayment(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PENDING;
    }

    public function getPaymentGateway(): ?string
    {
        return $this->payment_gateway;
    }

    public function getPaymentTransactionId(): ?string
    {
        return $this->payment_transaction_id;
    }

    public function getPaymentAmount(): float
    {
        if ($acceptedOffer = $this->offers()->whereIn('status', ['accepted', 'paid'])->first()) {
            return $acceptedOffer->price;
        }

        return $this->price ?? 0;
    }
}
