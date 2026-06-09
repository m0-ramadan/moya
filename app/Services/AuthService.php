<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Services\TwilioService;
use App\Services\WhatsappService;
use App\Exceptions\OtpException;
use App\Services\Otp\OtpManager;
use App\Http\Repositories\UserRepository;
use App\DataTransferObjects\PhoneLoginData;

class AuthService
{
    private const BLOCKED_ACCOUNT_MESSAGE = 'لا يمكن إتمام الطلب الآن، لأن حسابك موقوف مؤقتًا. برجاء التواصل مع الدعم.';

    protected UserRepository $users;
    protected TwilioService $twilio;
    protected WhatsappService $whatsapp;
    protected OtpManager $otpManager;

    public function __construct(
        UserRepository $users,
        TwilioService $twilio,
        WhatsappService $whatsapp,
        OtpManager $otpManager
    )
    {
        $this->users = $users;
        $this->twilio = $twilio;
        $this->whatsapp = $whatsapp;
        $this->otpManager = $otpManager;
    }

    public function sendOtp(PhoneLoginData $data, $request): array
    {
        $user = $this->users->findByFullPhone($data->full_phone)
            ?? $this->users->createByPhone($data->country_code, $data->phone_number);

        if ($user->isBanned()) {
            throw new OtpException(self::BLOCKED_ACCOUNT_MESSAGE);
        }

        $otp = $this->otpManager->generateAndStore($user);

        $userAgent = strtolower($request->header('User-Agent', ''));
        $isIphone = str_contains($userAgent, 'iphone');

        if ($isIphone) {
            $sms = $this->twilio->sendSms(
                $data->full_phone,
                "رمز التحقق الخاص بك هو: $otp\nصالح لمدة 10 دقائق"
            );

            if (!empty($sms['success']) && $sms['success'] === true) {
                return [
                    'method' => 'sms_iphone',
                    'phone'  => $data->full_phone,
                    'otp'    => $otp,
                ];
            }

            throw new OtpException('فشل إرسال رمز التحقق عبر SMS.');
        }

        if ($request->input('otp_method') === 'sms') {
       //  if (true) {

            $sms = $this->twilio->sendSms(
                $data->full_phone,
                "رمز التحقق الخاص بك هو: $otp\nصالح لمدة 10 دقائق"
            );

            if (!empty($sms['success']) && $sms['success'] === true) {
                return [
                    'method' => 'sms_verify',
                    'phone'  => $data->full_phone,
                    'otp'    => $otp,
                ];
            }

            return [
                'method'  => 'sms_verify',
                'phone'   => $data->full_phone,
                'otp'     => $otp,
                'success' => false,
                'error'   => $sms['error'] ?? 'فشل إرسال الرسالة بدون سبب محدد',
            ];
        }

        $whatsapp = $this->whatsapp->sendOtp($data->full_phone, $otp);

        if (!empty($whatsapp['success']) && $whatsapp['success'] === true) {
            return [
                'method' => 'whatsapp_verify',
                'phone'  => $data->full_phone,
                'otp'    => $otp,
            ];
        }

        $sms = $this->twilio->sendSms(
            $data->full_phone,
            "رمز التحقق الخاص بك هو: $otp\nصالح لمدة 10 دقائق"
        );

        if (empty($sms['success']) || $sms['success'] !== true) {
            throw new OtpException('لا يمكن ارسال رمز التحقق الي هذا الرقم حاليا، يرجى المحاولة لاحقاً.');
        }

        return [
            'method' => 'sms_fallback',
            'phone'  => $data->full_phone,
            'otp'    => $otp,
        ];
    }

    /**
     * Verify OTP: try Twilio verify then fallback local verification
     */
    public function verifyOtp(string $fullPhone, string $otp): array
    {
        $user = $this->users->findByFullPhone($fullPhone);
        if (!$user) throw new OtpException('Phone number not registered');
        if ($user->isBanned()) {
            throw new OtpException(self::BLOCKED_ACCOUNT_MESSAGE);
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
