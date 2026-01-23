<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Events\WalletEvent;
use App\Models\Wallet\UserWallet;
use App\Models\Wallet\LedgerEntry;
use Illuminate\Support\Facades\Log;
use App\Models\Wallet\IdempotencyKey;
use App\Services\Payment\Wallet\PaymobService;
use App\Services\Security\FraudDetector;

class UserWalletService extends AbstractWalletService
{
    private PaymobService $paymobService;

    public function __construct(
        LedgerService $ledgerService,
        FraudDetector $fraudDetector,
        PaymobService $paymobService
    ) {
        parent::__construct($ledgerService, $fraudDetector);
        $this->paymobService = $paymobService;
    }

    /**
     * Get user wallet balance
     */
    public function getBalance(User $user): array
    {
        $wallet = $user->userWallet ?? $user->createUserWallet();

        return [
            'wallet_id' => $wallet->id,
            'balance' => $wallet->balance,
            'available_balance' => $wallet->available_balance,
            'held_balance' => $wallet->held_balance,
            'currency' => $wallet->currency,
            'status' => $wallet->status,
            'daily_limit' => $wallet->daily_limit,
            'total_deposits_today' => $wallet->total_deposits_today,
            'total_withdrawals_today' => $wallet->total_withdrawals_today,
            'last_transaction_at' => $wallet->last_transaction_at,
        ];
    }

    /**
     * Initiate deposit
     */
    public function initiateDeposit(User $user, float $amount, array $data = []): array
    {
        $wallet = $user->userWallet ?? $user->createUserWallet();

        // Validate amount
        $this->validateDepositAmount($amount);

        // Create pending deposit entry
        $orderId = $this->generateReference('DEP');
        $paymentMethod = $data['payment_method'] ?? 'paymob';

        $pendingEntry = LedgerEntry::create([
            'wallet_type' => 'user',
            'wallet_id' => $wallet->id,
            'owner_type' => LedgerEntry::OWNER_TYPE_USER,
            'owner_id' => $user->id,
            'type' => LedgerEntry::TYPE_DEPOSIT_PENDING,
            'amount' => $amount,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance,
            'description' => 'إيداع معلق - بانتظار التأكيد',
            'status' => LedgerEntry::STATUS_PENDING,
            'reference' => $orderId,
            'metadata' => array_merge([
                'user_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'payment_method' => $paymentMethod,
            ], $data),
            'expires_at' => now()->addHours(24),
        ]);

        // Get payment URL
        // في دالة initiateDeposit
        $paymentData = $this->paymobService->createPaymentOrder([
            'user' => $user,
            'amount' => $amount,
            'wallet_currency' => $wallet->currency,
            'order_id' => $orderId,
            'callback_url' => route('paymob.webhook'),
        ]);
        if (! $paymentData['success']) {
            $pendingEntry->markFailed($paymentData['error']);
            throw new \Exception($paymentData['error']);
        }

        return [
            'success' => true,
            'payment_url' => $paymentData['payment_url'],
            'order_id' => $orderId,
            'entry_id' => $pendingEntry->id,
            'amount' => $amount,
            'currency' => $wallet->currency,
            'expires_at' => $paymentData['expires_at'] ?? null,
        ];
    }

    /**
     * Confirm deposit
     */

