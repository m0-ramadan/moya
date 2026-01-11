<?php

namespace App\Models\Wallet;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

abstract class AbstractWallet extends Model
{
    protected $casts = [
        'balance' => 'decimal:2',
        'held_balance' => 'decimal:2',
        'last_transaction_at' => 'datetime',
        'version' => 'integer'
    ];

    protected $attributes = [
        'version' => 1,
        'status' => 'active'
    ];

    /**
     * Get available balance
     */
    public function getAvailableBalanceAttribute(): float
    {
        return $this->balance - $this->held_balance;
    }

    /**
     * Get ledger entries
     */
    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class, 'wallet_id')
            ->where('wallet_type', $this->getWalletType());
    }

    /**
     * Calculate ledger balance
     */
    public function calculateLedgerBalance(): float
    {
        return (float) $this->ledgerEntries()
            ->whereIn('type', $this->getCreditTypes())
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->sum('amount') -
            (float) $this->ledgerEntries()
                ->whereIn('type', $this->getDebitTypes())
                ->where('status', LedgerEntry::STATUS_COMPLETED)
                ->sum('amount');
    }

    /**
     * Check wallet invariant
     */
    public function checkInvariant(): bool
    {
        $calculatedBalance = $this->calculateLedgerBalance();
        $difference = abs($this->balance - $calculatedBalance);
        $threshold = config('wallet.reconciliation.threshold', 0.01);

        $isValid = $difference <= $threshold;

        if (!$isValid) {
            Log::critical('Wallet invariant violation', [
                'wallet_id' => $this->id,
                'wallet_type' => $this->getWalletType(),
                'balance' => $this->balance,
                'calculated_balance' => $calculatedBalance,
                'difference' => $difference,
                'threshold' => $threshold
            ]);
        }

        return $isValid;
    }

    /**
     * Atomic balance update
     */
    public function updateBalance(float $amount, string $operation = 'increment'): bool
    {
        return DB::transaction(function () use ($amount, $operation) {
            $lockedWallet = static::where('id', $this->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedWallet) {
                throw new \Exception('Wallet not found');
            }

            $column = $operation === 'increment' ?
                DB::raw("balance + {$amount}") :
                DB::raw("balance - {$amount}");

            return static::where('id', $this->id)->update([
                'balance' => $column,
                'last_transaction_at' => now(),
                'version' => DB::raw('version + 1')
            ]);
        });
    }

    /**
     * Atomic held balance update
     */
    public function updateHeldBalance(float $amount, string $operation = 'increment'): bool
    {
        $column = $operation === 'increment' ?
            DB::raw("held_balance + {$amount}") :
            DB::raw("held_balance - {$amount}");

        return static::where('id', $this->id)->update([
            'held_balance' => $column,
            'last_transaction_at' => now()
        ]);
    }

    /**
     * Abstract methods to be implemented by child classes
     */
    abstract protected function getWalletType(): string;
    abstract protected function getCreditTypes(): array;
    abstract protected function getDebitTypes(): array;
}
