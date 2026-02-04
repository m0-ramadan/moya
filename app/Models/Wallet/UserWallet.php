<?php

namespace App\Models\Wallet;

use App\Models\User;

class UserWallet extends AbstractWallet
{
    protected $table = 'user_wallets';

    protected $fillable = [
        'user_id',
        'balance',
        'held_balance',
        'currency',
        'status',
        'last_transaction_at',
        'daily_limit',
        'monthly_limit',
        'total_deposits_today',
        'total_withdrawals_today',
        'total_transfers_today',
        'version'
    ];

    protected $attributes = [
        'daily_limit' => 10000,
        'monthly_limit' => 50000,
        'currency' => 'SAR'
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get wallet type
     */
    public function getWalletType(): string
    {
        return 'user';
    }

    /**
     * Get credit types
     */
    protected function getCreditTypes(): array
    {
        return [
            LedgerEntry::TYPE_DEPOSIT,
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
            LedgerEntry::TYPE_WITHDRAWAL,
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

        return ($this->total_withdrawals_today + $amount) <= $this->daily_limit;
    }

    /**
     * Reset daily totals
     */
    public function resetDailyTotals(): void
    {
        $this->update([
            'total_deposits_today' => 0,
            'total_withdrawals_today' => 0,
            'total_transfers_today' => 0
        ]);
    }

    /**
     * Update daily totals
     */
    public function updateDailyTotals(float $amount, string $type): void
    {
        $updates = [];

        switch ($type) {
            case 'deposit':
                $updates['total_deposits_today'] = $this->total_deposits_today + $amount;
                break;
            case 'withdrawal':
                $updates['total_withdrawals_today'] = $this->total_withdrawals_today + $amount;
                break;
            case 'transfer_out':
                $updates['total_transfers_today'] = $this->total_transfers_today + $amount;
                break;
        }

        if (!empty($updates)) {
            $this->update($updates);
        }
    }
}