    public function confirmDeposit(string $paymentTransactionId, array $details = []): ?LedgerEntry
    {
        Log::debug('confirmDeposit called', [
            'paymentTransactionId' => $paymentTransactionId,
            'details' => $details,
            'order_id' => $details['order_id'] ?? 'none',
            'user_id' => $details['user_id'] ?? 'none'
        ]);

        $requestData = array_merge($details, [
            'payment_transaction_id' => $paymentTransactionId,
        ]);

        return $this->processWithIdempotency(
            'deposit_' . $paymentTransactionId,
            $requestData,
            function () use ($paymentTransactionId, $details) {
                Log::debug('confirmDeposit - Inside process function', [
                    'order_id' => $details['order_id'] ?? 'none'
                ]);

                // Find pending deposit
                $pendingEntry = LedgerEntry::where('reference', $details['order_id'])
                    ->where('type', LedgerEntry::TYPE_DEPOSIT_PENDING)
                    ->where('status', LedgerEntry::STATUS_PENDING)
                    ->where('wallet_type', 'user')
                    ->lockForUpdate()
                    ->first();

                if (!$pendingEntry) {
                    Log::error('Pending entry not found', [
                        'order_id' => $details['order_id'] ?? 'none',
                        'user_id' => $details['user_id'] ?? 'none'
                    ]);
                    throw new \Exception('Pending transaction not found');
                }

                Log::debug('Found pending entry', [
                    'pending_entry_id' => $pendingEntry->id,
                    'amount' => $pendingEntry->amount,
                    'user_id' => $pendingEntry->user_id,
                    'wallet_id' => $pendingEntry->wallet_id
                ]);

                // Get wallet
                $wallet = $this->lockWallet(UserWallet::findOrFail($pendingEntry->wallet_id));

                Log::debug('Found wallet', [
                    'wallet_id' => $wallet->id,
                    'balance' => $wallet->balance
                ]);

                // Fraud check
                $this->validateDepositFraud($wallet, $pendingEntry->amount, $details);

                // Update pending entry
                $pendingEntry->markCompleted([
                    'payment_transaction_id' => $paymentTransactionId,
                    'metadata' => array_merge($pendingEntry->metadata ?? [], [
                        'confirmed_at' => now(),
                        'exchange_rate' => $details['exchange_rate'] ?? 1,
                        'currency_charged' => $details['currency_charged'] ?? $wallet->currency,
                    ]),
                ]);

                Log::debug('Pending entry marked as completed', [
                    'pending_entry_id' => $pendingEntry->id
                ]);

                // Create completed deposit entry
                $completedEntry = $this->credit(
                    $wallet,
                    $pendingEntry->amount,
                    LedgerEntry::TYPE_DEPOSIT,
                    'إيداع ناجح',
                    [
                        'payment_transaction_id' => $paymentTransactionId,
                        'order_id' => $details['order_id'],
                        'exchange_rate' => $details['exchange_rate'] ?? 1,
                    ],
                    [
                        'owner_type' => LedgerEntry::OWNER_TYPE_USER,
                        'owner_id' => $pendingEntry->owner_id,
                    ]
                );

                if (!$completedEntry) {
                    Log::error('Credit method returned null');
                    throw new \Exception('Failed to create completed deposit entry');
                }

                Log::debug('Completed entry created', [
                    'completed_entry_id' => $completedEntry->id,
                    'amount' => $completedEntry->amount,
                    'reference' => $completedEntry->reference
                ]);

                // Update daily totals
                $wallet->updateDailyTotals($pendingEntry->amount, 'deposit');

                // Fire event
                event(new WalletEvent($completedEntry));

                return $completedEntry;
            },
            LedgerEntry::OWNER_TYPE_USER,
            $details['user_id'] ?? null
        );
    }

    /**
     * Withdraw funds
     */
    public function withdraw(User $user, float $amount, array $data = []): LedgerEntry
    {
        $wallet = $user->userWallet ?? $user->createUserWallet();

        // Validate
        $this->validateWithdrawal($wallet, $amount);

        return $this->processWithIdempotency(
            'withdrawal_' . $user->id . '_' . md5($amount . now()->toISOString()),
            ['user_id' => $user->id, 'amount' => $amount, 'data' => $data],
            function () use ($wallet, $amount, $user, $data) {
                $lockedWallet = $this->lockWallet($wallet);

                $entry = $this->debit(
                    $lockedWallet,
                    $amount,
                    LedgerEntry::TYPE_WITHDRAWAL,
                    $data['description'] ?? 'سحب رصيد',
                    [
                        'withdrawal_method' => $data['withdrawal_method'] ?? 'bank_transfer',
                        'bank_account_id' => $data['bank_account_id'] ?? null,
                    ],
                    [
                        'owner_type' => LedgerEntry::OWNER_TYPE_USER,
                        'owner_id' => $user->id,
                    ]
                );

                // Update daily totals
                $lockedWallet->updateDailyTotals($amount, 'withdrawal');

                // Fire event
                event(new WalletEvent($entry));

                return $entry;
            },
            LedgerEntry::OWNER_TYPE_USER,
            $user->id
        );
    }

