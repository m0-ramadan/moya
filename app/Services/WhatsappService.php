<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    public function sendOtp(string $to, string $code): array
    {
        return $this->sendMessage($to, "كود التحقق الخاص بك هو: {$code}");
    }

    public function sendMessage(string $to, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.wasender.token'),
                'Content-Type' => 'application/json',
            ])->post('https://wasenderapi.com/api/send-message', [
                'to' => $to,
                'text' => $message,
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return [
                    'success' => true,
                    'sid' => $data['data']['msgId'] ?? null,
                    'status' => $data['data']['status'] ?? 'sent',
                ];
            }

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Failed to send message',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
