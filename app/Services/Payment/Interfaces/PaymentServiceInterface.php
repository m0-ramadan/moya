<?php

namespace App\Services\Payment\Interfaces;

interface PaymentServiceInterface
{
    /**
     * Create a payment order
     */
    public function createPayment(array $orderData): array;

    /**
     * Authorize payment
     */
    public function authorizePayment(string $orderId): array;

    /**
     * Capture payment
     */
    public function capturePayment(string $orderId, array $captureData): array;

    /**
     * Refund payment
     */
    public function refundPayment(string $orderId, array $refundData): array;

    /**
     * Cancel payment
     */
    public function cancelPayment(string $orderId): array;

    /**
     * Get order details
     */
    public function getOrder(string $orderId): array;

    /**
     * Get payment methods
     */
    public function getPaymentMethods(array $customerData): array;

    /**
     * Handle webhook notification
     */
    public function handleWebhook(array $payload): array;

    /**
     * Get service name
     */
    public function getServiceName(): string;
}
