<?php

namespace App\Services\Withdrawal;

interface PayoutGatewayInterface
{
    /**
     * Process payout
     */
    public function processPayout(array $data): array;

    /**
     * Check payout status
     */
    public function checkStatus(string $transactionId): array;

    /**
     * Get supported banks
     */
    public function getSupportedBanks(): array;

    /**
     * Get processing fees
     */
    public function getProcessingFees(float $amount, string $currency = 'SAR'): array;
}
