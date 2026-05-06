<?php

namespace App\Http\Controllers\Api\Payment;

use App\Events\Order\TripStartedForDriver;
use App\Events\Order\TripStartedForUser;
use App\Http\Controllers\Controller;
use App\Models\Order; // تأكد من استيراد نموذج الطلب
use App\Models\Wallet\IdempotencyKey;
use App\Models\Wallet\LedgerEntry;
use App\Models\Wallet\UserWallet;
use App\Services\Payment\PaymobService;
use App\Services\Wallet\UserWalletService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentCallbackController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle Paymob callback
     */
    public function handle(Request $request)
    {
        try {
            Log::info('Paymob Callback Received', [
                'query_params' => $request->query(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'body_type' => $request->input('type'),
                'has_obj' => $request->has('obj'),
            ]);

            // 1. Log the incoming request for debugging
            $this->logCallbackRequest($request);

            // 2. Parse and validate callback data
            $callbackData = $request->all();

            // 3. Validate required fields
            if (!$this->validateCallbackData($callbackData)) {
                return $this->errorResponse('Invalid callback data', 400);
            }

            // 4. Process the callback
            return $this->handleTransactionCallback($callbackData);

        } catch (\Exception $e) {
            Log::error('Callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Processing failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle transaction callback with idempotency check
     */
    private function handleTransactionCallback(array $callbackData)
    {
        $transactionData = $callbackData['obj'];
        $transactionId = $transactionData['id'] ?? null;
        $orderId = $transactionData['order']['merchant_order_id'] ?? null;

        // تفادي المعالجة المزدوجة
        if ($transactionId && IdempotencyKey::where('key', 'paymob_callback_' . $transactionId)->exists()) {
            Log::info('Callback already processed (idempotent)', [
                'transaction_id' => $transactionId,
                'order_id' => $orderId
            ]);
            return $this->successResponse(['already_processed' => true], 'Callback already processed');
        }

        try {
            DB::beginTransaction();

            // تسجيل مفتاح idempotency
            if ($transactionId) {
                IdempotencyKey::create([
                    'key' => 'paymob_callback_' . $transactionId,
                    'value' => json_encode($callbackData),
                    'expires_at' => now()->addDays(30)
                ]);
            }

            // Check if transaction is successful
            $isSuccess = $this->isTransactionSuccessful($transactionData);

            Log::debug('Transaction success check', [
                'success' => $isSuccess,
                'transaction_id' => $transactionId,
                'order_id' => $orderId
            ]);

            if ($isSuccess) {
                $result = $this->processSuccessfulPayment($transactionData, $callbackData);
            } else {
                $result = $this->processFailedPayment($transactionData, $callbackData);
            }

            DB::commit();

            $message = $isSuccess ? 'Payment processed successfully' : 'Payment failed';
            Log::info($message, [
                'transaction_id' => $transactionId,
                'order_id' => $orderId,
                'amount' => ($transactionData['amount_cents'] ?? 0) / 100
            ]);

            return $isSuccess 
                ? $this->successResponse($result, $message)
                : $this->errorResponse($message, 400, $result);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // إزالة مفتاح idempotency في حالة الفشل
            if (isset($transactionId)) {
                IdempotencyKey::where('key', 'paymob_callback_' . $transactionId)->delete();
            }

            Log::error('Transaction callback processing failed', [
                'transaction_id' => $transactionId ?? null,
                'order_id' => $orderId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Processing failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process successful payment
     */
    private function processSuccessfulPayment(array $transactionData, array $callbackData): array
    {
        $orderId = $transactionData['order']['merchant_order_id'];
        $transactionId = $transactionData['id'];
        $amountCents = $transactionData['amount_cents'];
        $amount = $amountCents / 100;
        $currency = $transactionData['currency'] ?? 'SAR';

        Log::debug('Processing successful payment', [
            'order_id' => $orderId,
            'amount' => $amount,
            'currency' => $currency
        ]);

        // ========== الحالة الأولى: دفع طلب (Order Payment) ==========
        $order = Order::find($orderId);
        
        if ($order) {
            Log::info('Processing ORDER payment', [
                'order_id' => $order->id,
                'current_status' => $order->status,
                'amount' => $amount
            ]);
$orderStatus = \App\Models\OrderStatus::where('name', 'in-road')->first();

            // تحديث حالة الطلب إذا لم يكن مدفوع بالفعل
            if (!$order->isPaid()) {
                $oldStatus = $order->status;
                $order->update([
                    'order_status_id' => $orderStatus?->id, // أو استخدم الحالة المناسبة في تطبيقك
                    'payment_status' => 'paid',
                    'payment_method' => $this->getPaymentMethod($transactionData),
                    'payment_transaction_id' => $transactionId,
                    'paid_at' => now(),
                    'payment_details' => json_encode([
                        'gateway' => 'paymob_ksa',
                        'transaction_id' => $transactionId,
                        'amount_cents' => $amountCents,
                        'amount' => $amount,
                        'currency' => $currency,
                        'payment_method' => $this->getPaymentMethod($transactionData),
                        'callback_data' => $callbackData,
                        'received_at' => now()->toIso8601String()
                    ])
                ]);
$offer = $order->offers()
    ->whereIn('status', ['accepted', 'payment_pending'])
    ->first();

if ($offer) {
    $order->update([
        'driver_id' => $offer->driver_id
    ]);

    // update offers
    $order->offers()->update(['status' => 'rejected']);
    $offer->update(['status' => 'accepted']);

    event(new TripStartedForDriver($order));
    event(new TripStartedForUser($order));
}
                Log::info('Order payment completed', [
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $order->status,
                    'amount' => $amount
                ]);

                // يمكنك تفعيل أحداث أو إشعارات هنا
                // event(new OrderPaid($order));
            } else {
                Log::info('Order already paid', [
                    'order_id' => $order->id,
                    'status' => $order->status
                ]);
            }

            return [
                'success' => true,
                'type' => 'order_payment',
                'order_id' => $order->id,
                'amount' => $amount,
                'amount_cents' => $amountCents,
                'currency' => $currency,
                'transaction_id' => $transactionId,
                'order_status' => $order->status
            ];
        }

        // ========== الحالة الثانية: إيداع في المحفظة (Wallet Deposit) ==========
        Log::info('No Order found, treating as WALLET DEPOSIT', ['order_id' => $orderId]);

        return $this->processWalletDeposit($orderId, $transactionData, $callbackData);
    }

    /**
     * Process wallet deposit
     */
    private function processWalletDeposit(string $referenceId, array $transactionData, array $callbackData): array
    {
        // Find the pending deposit transaction
        $pendingTransaction = LedgerEntry::where('reference', $referenceId)
            ->where('type', LedgerEntry::TYPE_DEPOSIT_PENDING)
            ->where('status', LedgerEntry::STATUS_PENDING)
            ->first();

        if (!$pendingTransaction) {
            // Fallback: حاول البحث بدون شرط النوع
            $pendingTransaction = LedgerEntry::where('reference', $referenceId)
                ->where('status', LedgerEntry::STATUS_PENDING)
                ->first();
        }

        if (!$pendingTransaction) {
            Log::error('Pending deposit transaction not found', [
                'reference' => $referenceId,
                'ledger_entries_count' => LedgerEntry::where('reference', $referenceId)->count()
            ]);
            throw new \Exception('Pending deposit transaction not found for reference: ' . $referenceId);
        }

        Log::debug('Found pending deposit transaction', [
            'transaction_id' => $pendingTransaction->id,
            'user_id' => $pendingTransaction->user_id,
            'wallet_id' => $pendingTransaction->wallet_id,
            'amount' => $pendingTransaction->amount,
            'status' => $pendingTransaction->status
        ]);

        // إذا كانت المعاملة مكتملة بالفعل
        if ($pendingTransaction->status === LedgerEntry::STATUS_COMPLETED) {
            Log::info('Transaction already completed', [
                'transaction_id' => $pendingTransaction->id,
                'reference' => $referenceId
            ]);

            $wallet = UserWallet::find($pendingTransaction->wallet_id);
            return [
                'success' => true,
                'already_processed' => true,
                'type' => 'wallet_deposit',
                'transaction' => $pendingTransaction,
                'user_id' => $pendingTransaction->user_id,
                'amount' => $pendingTransaction->amount,
                'wallet_balance' => $wallet ? $wallet->balance : 0,
                'reference' => $pendingTransaction->reference
            ];
        }

        $amountCents = $transactionData['amount_cents'];
        $amount = $amountCents / 100;
        $currency = $transactionData['currency'] ?? 'SAR';
        $transactionId = $transactionData['id'];

        // Get wallet
        $wallet = UserWallet::find($pendingTransaction->wallet_id);
        if (!$wallet) {
            Log::error('Wallet not found', [
                'wallet_id' => $pendingTransaction->wallet_id,
                'user_id' => $pendingTransaction->user_id
            ]);
            throw new \Exception('Wallet not found');
        }

        // 1. Mark pending transaction as completed
        $pendingTransaction->update([
            'status' => LedgerEntry::STATUS_COMPLETED,
            'payment_transaction_id' => $transactionId,
            'metadata' => array_merge($pendingTransaction->metadata ?? [], [
                'confirmed_at' => now()->toIso8601String(),
                'callback_data' => $callbackData,
                'transaction_data' => $transactionData,
                'gateway' => 'paymob_ksa',
                'currency_charged' => $currency
            ]),
            'processed_at' => now()
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
            'reference' => $referenceId . '-COMPLETED-' . Str::random(6),
            'metadata' => [
                'order_id' => $referenceId,
                'gateway_transaction_id' => $transactionId,
                'payment_details' => $transactionData,
                'callback_data' => $callbackData,
                'gateway' => 'paymob_ksa',
                'currency_charged' => $currency,
                'amount_cents' => $amountCents,
                'related_pending_id' => $pendingTransaction->id
            ],
            'processed_at' => now()
        ]);

        // 3. Update wallet balance
        $wallet->update([
            'balance' => $newBalance,
            'available_balance' => $newAvailableBalance,
            'last_transaction_at' => now()
        ]);

        // 4. Update daily totals
        try {
            if (method_exists($wallet, 'updateDailyTotals')) {
                $wallet->updateDailyTotals($amount, 'deposit');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update daily totals', ['error' => $e->getMessage()]);
        }

        Log::info('Wallet deposit completed', [
            'user_id' => $pendingTransaction->user_id,
            'wallet_id' => $wallet->id,
            'old_balance' => $wallet->balance - $amount,
            'new_balance' => $wallet->balance,
            'amount' => $amount
        ]);

        return [
            'success' => true,
            'type' => 'wallet_deposit',
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
            'gateway_transaction_id' => $transactionId
        ];
    }

    /**
     * Process failed payment
     */
    private function processFailedPayment(array $transactionData, array $callbackData): array
    {
        $orderId = $transactionData['order']['merchant_order_id'] ?? null;
        $transactionId = $transactionData['id'] ?? null;
        $errorMessage = $this->getErrorMessage($transactionData);

        if (!$orderId) {
            throw new \Exception('Order ID not found in failed payment data');
        }

        Log::info('Processing failed payment', [
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'error' => $errorMessage
        ]);

        $result = [
            'success' => false,
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'error' => $errorMessage
        ];

        // ========== الحالة الأولى: الطلب (Order) ==========
        $order = Order::find($orderId);
        if ($order) {
            $order->update([
                'payment_status' => 'failed',
                'payment_data' => json_encode([
                    'gateway' => 'paymob_ksa',
                    'transaction_id' => $transactionId,
                    'error' => $errorMessage,
                    'callback_data' => $callbackData,
                    'received_at' => now()->toIso8601String()
                ])
            ]);

            $result['type'] = 'order_payment';
            $result['order_status'] = $order->status;
            return $result;
        }

        // ========== الحالة الثانية: المحفظة (Wallet Deposit) ==========
        $pendingTransaction = LedgerEntry::where('reference', $orderId)
            ->where('type', LedgerEntry::TYPE_DEPOSIT_PENDING)
            ->where('status', LedgerEntry::STATUS_PENDING)
            ->first();

        if ($pendingTransaction) {
            $pendingTransaction->markFailed($errorMessage);
            $pendingTransaction->update([
                'metadata' => array_merge($pendingTransaction->metadata ?? [], [
                    'failed_at' => now()->toIso8601String(),
                    'callback_data' => $callbackData,
                    'transaction_data' => $transactionData,
                    'gateway_transaction_id' => $transactionId
                ])
            ]);

            $result['type'] = 'wallet_deposit';
            $result['transaction'] = $pendingTransaction;
            $result['reference'] = $pendingTransaction->reference;
        }

        return $result;
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

        if (!isset($obj['order']['merchant_order_id'])) {
            Log::error('Missing merchant_order_id', ['order_data' => $obj['order'] ?? []]);
            return false;
        }

        if (!isset($obj['id'], $obj['amount_cents'])) {
            Log::error('Missing required transaction fields', [
                'has_id' => isset($obj['id']),
                'has_amount_cents' => isset($obj['amount_cents'])
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
            'body_type' => $request->input('type'),
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
}