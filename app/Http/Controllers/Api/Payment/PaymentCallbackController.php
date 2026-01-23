<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use App\Models\Wallet\UserWallet;
use App\Models\Wallet\LedgerEntry;
use App\Models\Wallet\IdempotencyKey;
use App\Services\Wallet\UserWalletService;
use App\Services\Payment\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentCallbackController extends Controller
{
    use ApiResponseTrait;

    private UserWalletService $userWalletService;
    private PaymobService $paymobService;

    public function __construct(
        UserWalletService $userWalletService,
        PaymobService $paymobService
    ) {
        $this->userWalletService = $userWalletService;
        $this->paymobService = $paymobService;
    }

    /**
     * Handle Paymob callback
     */
    public function handle(Request $request)
    {
        // 1. Log the incoming request for debugging
        $this->logCallbackRequest($request);

        // 2. Parse and validate callback data
        $callbackData = $request->all();

        // 3. Validate required fields
        if (!$this->validateCallbackData($callbackData)) {
            return $this->errorResponse('Invalid callback data', 400);
        }

        // 4. Process the callback مباشرة بدون idempotency للتجربة
        try {
            return $this->handleTransactionCallbackDirectly($callbackData);
        } catch (\Exception $e) {
            Log::error('Callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Processing failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle transaction callback مباشرة
     */
    private function handleTransactionCallbackDirectly(array $callbackData)
    {
        $transactionData = $callbackData['obj'];

        try {
            DB::beginTransaction();

            // Check if transaction is successful
            $isSuccess = $this->isTransactionSuccessful($transactionData);

            Log::debug('Transaction success check', [
                'success' => $isSuccess,
                'transaction_id' => $transactionData['id'] ?? null,
                'order_id' => $transactionData['order']['merchant_order_id'] ?? null
            ]);

            if ($isSuccess) {
                $result = $this->processSuccessfulPaymentDirectly($transactionData, $callbackData);

                DB::commit();

                Log::info('Payment processed successfully DIRECTLY', [
                    'transaction_id' => $transactionData['id'],
                    'amount' => $transactionData['amount_cents'] / 100,
                    'order_id' => $transactionData['order']['merchant_order_id']
                ]);

                return $this->successResponse($result, 'Payment processed successfully');
            } else {
                $result = $this->processFailedPaymentDirectly($transactionData, $callbackData);

                DB::commit();

                Log::warning('Payment failed', [
                    'transaction_id' => $transactionData['id'] ?? null,
                    'order_id' => $transactionData['order']['merchant_order_id'] ?? null
                ]);

                return $this->errorResponse('Payment failed', 400, $result);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Transaction callback processing failed', [
                'transaction_id' => $transactionData['id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Processing failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process successful payment مباشرة
     */
    private function processSuccessfulPaymentDirectly(array $transactionData, array $callbackData): array
    {
        $orderId = $transactionData['order']['merchant_order_id'];

        Log::debug('Processing successful payment DIRECTLY', ['order_id' => $orderId]);

        // Find the pending deposit transaction
        $pendingTransaction = LedgerEntry::where('reference', $orderId)
            ->where('type', LedgerEntry::TYPE_DEPOSIT_PENDING)
            ->where('status', LedgerEntry::STATUS_PENDING)
            ->first();

        if (!$pendingTransaction) {
            // ابحث بدون شرط النوع
            $pendingTransaction = LedgerEntry::where('reference', $orderId)
                ->where('status', LedgerEntry::STATUS_PENDING)
                ->first();
        }

        if (!$pendingTransaction) {
            // ابحث بأي حالة
            $pendingTransaction = LedgerEntry::where('reference', $orderId)->first();
        }

        if (!$pendingTransaction) {
            Log::error('Transaction not found at all', [
                'order_id' => $orderId,
                'all_with_reference' => LedgerEntry::where('reference', $orderId)->count()
            ]);
            throw new \Exception('Transaction not found for order: ' . $orderId);
        }

        Log::debug('Found transaction', [
            'transaction_id' => $pendingTransaction->id,
            'user_id' => $pendingTransaction->user_id,
            'wallet_id' => $pendingTransaction->wallet_id,
            'amount' => $pendingTransaction->amount,
            'status' => $pendingTransaction->status,
            'type' => $pendingTransaction->type
        ]);

        // إذا كانت المعاملة مكتملة بالفعل، لا تعيد معالجتها
        if ($pendingTransaction->status === LedgerEntry::STATUS_COMPLETED) {
            Log::info('Transaction already completed', [
                'transaction_id' => $pendingTransaction->id,
                'order_id' => $orderId
            ]);

            // إرجاع النتيجة بدون عمل أي شيء
            $wallet = UserWallet::find($pendingTransaction->wallet_id);
            return [
                'success' => true,
                'already_processed' => true,
                'transaction' => $pendingTransaction,
                'user_id' => $pendingTransaction->user_id,
                'amount' => $pendingTransaction->amount,
                'wallet_balance' => $wallet ? $wallet->balance : 0,
                'reference' => $pendingTransaction->reference
            ];
        }

        // Calculate amount (convert from cents)
        $amountCents = $transactionData['amount_cents'];
        $amount = $amountCents / 100; // 50000 سنت = 500 ريال
        $currency = $transactionData['currency'] ?? 'SAR';
        $transactionId = $transactionData['id'];

        Log::debug('Amount calculation', [
            'amount_cents' => $amountCents,
            'amount' => $amount,
            'pending_transaction_amount' => $pendingTransaction->amount
        ]);

        // Get wallet
        $wallet = UserWallet::find($pendingTransaction->wallet_id);
        if (!$wallet) {
            Log::error('Wallet not found', [
                'wallet_id' => $pendingTransaction->wallet_id,
                'user_id' => $pendingTransaction->user_id
            ]);
            throw new \Exception('Wallet not found');
        }

        Log::debug('Wallet found', [
            'wallet_id' => $wallet->id,
            'balance_before' => $wallet->balance,
            'available_balance_before' => $wallet->available_balance
        ]);

        // 1. Mark pending transaction as completed
        $pendingTransaction->update([
            'status' => LedgerEntry::STATUS_COMPLETED,
            'payment_transaction_id' => $transactionId,
            'metadata' => array_merge($pendingTransaction->metadata ?? [], [
                'confirmed_at' => now()->toIso8601String(),
                'callback_data' => $callbackData,
                'transaction_data' => $transactionData,
                'gateway' => 'paymob_ksa',
                'currency_charged' => $currency,
                'processed_by' => 'direct_callback'
            ]),
            'processed_at' => now()
        ]);

        Log::debug('Pending transaction marked as completed', [
            'pending_transaction_id' => $pendingTransaction->id
        ]);

        // 2. Create completed deposit entry
        $newBalance = $wallet->balance + $amount;
        $newAvailableBalance = $wallet->available_balance + $amount;

        $completedTransaction = LedgerEntry::create([
            'wallet_type' => 'user',
            'wallet_id' => $wallet->id,
            'owner_type' => LedgerEntry::OWNER_TYPE_USER,
            'owner_id' => $pendingTransaction->user_id,
            'type' => LedgerEntry::TYPE_DEPOSIT,
            'amount' => $amount,
            'balance_before' => $wallet->balance,
            'balance_after' => $newBalance,
            'available_balance_before' => $wallet->available_balance,
            'available_balance_after' => $newAvailableBalance,
            'payment_method' => $this->getPaymentMethod($transactionData),
            'payment_transaction_id' => $transactionId,
            'description' => 'إيداع ناجح عبر Paymob KSA',
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => $orderId . '-COMPLETED-' . Str::random(6),
            'metadata' => [
                'order_id' => $orderId,
                'gateway_transaction_id' => $transactionId,
                'payment_details' => $transactionData,
                'callback_data' => $callbackData,
                'gateway' => 'paymob_ksa',
                'currency_charged' => $currency,
                'amount_cents' => $amountCents,
                'related_pending_id' => $pendingTransaction->id,
                'processed_by' => 'direct_callback'
            ],
            'processed_at' => now()
        ]);

        Log::debug('Completed transaction created', [
            'completed_transaction_id' => $completedTransaction->id,
            'new_balance' => $newBalance,
            'new_available_balance' => $newAvailableBalance
        ]);

        // 3. Update wallet balance
        $wallet->update([
            'balance' => $newBalance,
            'available_balance' => $newAvailableBalance,
            'last_transaction_at' => now()
        ]);

        Log::debug('Wallet updated', [
            'balance_after' => $wallet->balance,
            'available_balance_after' => $wallet->available_balance
        ]);

        // 4. Optionally, update daily totals if you have this method
        try {
            if (method_exists($wallet, 'updateDailyTotals')) {
                $wallet->updateDailyTotals($amount, 'deposit');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update daily totals', ['error' => $e->getMessage()]);
        }

        return [
            'success' => true,
            'transaction' => $completedTransaction,
            'pending_transaction' => $pendingTransaction,
            'user_id' => $pendingTransaction->user_id,
            'amount' => $amount,
            'amount_cents' => $amountCents,
            'currency' => $currency,
            'wallet_balance_before' => $wallet->balance - $amount,
            'wallet_balance_after' => $wallet->balance,
            'available_balance_before' => $wallet->available_balance - $amount,
            'available_balance_after' => $wallet->available_balance,
            'reference' => $completedTransaction->reference,
            'gateway_transaction_id' => $transactionId,
            'gateway_order_id' => $transactionData['order']['id'] ?? null
        ];
    }

    /**
     * Process failed payment مباشرة
     */
    private function processFailedPaymentDirectly(array $transactionData, array $callbackData): array
    {
        $orderId = $transactionData['order']['merchant_order_id'] ?? null;

        if (!$orderId) {
            throw new \Exception('Order ID not found in failed payment data');
        }

        // Find the pending deposit transaction
        $pendingTransaction = LedgerEntry::where('reference', $orderId)
            ->where('type', LedgerEntry::TYPE_DEPOSIT_PENDING)
            ->where('status', LedgerEntry::STATUS_PENDING)
            ->first();

        if (!$pendingTransaction) {
            throw new \Exception('Pending transaction not found for order: ' . $orderId);
        }

        // Mark as failed
        $pendingTransaction->markFailed($this->getErrorMessage($transactionData));

        // Update metadata
        $pendingTransaction->update([
            'metadata' => array_merge($pendingTransaction->metadata ?? [], [
                'failed_at' => now()->toIso8601String(),
                'callback_data' => $callbackData,
                'transaction_data' => $transactionData,
                'gateway_transaction_id' => $transactionData['id'] ?? null
            ])
        ]);

        return [
            'success' => false,
            'transaction' => $pendingTransaction,
            'user_id' => $pendingTransaction->user_id,
            'error' => $this->getErrorMessage($transactionData),
            'reference' => $pendingTransaction->reference
        ];
    }

    /**
     * Validate callback data structure
     */
    private function validateCallbackData(array $data): bool
    {
        if (!isset($data['type']) || strtoupper($data['type']) !== 'TRANSACTION') {
            Log::error('Invalid callback type', ['type' => $data['type'] ?? 'none']);
            return false;
        }

        if (!isset($data['obj']) || !is_array($data['obj'])) {
            Log::error('Missing transaction object');
            return false;
        }

        $obj = $data['obj'];

        // تحقق من merchant_order_id أولاً
        if (!isset($obj['order']['merchant_order_id'])) {
            Log::error('Missing merchant_order_id', ['order_data' => $obj['order'] ?? []]);
            return false;
        }

        if (!isset($obj['id'], $obj['amount_cents'])) {
            Log::error('Missing required transaction fields', [
                'has_id' => isset($obj['id']),
                'has_amount_cents' => isset($obj['amount_cents']),
                'has_merchant_order_id' => isset($obj['order']['merchant_order_id'])
            ]);
            return false;
        }

        return true;
    }

    /**
     * Check if transaction is successful
     */
    private function isTransactionSuccessful(array $transactionData): bool
    {
        $success = $transactionData['success'] ?? false;
        $migsResult = $transactionData['data']['migs_result'] ?? null;
        $txnResponseCode = $transactionData['data']['txn_response_code'] ?? null;
        $acqResponseCode = $transactionData['data']['acq_response_code'] ?? null;
        $isVoided = $transactionData['is_voided'] ?? false;
        $isRefunded = $transactionData['is_refunded'] ?? false;

        Log::debug('Transaction status details', [
            'success' => $success,
            'migs_result' => $migsResult,
            'txn_response_code' => $txnResponseCode,
            'acq_response_code' => $acqResponseCode,
            'is_voided' => $isVoided,
            'is_refunded' => $isRefunded
        ]);

        return $success &&
            $migsResult === 'SUCCESS' &&
            $txnResponseCode === 'APPROVED' &&
            $acqResponseCode === '00' &&
            !$isVoided &&
            !$isRefunded;
    }

    /**
     * Get payment method
     */
    private function getPaymentMethod(array $transactionData): string
    {
        $sourceType = $transactionData['source_data']['type'] ?? null;

        if ($sourceType === 'card') {
            return $transactionData['source_data']['sub_type'] ?? 'credit_card';
        }

        return $transactionData['payment_method'] ?? 'unknown';
    }

    /**
     * Get error message
     */
    private function getErrorMessage(array $transactionData): string
    {
        $message = $transactionData['data']['message'] ?? null;
        if ($message && $message !== 'Approved') {
            return $message;
        }

        $migsResult = $transactionData['data']['migs_result'] ?? null;
        if ($migsResult && $migsResult !== 'SUCCESS') {
            return 'Payment failed: ' . $migsResult;
        }

        $txnResponseCode = $transactionData['data']['txn_response_code'] ?? null;
        if ($txnResponseCode && $txnResponseCode !== 'APPROVED') {
            return 'Transaction declined: ' . $txnResponseCode;
        }

        return 'Payment failed';
    }

    /**
     * Log callback request
     */
    private function logCallbackRequest(Request $request): void
    {
        $logData = [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $this->getFilteredHeaders($request->headers->all()),
            'query_params' => $request->query->all(),
            'body_params' => $this->getFilteredBodyParams($request->all()),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String()
        ];

        Log::debug('Payment callback received', $logData);
    }

    /**
     * Get filtered headers
     */
    private function getFilteredHeaders(array $headers): array
    {
        $sensitiveHeaders = ['authorization', 'cookie', 'x-hmac-signature'];
        $filtered = [];

        foreach ($headers as $key => $value) {
            $lowerKey = strtolower($key);
            if (in_array($lowerKey, $sensitiveHeaders)) {
                $filtered[$key] = '[REDACTED]';
            } else {
                $filtered[$key] = is_array($value) ? implode(', ', $value) : $value;
            }
        }

        return $filtered;
    }

    /**
     * Get filtered body params
     */
    private function getFilteredBodyParams(array $params): array
    {
        $sensitiveParams = ['card_number', 'card_cvv', 'card_expiry', 'token', 'hmac', 'pan'];
        $filtered = [];

        foreach ($params as $key => $value) {
            $lowerKey = strtolower($key);
            if (in_array($lowerKey, $sensitiveParams)) {
                $filtered[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $filtered[$key] = $this->getFilteredBodyParams($value);
            } else {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }
}
