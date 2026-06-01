<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioService
{
    protected Client $client;
    protected string $verifySid;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );

        $this->verifySid = config('services.twilio.verify_sid');
    }

    /* =======================
     | OTP
     ======================= */

    // OTP via SMS
    public function sendOtpSms(string $phone): array
    {
        return $this->sendVerifyOtp($phone, 'sms');
    }

    protected function sendVerifyOtp(string $to, string $channel): array
    {
        try {
            $verification = $this->client
                ->verify->v2
                ->services($this->verifySid)
                ->verifications
                ->create($to, $channel);

            return [
                'success' => true,
                'channel' => $channel,
                'status' => $verification->status,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verifyOtp(string $phone, string $code): array
    {
        try {
            $check = $this->client
                ->verify->v2
                ->services($this->verifySid)
                ->verificationChecks
                ->create([
                    'to' => $phone,
                    'code' => $code,
                ]);

            return [
                'success' => $check->status === 'approved',
                'status' => $check->status,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /* =======================
     | SMS Fallback
     ======================= */

   public function sendSms(string $to, string $message): array
{
    try {

        // تأكد إن الرقم بصيغة دولية
        if (!str_starts_with($to, '+')) {
            return [
                'success' => false,
                'error'   => 'Phone number must be in E.164 format (e.g. +2011...)',
            ];
        }

        $from = config('services.twilio.from');

        if (!$from) {
            return [
                'success' => false,
                'error'   => 'Twilio FROM number is not configured',
            ];
        }

        $messageInstance = $this->client->messages->create(
            $to,
            [
                'from' => $from,
                'body' => $message,
            ]
        );

        return [
            'success' => true,
            'sid'     => $messageInstance->sid,
            'status'  => $messageInstance->status,
        ];

    } catch (TwilioException $e) {

        Log::error('Twilio SMS Error', [
            'to'    => $to,
            'error' => $e->getMessage(),
            'code'  => $e->getCode(),
        ]);

        return [
            'success' => false,
            'error'   => $e->getMessage(),
            'code'    => $e->getCode(),
        ];
    }
}
}
