<?php

namespace App\Services;

use App\Services\TwilioService;
use App\Exceptions\OtpException;
use App\Services\Otp\OtpManager;
use App\Http\Repositories\UserRepository;
use App\DataTransferObjects\PhoneLoginData;

class AuthService
{
    protected UserRepository $users;
    protected TwilioService $twilio;
    protected OtpManager $otpManager;

    public function __construct(UserRepository $users, TwilioService $twilio, OtpManager $otpManager)
    {
        $this->users = $users;
        $this->twilio = $twilio;
        $this->otpManager = $otpManager;
    }

    /**
     * Send OTP (tries Twilio Verify, falls back to SMS via local generated OTP)
     */
    public function sendOtp(PhoneLoginData $data): array
    {
        // 1️⃣ find or create user
        $user = $this->users->findByFullPhone($data->full_phone)
            ?? $this->users->createByPhone($data->country_code, $data->phone_number);

        /* ==========================
     | Try WhatsApp OTP first
     ========================== */
        $res = $this->twilio->sendOtpWhatsapp($data->full_phone);


dd($res);

        if ($res['success'] ?? false) {
            return [
                'method' => 'whatsapp_verify',
                'phone' => $data->full_phone,
            ];
        }

        /* ==========================
     | Fallback to SMS OTP
     ========================== */
        // $res = $this->twilio->sendOtpSms($data->full_phone);

        // if ($res['success'] ?? false) {
        //     return [
        //         'method' => 'sms_verify',
        //         'phone' => $data->full_phone,
        //     ];
        // }

        /* ==========================
     | Final fallback: Local OTP + SMS
     ========================== */
        $otp = $this->otpManager->generateAndStore($user);

        $sms = $this->twilio->sendSms(
            $data->full_phone,
            "رمز التحقق الخاص بك هو: $otp\nصالح لمدة 10 دقائق"
        );

        if (!($sms['success'] ?? false)) {
            throw new OtpException('Failed to send OTP via all channels');
        }

        return [
            'method' => 'local_sms_fallback',
            'phone' => $data->full_phone,
        ];
    }


    /**
     * Verify OTP: try Twilio verify then fallback local verification
     */
    public function verifyOtp(string $fullPhone, string $otp): array
    {
        $user = $this->users->findByFullPhone($fullPhone);
        if (!$user) throw new OtpException('Phone number not registered');

        $tw = $this->twilio->verifyOtp($fullPhone, $otp);
        if ($tw['success'] ?? false) {
            // create token and mark verified
            $user->phone_verified_at = now();
            $this->users->save($user);

            $token = $user->createToken('auth_token')->plainTextToken;
            return ['token' => $token, 'user' => $user];
        }

        // local verify
        if ($this->otpManager->verify($user, $otp)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return ['token' => $token, 'user' => $user];
        }

        throw new OtpException('Invalid or expired OTP');
    }

    public function canResend(string $fullPhone): bool
    {
        return $this->otpManager->canResend($fullPhone);
    }
}
