<?php

namespace App\Contracts\Payment;

interface PaymentGatewayInterface
{
    public function initiatePayment(array $data): array;
    public function verifyPayment(array $data): array;
    public function refundPayment(string $transactionId, float $amount, string $reason = ''): array;
    public function checkPaymentStatus(string $transactionId): array;
    public function isWebhookValid(array $data): bool;
    public function handleWebhook(array $data): array;
}
