<?php

namespace App\Services;

use App\DataTransferObjects\PhoneLoginData;
use App\Exceptions\OtpException;
use App\Http\Repositories\UserRepository;
use App\Services\Otp\OtpManager;

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
    public function sendOtp(PhoneLoginData $data, $request): array
    {
        // 1️⃣ Find or create user
        $user = $this->users->findByFullPhone($data->full_phone)
            ?? $this->users->createByPhone($data->country_code, $data->phone_number);

        // 2️⃣ Generate OTP once
        $otp = $this->otpManager->generateAndStore($user);

        /* ==========================
         | User explicitly wants SMS
         ========================== */
        if ($request->input('otp_method') === 'sms') {

            $sms = $this->twilio->sendSms(
                $data->full_phone,
                "رمز التحقق الخاص بك هو: $otp\nصالح لمدة 10 دقائق"
            );

            if (! empty($sms['success']) && $sms['success'] === true) {
                return [
                    'success' => true,
                    'method' => 'sms',
                    'message' => 'تم إرسال رمز التحقق عبر رسالة نصية (SMS). يرجى إدخال الرمز لإكمال تسجيل الدخول.',
                    'phone' => $data->full_phone,
                ];
            }

            return [
                'success' => false,
                'method' => 'sms',
                'message' => 'حدث خطأ أثناء إرسال رسالة التحقق. يرجى المحاولة مرة أخرى.',
            ];
        }

        /* ==========================
         | Try WhatsApp OTP
         ========================== */
        $whatsapp = $this->twilio->sendWhatsappOtp($data->full_phone, $otp);

        if (! empty($whatsapp['success']) && $whatsapp['success'] === true) {
            return [
                'success' => true,
                'method' => 'whatsapp',
                'message' => 'تم إرسال رمز التحقق عبر واتساب. يرجى التحقق من الرسائل.',
                'phone' => $data->full_phone,
            ];
        }

        /* ==========================
         | Final fallback: SMS
         ========================== */
        $sms = $this->twilio->sendSms(
            $data->full_phone,
            "رمز التحقق الخاص بك هو: $otp\nصالح لمدة 10 دقائق"
        );

        if (! empty($sms['success']) && $sms['success'] === true) {
            return [
                'success' => true,
                'method' => 'sms',
                'message' => 'تعذر الإرسال عبر واتساب. تم إرسال رمز التحقق عبر رسالة نصية (SMS).',
                'phone' => $data->full_phone,
            ];
        }

        throw new OtpException('لا يمكن ارسال رمز التحقق الي هذاالرقم حاليا، يرجى المحاولة لاحقاً.');
    }

    /**
     * Verify OTP: try Twilio verify then fallback local verification
     */
    public function verifyOtp(string $fullPhone, string $otp): array
    {
        $user = $this->users->findByFullPhone($fullPhone);
        if (! $user) {
            throw new OtpException('Phone number not registered');
        }

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
