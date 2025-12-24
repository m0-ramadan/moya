<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

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
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
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
}