    /**
     * Transfer funds
     */
    public function transfer(User $fromUser, $toOwner, float $amount, array $data = []): array
    {
        $fromWallet = $fromUser->userWallet ?? $fromUser->createUserWallet();

        // Validate receiver
        [$toWallet, $toOwnerType, $toOwnerId] = $this->validateTransferReceiver($toOwner);

        // Generate transfer ID
        $transferId = $this->generateReference('TRF');

        return $this->processWithIdempotency(
            'transfer_' . $transferId,
            [
                'from_user_id' => $fromUser->id,
                'to_owner_type' => $toOwnerType,
                'to_owner_id' => $toOwnerId,
                'amount' => $amount,
            ],
            function () use ($fromWallet, $toWallet, $amount, $transferId, $fromUser, $toOwnerType, $toOwnerId, $data) {
                // Lock both wallets
                $lockedFromWallet = $this->lockWallet($fromWallet);
                $lockedToWallet = $this->lockWallet($toWallet);

                // Validate balance
                if ($lockedFromWallet->available_balance < $amount) {
                    throw new \Exception('رصيد غير كافي');
                }

                // Debit from sender
                $debitEntry = $this->debit(
                    $lockedFromWallet,
                    $amount,
                    LedgerEntry::TYPE_TRANSFER_OUT,
                    $data['description'] ?? 'تحويل أموال',
                    [
                        'transfer_id' => $transferId,
                        'to_owner_type' => $toOwnerType,
                        'to_owner_id' => $toOwnerId,
                    ],
                    [
                        'owner_type' => LedgerEntry::OWNER_TYPE_USER,
                        'owner_id' => $fromUser->id,
                    ]
                );

                // Credit to receiver
                $creditEntry = $this->credit(
                    $lockedToWallet,
                    $amount,
                    LedgerEntry::TYPE_TRANSFER_IN,
                    $data['description'] ?? 'استلام تحويل',
                    [
                        'transfer_id' => $transferId,
                        'from_owner_type' => LedgerEntry::OWNER_TYPE_USER,
                        'from_owner_id' => $fromUser->id,
                    ],
                    [
                        'owner_type' => $toOwnerType,
                        'owner_id' => $toOwnerId,
                    ]
                );

                // Link entries
                $debitEntry->update(['related_entry_id' => $creditEntry->id]);
                $creditEntry->update(['related_entry_id' => $debitEntry->id]);

                // Update daily totals
                $lockedFromWallet->updateDailyTotals($amount, 'transfer_out');

                // Fire events
                event(new WalletEvent($debitEntry));
                event(new WalletEvent($creditEntry));

                return [
                    'debit_entry' => $debitEntry,
                    'credit_entry' => $creditEntry,
                    'transfer_id' => $transferId,
                ];
            },
            LedgerEntry::OWNER_TYPE_USER,
            $fromUser->id
        );
    }

    /**
     * Hold amount for order
     */
    public function holdForOrder(User $user, float $amount, string $orderId, array $data = []): LedgerEntry
    {
        $wallet = $user->userWallet ?? $user->createUserWallet();

        return $this->hold(
            $wallet,
            $amount,
            $data['description'] ?? 'حجز للطلب #' . $orderId,
            $orderId,
            ['order_id' => $orderId],
            [
                'owner_type' => LedgerEntry::OWNER_TYPE_USER,
                'owner_id' => $user->id,
            ],
            $data['expires_in_hours'] ?? 24
        );
    }

