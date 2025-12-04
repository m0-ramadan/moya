<?php
// app/Services/Otp/OtpManager.php

namespace App\Services\Otp;

use App\Models\OtpHistory;
use App\Models\User;
use Carbon\Carbon;

class OtpManager
{
    protected int $ttlMinutes = 10;
    protected int $maxResendAttempts = 3;
    protected int $resendWindowMinutes = 10;

    public function generateAndStore(User $user): string
    {
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes($this->ttlMinutes);

        $user->otp = $otp;
        $user->otp_expires_at = $expiresAt;
        $user->save();

        OtpHistory::create([
            'user_id' => $user->id,
            'phone_number' => $user->full_phone,
            'otp' => $otp,
            'purpose' => 'login',
            'status' => 'pending',
            'expires_at' => $expiresAt,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $otp;
    }

    public function canResend(string $fullPhone): bool
    {
        $count = OtpHistory::where('phone_number', $fullPhone)
            ->where('created_at', '>', now()->subMinutes($this->resendWindowMinutes))
            ->count();

        return $count < $this->maxResendAttempts;
    }

    public function verify(User $user, string $otp): bool
    {
        if (!$user->otp || !$user->otp_expires_at) return false;
        if ($user->otp_expires_at->isPast()) return false;
        if ($user->otp !== $otp) return false;

        // mark verified
        $user->phone_verified_at = now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        OtpHistory::where('user_id', $user->id)
            ->where('otp', $otp)
            ->where('status', 'pending')
            ->update(['status' => 'verified']);

        return true;
    }
}
