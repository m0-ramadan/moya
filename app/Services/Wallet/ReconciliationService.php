<?php

namespace App\Services\Wallet;

use App\Models\Wallet\AbstractWallet;
use App\Models\Wallet\LedgerEntry;
use Illuminate\Support\Facades\Log;

class ReconciliationService
{
    private LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Reconcile wallet
     */
    public function reconcile(AbstractWallet $wallet): array
    {
        $calculatedBalance = $wallet->calculateLedgerBalance();
        $difference = $wallet->balance - $calculatedBalance;
        $threshold = config('wallet.reconciliation.threshold', 0.01);

        if (abs($difference) <= $threshold) {
            return [
                'reconciled' => false,
                'difference' => $difference,
                'within_threshold' => true
            ];
        }

        // Create adjustment entry
        if ($difference > 0) {
            // Wallet has more than ledger - debit the difference
            $entry = $this->ledgerService->debit(
                $wallet,
                abs($difference),
                LedgerEntry::TYPE_ADJUSTMENT,
                'تعديل رصيد - تصحيح نظام (فائض)',
                ['reconciliation' => true, 'difference' => $difference]
            );
            $action = 'debited';
        } else {
            // Wallet has less than ledger - credit the difference
            $entry = $this->ledgerService->credit(
                $wallet,
                abs($difference),
                LedgerEntry::TYPE_ADJUSTMENT,
                'تعديل رصيد - تصحيح نظام (نقص)',
                ['reconciliation' => true, 'difference' => $difference]
            );
            $action = 'credited';
        }

        Log::warning('Wallet reconciled', [
            'wallet_id' => $wallet->id,
            'wallet_type' => $wallet->getWalletType(),
            'old_balance' => $wallet->balance,
            'calculated_balance' => $calculatedBalance,
            'difference' => $difference,
            'action' => $action,
            'entry_id' => $entry->id
        ]);

        return [
            'reconciled' => true,
            'difference' => $difference,
            'action' => $action,
            'entry_id' => $entry->id,
            'new_balance' => $wallet->fresh()->balance
        ];
    }

    /**
     * Reconcile multiple wallets
     */
    public function reconcileBatch(array $wallets): array
    {
        $results = [
            'total' => count($wallets),
            'reconciled' => 0,
            'skipped' => 0,
            'errors' => 0,
            'details' => []
        ];

        foreach ($wallets as $wallet) {
            try {
                $result = $this->reconcile($wallet);

                $results['details'][] = [
                    'wallet_id' => $wallet->id,
                    'wallet_type' => $wallet->getWalletType(),
                    'reconciled' => $result['reconciled'],
                    'difference' => $result['difference'],
                    'action' => $result['action'] ?? null
                ];

                if ($result['reconciled']) {
                    $results['reconciled']++;
                } else {
                    $results['skipped']++;
                }
            } catch (\Exception $e) {
                $results['errors']++;
                $results['details'][] = [
                    'wallet_id' => $wallet->id,
                    'wallet_type' => $wallet->getWalletType(),
                    'error' => $e->getMessage()
                ];

                Log::error('Wallet reconciliation failed', [
                    'wallet_id' => $wallet->id,
                    'wallet_type' => $wallet->getWalletType(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Find discrepancies
     */
    public function findDiscrepancies(array $wallets): array
    {
        $discrepancies = [];

        foreach ($wallets as $wallet) {
            $calculatedBalance = $wallet->calculateLedgerBalance();
            $difference = abs($wallet->balance - $calculatedBalance);
            $threshold = config('wallet.reconciliation.threshold', 0.01);

            if ($difference > $threshold) {
                $discrepancies[] = [
                    'wallet_id' => $wallet->id,
                    'wallet_type' => $wallet->getWalletType(),
                    'balance' => $wallet->balance,
                    'calculated_balance' => $calculatedBalance,
                    'difference' => $wallet->balance - $calculatedBalance,
                    'absolute_difference' => $difference,
                    'exceeds_threshold' => true
                ];
            }
        }

        return $discrepancies;
    }
}