    /**
     * Get transaction history
     */
    public function getTransactionHistory(User $user, array $filters = [])
    {
        $query = LedgerEntry::where('owner_type', LedgerEntry::OWNER_TYPE_USER)
            ->where('owner_id', $user->id)
            ->latest();

        return $this->applyLedgerFilters($query, $filters)->paginate(
            $filters['limit'] ?? 20
        );
    }

    /**
     * Implement abstract methods
     */
    protected function lockWallet($wallet)
    {
        return UserWallet::where('id', $wallet->id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    protected function getWalletByType(string $walletType, int $walletId)
    {
        if ($walletType === 'user') {
            return UserWallet::findOrFail($walletId);
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
    private function validateDepositAmount(float $amount): void
    {
        $min = config('wallet.user.min_deposit', 10);
        $max = config('wallet.user.max_deposit', 50000);

        if ($amount < $min || $amount > $max) {
            throw new \Exception("المبلغ يجب أن يكون بين {$min} و {$max}");
        }
    }

    private function validateWithdrawal(UserWallet $wallet, float $amount): void
    {
        if ($wallet->available_balance < $amount) {
            throw new \Exception('رصيد غير كافي');
        }

        if (! $wallet->canWithdrawToday($amount)) {
            throw new \Exception('تجاوز الحد اليومي للسحب');
        }

        $min = config('wallet.user.min_withdrawal', 50);
        if ($amount < $min) {
            throw new \Exception("الحد الأدنى للسحب هو {$min}");
        }
    }

    private function validateDepositFraud(UserWallet $wallet, float $amount, array $details): void
    {
        if (! $this->fraudDetector->validateDeposit($wallet, $amount, 'paymob', $details)) {
            throw new \Exception('فشل في التحقق من الأمان');
        }
    }

    private function validateTransferReceiver($toOwner): array
    {
        if ($toOwner instanceof User) {
            $toWallet = $toOwner->userWallet ?? $toOwner->createUserWallet();

            return [$toWallet, LedgerEntry::OWNER_TYPE_USER, $toOwner->id];
        }

        if ($toOwner instanceof \App\Models\Driver) {
            $toWallet = $toOwner->driverWallet ?? $toOwner->createDriverWallet();

            return [$toWallet, LedgerEntry::OWNER_TYPE_DRIVER, $toOwner->id];
        }

        throw new \Exception('نوع المستلم غير معروف');
    }

    private function applyLedgerFilters($query, array $filters)
    {
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date'] . ' 23:59:59');
        }

        return $query;
    }

    /**
     * Deposit amount to wallet (Helper method)
     */
    public function deposit(User $user, float $amount, array $data = []): LedgerEntry
    {
        $wallet = $user->userWallet ?? $user->createUserWallet();

        return $this->processWithIdempotency(
            'deposit_' . $user->id . '_' . md5($amount . now()->toISOString()),
            ['user_id' => $user->id, 'amount' => $amount, 'data' => $data],
            function () use ($wallet, $amount, $user, $data) {
                $lockedWallet = $this->lockWallet($wallet);

                $entry = $this->credit(
                    $lockedWallet,
                    $amount,
                    LedgerEntry::TYPE_DEPOSIT,
                    $data['description'] ?? 'إيداع رصيد',
                    array_merge([
                        'deposit_method' => $data['method'] ?? 'manual',
                        'reference_number' => $data['reference'] ?? null,
                    ], $data['metadata'] ?? []),
                    [
                        'owner_type' => LedgerEntry::OWNER_TYPE_USER,
                        'owner_id' => $user->id,
                    ]
                );

                // Update daily totals
                $lockedWallet->updateDailyTotals($amount, 'deposit');

                // Fire event
                event(new WalletEvent($entry));

                return $entry;
            },
            LedgerEntry::OWNER_TYPE_USER,
            $user->id
        );
    }
}
