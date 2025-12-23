<?php

namespace App\Services;

use Twilio\Rest\Client;
use Exception;

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

    // OTP via WhatsApp
    public function sendOtpWhatsapp(string $phone): array
    {
        return $this->sendVerifyOtp("whatsapp:$phone", 'whatsapp');
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
     | WhatsApp Messages
     ======================= */

    public function sendWhatsapp(string $to, string $message): array
    {
        try {
            $msg = $this->client->messages->create(
                "whatsapp:$to",
                [
                    'from' => config('services.twilio.whatsapp_from'),
                    'body' => $message,
                ]
            );

            return [
                'success' => true,
                'sid' => $msg->sid,
                'status' => $msg->status,
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
            $msg = $this->client->messages->create(
                $to,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message,
                ]
            );

            return [
                'success' => true,
                'sid' => $msg->sid,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
