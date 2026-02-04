<?php

namespace App\Services\Wallet;

use Illuminate\Support\Str;
use App\Models\Wallet\LedgerEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Wallet\AbstractWallet;

class LedgerService
{
    /**
     * Credit wallet
     */
    // public function credit(
    //     AbstractWallet $wallet,
    //     float $amount,
    //     string $type,
    //     string $description,
    //     array $metadata = [],
    //     array $ownerInfo = []
    // ): LedgerEntry {
    //     return DB::transaction(function () use ($wallet, $amount, $type, $description, $metadata, $ownerInfo) {
    //         $ledgerEntry = LedgerEntry::create([
    //             'wallet_type' => $wallet->getWalletType(),
    //             'wallet_id' => $wallet->id,
    //             'owner_type' => $ownerInfo['owner_type'] ?? LedgerEntry::OWNER_TYPE_SYSTEM,
    //             'owner_id' => $ownerInfo['owner_id'] ?? null,
    //             'type' => $type,
    //             'amount' => $amount,
    //             'balance_before' => $wallet->balance,
    //             'balance_after' => $wallet->balance + $amount,
    //             'available_balance_before' => $wallet->available_balance,
    //             'available_balance_after' => $wallet->available_balance + $amount,
    //             'description' => $description,
    //             'status' => LedgerEntry::STATUS_COMPLETED,
    //             'reference' => $this->generateReference($type),
    //             'metadata' => $metadata,
    //             'processed_at' => now()
    //         ]);

    //         $wallet->updateBalance($amount, 'increment');

    //         return $ledgerEntry;
    //     });
    // }
    // في AbstractWalletService أو LedgerService
    public function credit($wallet, float $amount, string $type, string $description, array $metadata = [], array $ownerData = []): ?LedgerEntry
    {
        Log::debug('Credit method called', [
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => $type,
            'description' => $description
        ]);

        try {
            // حساب الرصيد الجديد
            $newBalance = $wallet->balance + $amount;
            $newAvailableBalance = $wallet->available_balance + $amount;

            Log::debug('Balance calculations', [
                'old_balance' => $wallet->balance,
                'old_available_balance' => $wallet->available_balance,
                'new_balance' => $newBalance,
                'new_available_balance' => $newAvailableBalance
            ]);

            // إنشاء السجل
            $entry = LedgerEntry::create([
                'wallet_type' => $wallet->getMorphClass(),
                'wallet_id' => $wallet->id,
                'owner_type' => $ownerData['owner_type'] ?? null,
                'owner_id' => $ownerData['owner_id'] ?? null,
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $newBalance,
                'available_balance_before' => $wallet->available_balance,
                'available_balance_after' => $newAvailableBalance,
                'description' => $description,
                'status' => LedgerEntry::STATUS_COMPLETED,
                'reference' => $this->generateReference('DEP'),
                'metadata' => $metadata,
                'processed_at' => now(),
            ]);

            Log::debug('Ledger entry created', [
                'entry_id' => $entry->id,
                'reference' => $entry->reference
            ]);

            // تحديث محفظة
            $wallet->update([
                'balance' => $newBalance,
                'available_balance' => $newAvailableBalance,
                'last_transaction_at' => now(),
            ]);

            Log::debug('Wallet updated', [
                'new_balance' => $wallet->balance,
                'new_available_balance' => $wallet->available_balance
            ]);

            return $entry;
        } catch (\Exception $e) {
            Log::error('Error in credit method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Debit wallet
     */
    public function debit(
        AbstractWallet $wallet,
        float $amount,
        string $type,
        string $description,
        array $metadata = [],
        array $ownerInfo = []
    ): LedgerEntry {
        return DB::transaction(function () use ($wallet, $amount, $type, $description, $metadata, $ownerInfo) {
            // Validate available balance for certain debit types
            if (in_array($type, [
                LedgerEntry::TYPE_WITHDRAWAL,
                LedgerEntry::TYPE_CASHOUT,
                LedgerEntry::TYPE_TRANSFER_OUT,
                LedgerEntry::TYPE_PAYMENT
            ])) {
                if ($wallet->available_balance < $amount) {
                    throw new \Exception('رصيد غير كافي');
                }
            }

            $ledgerEntry = LedgerEntry::create([
                'wallet_type' => $wallet->getWalletType(),
                'wallet_id' => $wallet->id,
                'owner_type' => $ownerInfo['owner_type'] ?? LedgerEntry::OWNER_TYPE_SYSTEM,
                'owner_id' => $ownerInfo['owner_id'] ?? null,
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance - $amount,
                'available_balance_before' => $wallet->available_balance,
                'available_balance_after' => $wallet->available_balance - $amount,
                'description' => $description,
                'status' => LedgerEntry::STATUS_PROCESSING,
                'reference' => $this->generateReference($type),
                'metadata' => $metadata,
                'processed_at' => now()
            ]);

            $wallet->updateBalance($amount, 'decrement');

            return $ledgerEntry;
        });
    }

    /**
     * Generate unique reference
     */
    private function generateReference(string $type): string
    {
        $prefixes = [
            LedgerEntry::TYPE_DEPOSIT => 'DEP',
            LedgerEntry::TYPE_WITHDRAWAL => 'WTH',
            LedgerEntry::TYPE_CASHOUT => 'CASH',
            LedgerEntry::TYPE_TRANSFER_IN => 'TIN',
            LedgerEntry::TYPE_TRANSFER_OUT => 'TOUT',
            LedgerEntry::TYPE_EARNING => 'ERN',
            LedgerEntry::TYPE_COMMISSION => 'COM',
            LedgerEntry::TYPE_PAYMENT => 'PAY',
            LedgerEntry::TYPE_HOLD => 'HLD',
            LedgerEntry::TYPE_REFUND => 'REF',
            LedgerEntry::TYPE_FEE => 'FEE',
            LedgerEntry::TYPE_RELEASE => 'REL',
            LedgerEntry::TYPE_PAYOUT => 'POUT',
            LedgerEntry::TYPE_ADJUSTMENT => 'ADJ'
        ];

        $prefix = $prefixes[$type] ?? 'LED';

        return $prefix . '-' . now()->format('YmdHis') . '-' . Str::random(6);
    }

    /**
     * Get ledger summary
     */
    public function getLedgerSummary(AbstractWallet $wallet, array $filters = []): array
    {
        $query = $wallet->ledgerEntries()
            ->select(
                'type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('MIN(created_at) as first_transaction'),
                DB::raw('MAX(created_at) as last_transaction')
            )
            ->where('status', LedgerEntry::STATUS_COMPLETED);

        // Apply filters
        if (!empty($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        $summary = $query->groupBy('type')->get();

        return [
            'summary' => $summary,
            'wallet_balance' => $wallet->balance,
            'calculated_balance' => $wallet->calculateLedgerBalance(),
            'discrepancy' => abs($wallet->balance - $wallet->calculateLedgerBalance())
        ];
    }
}
