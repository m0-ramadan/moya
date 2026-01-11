<?php

namespace App\Models\Wallet;

use App\Models\Driver;

class DriverWallet extends AbstractWallet
{
    protected $table = 'driver_wallets';

    protected $fillable = [
        'driver_id',
        'balance',
        'held_balance',
        'currency',
        'status',
        'last_transaction_at',
        'daily_limit',
        'monthly_limit',
        'total_earnings_today',
        'total_withdrawals_today',
        'total_cashouts_today',
        'version'
    ];

    protected $attributes = [
        'daily_limit' => 20000,
        'monthly_limit' => 100000,
        'currency' => 'SAR'
    ];

    /**
     * Relationships
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get wallet type
     */
    protected function getWalletType(): string
    {
        return 'driver';
    }

    /**
     * Get credit types
     */
    protected function getCreditTypes(): array
    {
        return [
            LedgerEntry::TYPE_EARNING,
            LedgerEntry::TYPE_COMMISSION,
            LedgerEntry::TYPE_TRANSFER_IN,
            LedgerEntry::TYPE_REFUND,
            LedgerEntry::TYPE_RELEASE
        ];
    }

    /**
     * Get debit types
     */
    protected function getDebitTypes(): array
    {
        return [
            LedgerEntry::TYPE_CASHOUT,
            LedgerEntry::TYPE_TRANSFER_OUT,
            LedgerEntry::TYPE_PAYMENT,
            LedgerEntry::TYPE_FEE,
            LedgerEntry::TYPE_HOLD
        ];
    }

    /**
     * Check daily limit
     */
    public function canWithdrawToday(float $amount): bool
    {
        $today = now()->startOfDay();

        if ($this->last_transaction_at && $this->last_transaction_at->lt($today)) {
            $this->resetDailyTotals();
        }

        return ($this->total_cashouts_today + $amount) <= $this->daily_limit;
    }

    /**
     * Reset daily totals
     */
    public function resetDailyTotals(): void
    {
        $this->update([
            'total_earnings_today' => 0,
            'total_withdrawals_today' => 0,
            'total_cashouts_today' => 0
        ]);
    }

    /**
     * Update daily totals
     */
    public function updateDailyTotals(float $amount, string $type): void
    {
        $updates = [];

        switch ($type) {
            case 'earning':
                $updates['total_earnings_today'] = $this->total_earnings_today + $amount;
                break;
            case 'cashout':
                $updates['total_cashouts_today'] = $this->total_cashouts_today + $amount;
                break;
            case 'withdrawal':
                $updates['total_withdrawals_today'] = $this->total_withdrawals_today + $amount;
                break;
        }

        if (!empty($updates)) {
            $this->update($updates);
        }
    }

    /**
     * Add earnings from order
     */
    public function addEarnings(float $amount, string $orderId, string $description = null): LedgerEntry
    {
        $ledgerEntry = LedgerEntry::create([
            'wallet_type' => $this->getWalletType(),
            'wallet_id' => $this->id,
            'owner_type' => LedgerEntry::OWNER_TYPE_DRIVER,
            'owner_id' => $this->driver_id,
            'type' => LedgerEntry::TYPE_EARNING,
            'amount' => $amount,
            'balance_before' => $this->balance,
            'balance_after' => $this->balance + $amount,
            'available_balance_before' => $this->available_balance,
            'available_balance_after' => $this->available_balance + $amount,
            'description' => $description ?? 'أرباح من طلب',
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => 'EARN-' . now()->format('Ymd') . '-' . substr(md5(uniqid()), 0, 8),
            'metadata' => [
                'order_id' => $orderId,
                'driver_id' => $this->driver_id,
                'earning_type' => 'order_completion'
            ]
        ]);

        $this->updateBalance($amount, 'increment');
        $this->updateDailyTotals($amount, 'earning');

        return $ledgerEntry;
    }
}
