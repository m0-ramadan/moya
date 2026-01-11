<?php

namespace App\Services\Withdrawal;

use App\Models\Wallet\LedgerEntry;
use App\Models\Wallet\AbstractWallet;

class WithdrawalValidator
{
    /**
     * Validate withdrawal entry
     */
    public function validate(LedgerEntry $entry, array $data = []): void
    {
        // Validate entry type
        if (!in_array($entry->type, [LedgerEntry::TYPE_WITHDRAWAL, LedgerEntry::TYPE_CASHOUT])) {
            throw new \Exception('Invalid withdrawal type');
        }

        // Validate status
        if ($entry->status !== LedgerEntry::STATUS_PENDING) {
            throw new \Exception('Withdrawal already processed');
        }

        // Validate amount
        $this->validateAmount($entry->amount, $entry->owner_type);

        // Validate frequency
        $this->validateFrequency($entry);

        // Validate business rules
        $this->validateBusinessRules($entry, $data);
    }

    /**
     * Validate amount
     */
    private function validateAmount(float $amount, string $ownerType): void
    {
        $configKey = $ownerType === LedgerEntry::OWNER_TYPE_USER ? 'user' : 'driver';

        $min = config("wallet.{$configKey}.min_withdrawal", 50);
        $max = config("wallet.{$configKey}.max_withdrawal", 5000);

        if ($amount < $min) {
            throw new \Exception("الحد الأدنى للسحب هو {$min}");
        }

        if ($amount > $max) {
            throw new \Exception("الحد الأقصى للسحب هو {$max}");
        }
    }

    /**
     * Validate withdrawal frequency
     */
    private function validateFrequency(LedgerEntry $entry): void
    {
        $recentWithdrawals = LedgerEntry::where('owner_type', $entry->owner_type)
            ->where('owner_id', $entry->owner_id)
            ->whereIn('type', [LedgerEntry::TYPE_WITHDRAWAL, LedgerEntry::TYPE_CASHOUT])
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        $maxDaily = config('wallet.max_daily_withdrawals', 5);

        if ($recentWithdrawals >= $maxDaily) {
            throw new \Exception("تجاوز الحد اليومي لطلبات السحب");
        }
    }

    /**
     * Validate business rules
     */
    private function validateBusinessRules(LedgerEntry $entry, array $data): void
    {
        $wallet = $this->getWalletForEntry($entry);

        // Check daily limit
        if (!$this->checkDailyLimit($wallet, $entry->amount)) {
            throw new \Exception('تجاوز الحد اليومي للسحب');
        }

        // Check minimum balance after withdrawal
        $minimumBalance = config('wallet.minimum_balance_after_withdrawal', 0);
        $balanceAfter = $wallet->available_balance - $entry->amount;

        if ($balanceAfter < $minimumBalance) {
            throw new \Exception('يجب الحفاظ على رصيد أدنى بعد السحب');
        }

        // Validate bank account if provided
        if (!empty($data['bank_account_id'])) {
            $this->validateBankAccount($data['bank_account_id'], $entry->owner_type, $entry->owner_id);
        }
    }

    /**
     * Check daily limit
     */
    private function checkDailyLimit(AbstractWallet $wallet, float $amount): bool
    {
        return $wallet->canWithdrawToday($amount);
    }

    /**
     * Validate bank account
     */
    private function validateBankAccount(int $bankAccountId, string $ownerType, int $ownerId): void
    {
        // Validate that bank account belongs to owner
        // This is a placeholder - implement based on your schema
        $isValid = true; // Replace with actual validation

        if (!$isValid) {
            throw new \Exception('حساب البنك غير صالح أو لا ينتمي للمستخدم');
        }
    }

    /**
     * Get wallet for entry
     */
    private function getWalletForEntry(LedgerEntry $entry): AbstractWallet
    {
        if ($entry->wallet_type === 'user') {
            return \App\Models\Wallet\UserWallet::findOrFail($entry->wallet_id);
        } elseif ($entry->wallet_type === 'driver') {
            return \App\Models\Wallet\DriverWallet::findOrFail($entry->wallet_id);
        }

        throw new \Exception('Unknown wallet type');
    }
}
