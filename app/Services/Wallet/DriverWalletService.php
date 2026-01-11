<?php

namespace App\Services\Wallet;

use App\Models\Driver;
use App\Models\Wallet\DriverWallet;
use App\Models\Wallet\LedgerEntry;
use App\Models\Wallet\IdempotencyKey;
use App\Events\WalletEvent;
use Illuminate\Support\Facades\DB;

class DriverWalletService extends AbstractWalletService
{
    /**
     * Get driver wallet balance
     */
    public function getBalance(Driver $driver): array
    {
        $wallet = $driver->driverWallet ?? $driver->createDriverWallet();

        return [
            'wallet_id' => $wallet->id,
            'balance' => $wallet->balance,
            'available_balance' => $wallet->available_balance,
            'held_balance' => $wallet->held_balance,
            'currency' => $wallet->currency,
            'status' => $wallet->status,
            'daily_limit' => $wallet->daily_limit,
            'total_earnings_today' => $wallet->total_earnings_today,
            'total_cashouts_today' => $wallet->total_cashouts_today,
            'last_transaction_at' => $wallet->last_transaction_at
        ];
    }

    /**
     * Cash out earnings
     */
    public function cashOut(Driver $driver, float $amount, array $data = []): LedgerEntry
    {
        $wallet = $driver->driverWallet ?? $driver->createDriverWallet();

        // Validate
        $this->validateCashout($wallet, $amount);

        return $this->processWithIdempotency(
            'cashout_' . $driver->id . '_' . md5($amount . now()->toISOString()),
            ['driver_id' => $driver->id, 'amount' => $amount, 'data' => $data],
            function () use ($wallet, $amount, $driver, $data) {
                $lockedWallet = $this->lockWallet($wallet);

                $entry = $this->debit(
                    $lockedWallet,
                    $amount,
                    LedgerEntry::TYPE_CASHOUT,
                    $data['description'] ?? 'سحب أرباح',
                    [
                        'withdrawal_method' => 'bank_transfer',
                        'bank_account_id' => $data['bank_account_id'],
                        'driver_id' => $driver->id
                    ],
                    [
                        'owner_type' => LedgerEntry::OWNER_TYPE_DRIVER,
                        'owner_id' => $driver->id
                    ]
                );

                // Update daily totals
                $lockedWallet->updateDailyTotals($amount, 'cashout');

                // Fire event
                event(new WalletEvent($entry));

                return $entry;
            },
            LedgerEntry::OWNER_TYPE_DRIVER,
            $driver->id
        );
    }

    /**
     * Add earnings
     */
    public function addEarnings(Driver $driver, float $amount, string $orderId, array $metadata = []): LedgerEntry
    {
        $wallet = $driver->driverWallet ?? $driver->createDriverWallet();

        return $this->processWithIdempotency(
            'earning_' . $orderId,
            ['driver_id' => $driver->id, 'order_id' => $orderId, 'amount' => $amount],
            function () use ($wallet, $amount, $driver, $orderId, $metadata) {
                $lockedWallet = $this->lockWallet($wallet);

                $entry = $this->credit(
                    $lockedWallet,
                    $amount,
                    LedgerEntry::TYPE_EARNING,
                    'أرباح من طلب #' . $orderId,
                    array_merge([
                        'order_id' => $orderId,
                        'earning_type' => 'order_completion'
                    ], $metadata),
                    [
                        'owner_type' => LedgerEntry::OWNER_TYPE_DRIVER,
                        'owner_id' => $driver->id
                    ]
                );

                // Update daily totals
                $lockedWallet->updateDailyTotals($amount, 'earning');

                // Fire event
                event(new WalletEvent($entry));

                return $entry;
            },
            LedgerEntry::OWNER_TYPE_DRIVER,
            $driver->id
        );
    }

    /**
     * Add commission
     */
    public function addCommission(Driver $driver, float $amount, string $description, array $metadata = []): LedgerEntry
    {
        $wallet = $driver->driverWallet ?? $driver->createDriverWallet();

        $entry = $this->credit(
            $wallet,
            $amount,
            LedgerEntry::TYPE_COMMISSION,
            $description,
            array_merge(['commission_type' => 'system'], $metadata),
            [
                'owner_type' => LedgerEntry::OWNER_TYPE_DRIVER,
                'owner_id' => $driver->id
            ]
        );

        $wallet->updateDailyTotals($amount, 'earning');
        event(new WalletEvent($entry));

        return $entry;
    }

    /**
     * Get transaction history
     */
    public function getTransactionHistory(Driver $driver, array $filters = [])
    {
        $query = LedgerEntry::where('owner_type', LedgerEntry::OWNER_TYPE_DRIVER)
            ->where('owner_id', $driver->id)
            ->latest();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($filters['limit'] ?? 20);
    }

    /**
     * Approve cashout (admin)
     */
    public function approveCashout(int $entryId, int $approverId): LedgerEntry
    {
        return DB::transaction(function () use ($entryId, $approverId) {
            $entry = LedgerEntry::where('id', $entryId)
                ->where('type', LedgerEntry::TYPE_CASHOUT)
                ->where('status', LedgerEntry::STATUS_PENDING)
                ->lockForUpdate()
                ->firstOrFail();

            $entry->markApproved($approverId);
            $entry->markCompleted();

            event(new WalletEvent($entry, 'approved'));

            return $entry;
        });
    }

    /**
     * Reject cashout (admin)
     */
    public function rejectCashout(int $entryId, int $rejecterId, string $reason): LedgerEntry
    {
        return DB::transaction(function () use ($entryId, $rejecterId, $reason) {
            $entry = LedgerEntry::where('id', $entryId)
                ->where('type', LedgerEntry::TYPE_CASHOUT)
                ->where('status', LedgerEntry::STATUS_PENDING)
                ->lockForUpdate()
                ->firstOrFail();

            // Return amount to wallet
            $wallet = $this->getWalletByType($entry->wallet_type, $entry->wallet_id);
            $wallet->updateBalance($entry->amount, 'increment');

            $entry->markFailed($reason);
            $entry->update([
                'metadata' => array_merge($entry->metadata ?? [], [
                    'rejected_by' => $rejecterId,
                    'rejection_reason' => $reason
                ])
            ]);

            event(new WalletEvent($entry, 'rejected'));

            return $entry;
        });
    }

    /**
     * Implement abstract methods
     */
    protected function lockWallet($wallet)
    {
        return DriverWallet::where('id', $wallet->id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    protected function getWalletByType(string $walletType, int $walletId)
    {
        if ($walletType === 'driver') {
            return DriverWallet::findOrFail($walletId);
        }

        throw new \Exception('Unknown wallet type');
    }

    protected function getResourceByIdempotency(IdempotencyKey $key)
    {
        if ($key->resource_type === LedgerEntry::class) {
            return LedgerEntry::find($key->resource_id);
        }

        return null;
    }

    /**
     * Helper methods
     */
    private function validateCashout(DriverWallet $wallet, float $amount): void
    {
        if ($wallet->available_balance < $amount) {
            throw new \Exception('رصيد غير كافي');
        }

        if (!$wallet->canWithdrawToday($amount)) {
            throw new \Exception('تجاوز الحد اليومي للسحب');
        }

        $min = config('wallet.driver.min_cashout', 100);
        if ($amount < $min) {
            throw new \Exception("الحد الأدنى للسحب هو {$min}");
        }
    }
}
