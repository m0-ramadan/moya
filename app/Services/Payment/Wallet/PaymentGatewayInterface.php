<?php

namespace App\Services\Payment\Wallet;

interface PaymentGatewayInterface
{
    /**
     * Create payment order / payment link
     */
    public function createPaymentOrder(array $data): array;

    /**
     * Verify transaction from webhook or callback
     */
    public function verifyTransaction(array $data): array;

    /**
     * Refund a transaction
     */
    public function refund(string $transactionId, float $amount): array;

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $transactionId): array;
}
