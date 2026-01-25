<?php

namespace App\Contracts\Payment;

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
    public function refund(string $transactionId, float $amount, string $reason = ''): array;

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $transactionId): array;

    /**
     * Initiate payment (alias for createPaymentOrder)
     */
    public function initiatePayment(array $data): array;
    
    /**
     * Verify payment (alias for verifyTransaction)
     */
    public function verifyPayment(array $data): array;
    
    /**
     * Refund payment (alias for refund)
     */
    public function refundPayment(string $transactionId, float $amount, string $reason = ''): array;
    
    /**
     * Check payment status (alias for getTransactionStatus)
     */
    public function checkPaymentStatus(string $transactionId): array;
    
    /**
     * Validate webhook signature
     */
    public function isWebhookValid(array $data): bool;
    
    /**
     * Handle webhook callback
     */
    public function handleWebhook(array $data): array;
}