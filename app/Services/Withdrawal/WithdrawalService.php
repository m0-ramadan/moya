<?php

namespace App\Services\Withdrawal;

use App\Models\Wallet\LedgerEntry;
use App\Models\Wallet\IdempotencyKey;
use App\Services\Security\FraudDetector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalService
{
    private WithdrawalValidator $validator;
    private FraudDetector $fraudDetector;

    public function __construct(
        WithdrawalValidator $validator,
        FraudDetector $fraudDetector
    ) {
        $this->validator = $validator;
        $this->fraudDetector = $fraudDetector;
    }

    /**
     * Process withdrawal
     */
    public function process(LedgerEntry $entry, array $data = []): LedgerEntry
    {
        // Validate withdrawal
        $this->validator->validate($entry, $data);

        // Check for fraud
        if (!$this->fraudDetector->validateWithdrawal($entry, $data)) {
            throw new \Exception('Withdrawal validation failed');
        }

        return DB::transaction(function () use ($entry, $data) {
            // Mark as processing
            $entry->markProcessing();

            // Process through payout gateway
            $payoutResult = $this->processPayout($entry, $data);

            if ($payoutResult['success']) {
                $entry->markCompleted([
                    'payout_transaction_id' => $payoutResult['transaction_id'],
                    'metadata' => array_merge($entry->metadata ?? [], [
                        'payout_data' => $payoutResult['data'],
                        'processed_at' => now()->toIso8601String()
                    ])
                ]);
            } else {
                $entry->markFailed($payoutResult['error']);

                // Refund amount if payout failed
                $this->refundFailedWithdrawal($entry);
            }

            return $entry;
        });
    }

    /**
     * Batch process withdrawals
     */
    public function processBatch(array $entryIds, array $options = []): array
    {
        $results = [
            'total' => count($entryIds),
            'processed' => 0,
            'failed' => 0,
            'details' => []
        ];

        foreach ($entryIds as $entryId) {
            try {
                $entry = LedgerEntry::findOrFail($entryId);

                if (
                    $entry->type !== LedgerEntry::TYPE_WITHDRAWAL &&
                    $entry->type !== LedgerEntry::TYPE_CASHOUT
                ) {
                    throw new \Exception('Invalid entry type');
                }

                if ($entry->status !== LedgerEntry::STATUS_PENDING) {
                    throw new \Exception('Entry already processed');
                }

                $processedEntry = $this->process($entry, $options);

                $results['details'][] = [
                    'entry_id' => $entryId,
                    'success' => true,
                    'status' => $processedEntry->status
                ];
                $results['processed']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'entry_id' => $entryId,
                    'success' => false,
                    'error' => $e->getMessage()
                ];

                Log::error('Batch withdrawal processing failed', [
                    'entry_id' => $entryId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Process payout through gateway
     */
    private function processPayout(LedgerEntry $entry, array $data): array
    {
        $gateway = app(PayoutGatewayInterface::class);

        try {
            $payoutData = [
                'amount' => $entry->amount,
                'currency' => $data['currency'] ?? 'SAR',
                'recipient' => $this->getRecipientInfo($entry, $data),
                'description' => $entry->description,
                'reference' => $entry->reference,
                'metadata' => array_merge($entry->metadata ?? [], $data)
            ];

            return $gateway->processPayout($payoutData);
        } catch (\Exception $e) {
            Log::error('Payout processing failed', [
                'entry_id' => $entry->id,
                'amount' => $entry->amount,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get recipient information
     */
    private function getRecipientInfo(LedgerEntry $entry, array $data): array
    {
        $owner = $entry->owner;

        $info = [
            'owner_type' => $entry->owner_type,
            'owner_id' => $entry->owner_id,
            'amount' => $entry->amount
        ];

        if ($entry->owner_type === LedgerEntry::OWNER_TYPE_USER) {
            $info['name'] = $owner->name ?? '';
            $info['phone'] = $owner->phone ?? '';
            $info['email'] = $owner->email ?? '';
        } elseif ($entry->owner_type === LedgerEntry::OWNER_TYPE_DRIVER) {
            $info['name'] = $owner->full_name ?? '';
            $info['phone'] = $owner->phone_number ?? '';
            $info['email'] = $owner->user->email ?? '';
        }

        // Add bank account info if available
        if (!empty($data['bank_account_id'])) {
            $info['bank_account'] = $this->getBankAccountInfo($data['bank_account_id']);
        }

        return $info;
    }

    /**
     * Get bank account info
     */
    private function getBankAccountInfo(int $bankAccountId): array
    {
        // Fetch from bank accounts table
        // This is a placeholder - implement based on your schema
        return [
            'id' => $bankAccountId,
            'bank_name' => 'مصرف الراجحي',
            'account_number' => '******',
            'iban' => 'SA***********'
        ];
    }

    /**
     * Refund failed withdrawal
     */
    private function refundFailedWithdrawal(LedgerEntry $entry): void
    {
        try {
            $wallet = $this->getWalletForEntry($entry);

            // Credit back the amount
            $wallet->updateBalance($entry->amount, 'increment');

            Log::info('Withdrawal refunded', [
                'entry_id' => $entry->id,
                'amount' => $entry->amount,
                'wallet_id' => $wallet->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to refund withdrawal', [
                'entry_id' => $entry->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get wallet for ledger entry
     */
    private function getWalletForEntry(LedgerEntry $entry)
    {
        if ($entry->wallet_type === 'user') {
            return \App\Models\Wallet\UserWallet::find($entry->wallet_id);
        } elseif ($entry->wallet_type === 'driver') {
            return \App\Models\Wallet\DriverWallet::find($entry->wallet_id);
        }

        throw new \Exception('Unknown wallet type');
    }
}
