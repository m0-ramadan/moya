<?php

namespace App\Services;

use Twilio\Rest\Client;
use Exception;

class TwilioService
{
    protected $client;
    protected $verifySid;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.auth_token');
        $this->verifySid = config('services.twilio.verify_sid');

        $this->client = new Client($sid, $token);
    }

    /**
     * Send OTP via SMS
     */
    public function sendOtp(string $phoneNumber): array
    {
        try {
            $verification = $this->client->verify->v2->services($this->verifySid)
                ->verifications
                ->create($phoneNumber, "sms");

            return [
                'success' => true,
                'sid' => $verification->sid,
                'status' => $verification->status,
                'to' => $verification->to
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(string $phoneNumber, string $code): array
    {
        try {
            $verificationCheck = $this->client->verify->v2->services($this->verifySid)
                ->verificationChecks
                ->create([
                    'to' => $phoneNumber,
                    'code' => $code
                ]);

            return [
                'success' => $verificationCheck->status === 'approved',
                'status' => $verificationCheck->status,
                'valid' => $verificationCheck->valid
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS directly (fallback method)
     */
    public function sendSms(string $to, string $message): array
    {
        try {
            $message = $this->client->messages->create(
                $to,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message
                ]
            );

            return [
                'success' => true,
                'sid' => $message->sid,
                'status' => $message->status
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}