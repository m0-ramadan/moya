<?php

namespace App\Models;

use App\Models\Wallet\LedgerEntry;
use App\Models\Wallet\DriverWallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Driver.php
class Driver extends Model
{

    protected $fillable = [

        'user_id',

        // الجنسية
        'citizenship',
        'country_id',

        // بيانات شخصية
        'date_of_birth',
        'national_id',
        'iqama_number',
        'iqama_expiry_date',

        // صور
        'personal_photo',
        'id_image_front',
        'id_image_back',

        // رخصة القيادة
        'license_number',
        'license_expiry_date',
        'license_image_front',
        'license_image_back',

        // المركبة
        'vehicle_size',
        'is_vehicle_owner',
        'vehicle_plate_number',
        'vehicle_registration_number',
        'vehicle_residency_number',
        //'vehicle_model',
        // 'vehicle_year',
        //  'vehicle_color',

        // رخصة السير
        'vehicle_registration_image',

        // التحقق
        'is_verified',
        'verified_at',
        'rejection_reason',

        'status',
        'is_active',
    ];
    protected $casts = [
        'license_expiry_date' => 'date',
        'date_of_birth' => 'date',
        'verified_at' => 'date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function vehicle()
    // {
    //     return $this->hasOne(Vehicle::class);
    // }

    public function ratings()
    {
        return $this->hasMany(DriverRating::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function reports()
    {
        return $this->hasMany(DriverReport::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Boot the model
     */
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($driver) {
    //         $driver->createDriverWallet();
    //     });
    // }

    /**
     * Get driver's wallet
     */
    public function driverWallet()
    {
        return $this->hasOne(DriverWallet::class);
    }


    /**
     * Get wallet
     */
    public function wallet()
    {
        return $this->driverWallet;
    }

    /**
     * Check if driver can transact
     */
    public function canTransact(float $amount, string $type = 'withdrawal'): bool
    {
        if (!$this->is_active || $this->status !== 'active') {
            return false;
        }

        $wallet = $this->wallet();

        if (!$wallet || $wallet->status !== 'active') {
            return false;
        }

        return true;
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->father_name . ' ' . $this->grandfather_name . ' ' . $this->family_name;
    }

    /**
     * Get ledger entries
     */
    public function ledgerEntries()
    {
        return LedgerEntry::where('owner_type', 'driver')
            ->where('owner_id', $this->id);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // إزالة الحدث المكرر
        static::created(function ($driver) {
            $driver->createDriverWallet();
        });
    }

    /**
     * Create driver wallet
     */
    public function createDriverWallet()
    {
        if (!$this->driverWallet()->exists()) {
            return DriverWallet::create([
                'driver_id' => $this->id,
                'balance' => 0,
                'held_balance' => 0,
                'currency' => config('wallet.default_currency', 'SAR'),
                'status' => 'active',
                'daily_limit' => 20000,
                'monthly_limit' => 100000
            ]);
        }

        return $this->driverWallet;
    }

    /* ================= Helpers ================= */

    public function isApproved(): bool
    {
        return $this->is_verified === true;
    }

    public function isSaudi(): bool
    {
        return $this->citizenship === 'saudi';
    }

    public function isResident(): bool
    {
        return $this->citizenship === 'resident';
    }

    public function latestLocation()
    {
        return $this->hasOne(DriverLocation::class)->latestOfMany();
    }
    public function currectLocation()
    {
        return $this->hasOne(DriverCurrentLocation::class);
    }
}
