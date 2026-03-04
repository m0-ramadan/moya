<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Contract;
use App\Models\OtpHistory;
use App\Models\DeviceToken;
use App\Traits\CustomNotifiable;
use App\Models\Wallet\UserWallet;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Wallet\LedgerEntry;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable as LaravelNotifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, CustomNotifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'facebook_id',
        'apple_id',
        'phone',
        'phone_number',
        'country_code',
        'full_phone',
        'phone_verified_at',
        'otp',
        'otp_expires_at',
        'avatar',
        'allow_notifications',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'phone_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_expires_at' => 'datetime',
    ];

    /**
     * Get the user's formatted phone number
     */
    public function getFormattedPhoneAttribute(): string
    {
        return $this->country_code . $this->phone_number;
    }

    /**
     * Check if OTP is expired
     */
    public function isOtpExpired(): bool
    {
        return $this->otp_expires_at && $this->otp_expires_at->isPast();
    }

    /**
     * Check if phone is verified
     */
    public function isPhoneVerified(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * Generate OTP
     */
    public function generateOtp(): string
    {
        $this->otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_expires_at = now()->addMinutes(10);
        $this->save();

        // Log OTP history
        OtpHistory::create([
            'user_id' => $this->id,
            'phone_number' => $this->full_phone,
            'otp' => $this->otp,
            'purpose' => 'login',
            'status' => 'pending',
            'expires_at' => $this->otp_expires_at,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $this->otp;
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(string $otp): bool
    {
        if ($this->isOtpExpired()) {
            return false;
        }

        if ($this->otp === $otp) {
            $this->phone_verified_at = now();
            $this->otp = null;
            $this->otp_expires_at = null;
            $this->save();

            // Update OTP history
            OtpHistory::where('user_id', $this->id)
                ->where('otp', $otp)
                ->where('status', 'pending')
                ->update(['status' => 'verified']);

            return true;
        }

        return false;
    }

    // علاقة المستخدم مع العقود
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function savedLocations()
    {
        return $this->hasMany(SavedLocation::class);
    }

    // علاقة المستخدم مع المدفوعات
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // العقد النشط الحالي للمستخدم
    public function activeContract()
    {
        return $this->hasOne(Contract::class)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc');
    }

    // طلبات المستخدم
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Clear all notifications (يمكن حذف هذه الدالة لأنها موجودة في الـ trait)
     */
    public function clearAllNotifications(): int
    {
        return $this->clearNotifications();
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function activeDeviceTokens()
    {
        return $this->hasMany(DeviceToken::class)->where('is_active', true);
    }



    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if ($user->type === 'user') {
                $user->createUserWallet();
            }
        });
    }

    /**
     * Get formatted phone number
     */
    // public function getFormattedPhoneAttribute(): string
    // {
    //     return $this->country_code . $this->phone_number;
    // }

    /**
     * Get user's wallet
     */
    public function userWallet()
    {
        return $this->hasOne(UserWallet::class);
    }

    /**
     * Create user wallet
     */
    public function createUserWallet()
    {
        if (!$this->userWallet) {
            return UserWallet::create([
                'user_id' => $this->id,
                'balance' => 0,
                'held_balance' => 0,
                'currency' => config('wallet.default_currency', 'SAR'),
                'status' => 'active'
            ]);
        }

        return $this->userWallet;
    }

    /**
     * Get wallet
     */
    // public function wallet()
    // {
    //     return $this->userWallet;
    // }
public function wallet()
{
    return $this->hasOne(\App\Models\Wallet\UserWallet::class);
}
    /**
     * Check if user can transact
     */
    public function canTransact(float $amount, string $type = 'withdrawal'): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $wallet = $this->wallet();

        if (!$wallet || $wallet->status !== 'active') {
            return false;
        }

        return true;
    }

    /**
     * Get ledger entries
     */
    public function ledgerEntries()
    {
        return LedgerEntry::where('owner_type', 'user')
            ->where('owner_id', $this->id);
    }
    public function driver()
    {
        return $this->hasOne(Driver::class);
    }
}
