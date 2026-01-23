<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Create payment order
     */
    public function createPaymentOrder(array $data): array;

    /**
     * Verify transaction
     */
    public function verifyTransaction(array $data): array;

    /**
     * Refund transaction
     */
    public function refund(string $transactionId, float $amount): array;

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $transactionId): array;
}
