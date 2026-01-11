<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Wallet\UserWallet;
use App\Models\Wallet\LedgerEntry;
use App\Models\Wallet\IdempotencyKey;
use App\Services\Wallet\UserWalletService;
use App\Services\Payment\PaymobService;
use App\Services\Security\IpWhitelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentCallbackController extends Controller
{
    private UserWalletService $userWalletService;
    private PaymobService $paymobService;
    private IpWhitelist $ipWhitelist;

    public function __construct(
        UserWalletService $userWalletService,
        PaymobService $paymobService,
        IpWhitelist $ipWhitelist
    ) {
        $this->userWalletService = $userWalletService;
        $this->paymobService = $paymobService;
        $this->ipWhitelist = $ipWhitelist;
    }

    /**
     * Handle Paymob callback
     */
    public function handle(Request $request)
    {
        // 1. Log the incoming request for debugging
        $this->logCallbackRequest($request);

        // 2. Validate IP address (Paymob IPs only)
        if (!$this->validateIpAddress($request)) {
            return $this->errorResponse('Unauthorized IP address', 403);
        }

        // 3. Validate HMAC signature
        if (!$this->validateHmacSignature($request)) {
            return $this->errorResponse('Invalid HMAC signature', 400);
        }

        // 4. Parse and validate callback data
        $callbackData = $this->parseCallbackData($request);
        if (!$callbackData['valid']) {
            return $this->errorResponse($callbackData['error'], 400);
        }

        // 5. Generate idempotency key
        $idempotencyKey = $this->generateIdempotencyKey($callbackData);

        // 6. Check if already processed
        if ($this->isAlreadyProcessed($idempotencyKey)) {
            Log::info('Callback already processed', [
                'transaction_id' => $callbackData['transaction_id'],
                'order_id' => $callbackData['order_id']
            ]);

            return $this->successResponse('Transaction already processed');
        }

        // 7. Process the callback based on type
        try {
            switch ($callbackData['type']) {
                case 'transaction':
                    return $this->handleTransactionCallback($callbackData, $idempotencyKey);

                case 'tokenization':
                    return $this->handleTokenizationCallback($callbackData, $idempotencyKey);

                case 'refund':
                    return $this->handleRefundCallback($callbackData, $idempotencyKey);

                default:
                    return $this->errorResponse('Unknown callback type', 400);
            }
        } catch (\Exception $e) {
            Log::error('Callback processing failed', [
                'callback_data' => $callbackData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Processing failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle transaction callback (payment success/failure)
     */
    private function handleTransactionCallback(array $callbackData, string $idempotencyKey)
    {
        // Acquire idempotency lock
        $idempotencyRecord = $this->acquireIdempotencyLock($idempotencyKey, $callbackData);
        if (!$idempotencyRecord) {
            return $this->errorResponse('Callback already being processed', 409);
        }

        try {
            DB::beginTransaction();

            // Validate transaction with Paymob
            $transactionValid = $this->paymobService->validateTransaction($callbackData);
            if (!$transactionValid) {
                throw new \Exception('Transaction validation failed');
            }

            // Check if transaction is successful
            if ($callbackData['success'] === true) {
                // Process successful payment
                $result = $this->processSuccessfulPayment($callbackData);

                // Complete idempotency
                $idempotencyRecord->completeWithResponse(
                    md5(json_encode($result)),
                    'LedgerEntry',
                    $result['transaction']->id ?? null
                );

                DB::commit();

                Log::info('Payment processed successfully', [
                    'transaction_id' => $callbackData['transaction_id'],
                    'amount' => $callbackData['amount'],
                    'user_id' => $callbackData['user_id'] ?? null,
                    'order_id' => $callbackData['order_id']
                ]);

                return $this->successResponse('Payment processed successfully', $result);
            } else {
                // Process failed payment
                $result = $this->processFailedPayment($callbackData);

                // Complete idempotency
                $idempotencyRecord->completeWithResponse(
                    md5(json_encode($result)),
                    'LedgerEntry',
                    $result['transaction']->id ?? null
                );

                DB::commit();

                Log::warning('Payment failed', [
                    'transaction_id' => $callbackData['transaction_id'],
                    'error' => $callbackData['error'] ?? 'Unknown error'
                ]);

                return $this->errorResponse('Payment failed', 400, $result);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $idempotencyRecord->markFailed();

            Log::error('Transaction callback processing failed', [
                'transaction_id' => $callbackData['transaction_id'],
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('Processing failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle tokenization callback (card token)
     */
    private function handleTokenizationCallback(array $callbackData, string $idempotencyKey)
    {
        // Acquire idempotency lock
        $idempotencyRecord = $this->acquireIdempotencyLock($idempotencyKey, $callbackData);
        if (!$idempotencyRecord) {
            return $this->errorResponse('Callback already being processed', 409);
        }

        try {
            // Validate token with Paymob
            $tokenValid = $this->paymobService->validateToken($callbackData['token']);
            if (!$tokenValid) {
                throw new \Exception('Token validation failed');
            }

            // Store token securely
            $storedToken = $this->storePaymentToken($callbackData);

            // Complete idempotency
            $idempotencyRecord->completeWithResponse(
                md5(json_encode($storedToken)),
                'PaymentToken',
                $storedToken['id'] ?? null
            );

            Log::info('Tokenization successful', [
                'user_id' => $callbackData['user_id'],
                'token_mask' => $storedToken['masked_token'] ?? '****'
            ]);

            return $this->successResponse('Tokenization successful', $storedToken);
        } catch (\Exception $e) {
            $idempotencyRecord->markFailed();

            Log::error('Tokenization callback failed', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackData
            ]);

            return $this->errorResponse('Tokenization failed: ' . $e->getMessage(), 400);
        }
    }

    /**
     * Handle refund callback
     */
    private function handleRefundCallback(array $callbackData, string $idempotencyKey)
    {
        // Acquire idempotency lock
        $idempotencyRecord = $this->acquireIdempotencyLock($idempotencyKey, $callbackData);
        if (!$idempotencyRecord) {
            return $this->errorResponse('Callback already being processed', 409);
        }

        try {
            DB::beginTransaction();

            // Validate refund with Paymob
            $refundValid = $this->paymobService->validateRefund($callbackData);
            if (!$refundValid) {
                throw new \Exception('Refund validation failed');
            }

            // Process refund
            $result = $this->processRefund($callbackData);

            // Complete idempotency
            $idempotencyRecord->completeWithResponse(
                md5(json_encode($result)),
                'LedgerEntry',
                $result['transaction']->id ?? null
            );

            DB::commit();

            Log::info('Refund processed successfully', [
                'refund_id' => $callbackData['refund_id'],
                'amount' => $callbackData['amount'],
                'original_transaction_id' => $callbackData['original_transaction_id']
            ]);

            return $this->successResponse('Refund processed successfully', $result);
        } catch (\Exception $e) {
            DB::rollBack();
            $idempotencyRecord->markFailed();

            Log::error('Refund callback failed', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackData
            ]);

            return $this->errorResponse('Refund failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process successful payment
     */
    private function processSuccessfulPayment(array $callbackData): array
    {
        // Find the pending deposit transaction
        $pendingTransaction = LedgerEntry::where('reference', $callbackData['order_id'])
            ->where('type', LedgerEntry::TYPE_DEPOSIT_PENDING)
            ->where('status', LedgerEntry::STATUS_PENDING)
            ->first();

        if (!$pendingTransaction) {
            throw new \Exception('Pending transaction not found');
        }

        // Confirm deposit using UserWalletService
        $completedTransaction = $this->userWalletService->confirmDeposit(
            $callbackData['transaction_id'],
            [
                'order_id' => $callbackData['order_id'],
                'amount' => $callbackData['amount'],
                'currency' => $callbackData['currency'],
                'payment_method' => $callbackData['payment_method'] ?? 'paymob',
                'user_id' => $pendingTransaction->user_id,
                'exchange_rate' => $callbackData['exchange_rate'] ?? 1,
                'currency_charged' => $callbackData['currency_charged'] ?? 'SAR'
            ]
        );

        if (!$completedTransaction) {
            throw new \Exception('Deposit confirmation failed');
        }

        // Update transaction metadata
        $completedTransaction->update([
            'metadata' => array_merge($completedTransaction->metadata ?? [], [
                'callback_data' => $callbackData,
                'processed_at' => now()->toIso8601String(),
                'payment_gateway' => 'paymob',
                'payment_method_details' => $callbackData['payment_method_details'] ?? null
            ])
        ]);

        return [
            'success' => true,
            'transaction' => $completedTransaction,
            'user_id' => $pendingTransaction->user_id,
            'amount' => $completedTransaction->amount,
            'currency' => $completedTransaction->metadata['currency_charged'] ?? 'SAR',
            'wallet_balance' => UserWallet::find($completedTransaction->wallet_id)->balance,
            'reference' => $completedTransaction->reference
        ];
    }

    /**
     * Process failed payment
     */
    private function processFailedPayment(array $callbackData): array
    {
        // Find the pending deposit transaction
        $pendingTransaction = LedgerEntry::where('reference', $callbackData['order_id'])
            ->where('type', LedgerEntry::TYPE_DEPOSIT_PENDING)
            ->where('status', LedgerEntry::STATUS_PENDING)
            ->first();

        if (!$pendingTransaction) {
            throw new \Exception('Pending transaction not found');
        }

        // Mark transaction as failed
        $pendingTransaction->markFailed($callbackData['error'] ?? 'Payment failed');

        // Update metadata with error details
        $pendingTransaction->update([
            'metadata' => array_merge($pendingTransaction->metadata ?? [], [
                'callback_data' => $callbackData,
                'failed_at' => now()->toIso8601String(),
                'failure_reason' => $callbackData['error'] ?? 'Unknown error',
                'payment_gateway_error' => $callbackData['gateway_error'] ?? null
            ])
        ]);

        // Send failure notification to user
        $this->sendPaymentFailureNotification($pendingTransaction, $callbackData);

        return [
            'success' => false,
            'transaction' => $pendingTransaction,
            'user_id' => $pendingTransaction->user_id,
            'error' => $callbackData['error'] ?? 'Payment failed',
            'reference' => $pendingTransaction->reference
        ];
    }

    /**
     * Process refund
     */
    private function processRefund(array $callbackData): array
    {
        // Find original transaction
        $originalTransaction = LedgerEntry::where('payment_transaction_id', $callbackData['original_transaction_id'])
            ->where('type', LedgerEntry::TYPE_DEPOSIT)
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->first();

        if (!$originalTransaction) {
            throw new \Exception('Original transaction not found');
        }

        // Get user wallet
        $userWallet = UserWallet::where('id', $originalTransaction->wallet_id)->first();
        if (!$userWallet) {
            throw new \Exception('User wallet not found');
        }

        // Check if wallet has sufficient balance
        if ($userWallet->available_balance < $callbackData['amount']) {
            throw new \Exception('Insufficient wallet balance for refund');
        }

        // Create refund transaction
        $refundTransaction = LedgerEntry::create([
            'wallet_type' => 'user',
            'wallet_id' => $userWallet->id,
            'user_id' => $originalTransaction->user_id,
            'type' => LedgerEntry::TYPE_REFUND,
            'amount' => $callbackData['amount'],
            'balance_before' => $userWallet->balance,
            'balance_after' => $userWallet->balance - $callbackData['amount'],
            'available_balance_before' => $userWallet->available_balance,
            'available_balance_after' => $userWallet->available_balance - $callbackData['amount'],
            'payment_method' => $originalTransaction->payment_method,
            'payment_transaction_id' => $callbackData['refund_id'],
            'description' => 'استرداد مدفوعات - ' . ($callbackData['reason'] ?? 'طلب العميل'),
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => 'REF-' . now()->format('Ymd') . '-' . Str::random(6),
            'related_transaction_id' => $originalTransaction->id,
            'metadata' => [
                'original_transaction_id' => $originalTransaction->id,
                'refund_reason' => $callbackData['reason'] ?? 'طلب العميل',
                'callback_data' => $callbackData,
                'processed_at' => now()->toIso8601String()
            ],
            'processed_at' => now()
        ]);

        // Update wallet balance
        $userWallet->update([
            'balance' => DB::raw('balance - ' . $callbackData['amount']),
            'last_transaction_at' => now()
        ]);

        return [
            'success' => true,
            'transaction' => $refundTransaction,
            'original_transaction' => $originalTransaction,
            'amount' => $callbackData['amount'],
            'user_id' => $originalTransaction->user_id,
            'wallet_balance' => $userWallet->balance
        ];
    }

    /**
     * Store payment token securely
     */
    private function storePaymentToken(array $callbackData): array
    {
        // This should be stored in a secure vault/tokenization service
        // For this example, we'll store a masked version

        $tokenId = 'tok_' . Str::random(32);
        $maskedToken = substr($callbackData['token'], 0, 4) . '****' . substr($callbackData['token'], -4);

        DB::table('payment_tokens')->insert([
            'user_id' => $callbackData['user_id'],
            'token_id' => $tokenId,
            'token_masked' => $maskedToken,
            'payment_method' => $callbackData['payment_method'] ?? 'credit_card',
            'card_brand' => $callbackData['card_brand'] ?? null,
            'card_last_four' => $callbackData['card_last_four'] ?? null,
            'card_expiry_month' => $callbackData['card_expiry_month'] ?? null,
            'card_expiry_year' => $callbackData['card_expiry_year'] ?? null,
            'is_default' => false,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return [
            'id' => $tokenId,
            'masked_token' => $maskedToken,
            'user_id' => $callbackData['user_id'],
            'payment_method' => $callbackData['payment_method'] ?? 'credit_card',
            'card_last_four' => $callbackData['card_last_four'] ?? null
        ];
    }

    /**
     * Send payment failure notification
     */
    private function sendPaymentFailureNotification(LedgerEntry $transaction, array $callbackData): void
    {
        // This should be implemented with your notification service
        // For example: email, SMS, push notification

        Log::info('Payment failure notification sent', [
            'transaction_id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'amount' => $transaction->amount,
            'error' => $callbackData['error'] ?? 'Unknown error'
        ]);
    }

    /**
     * Log callback request for debugging
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

        Log::channel('payment_callback')->debug('Payment callback received', $logData);
    }

    /**
     * Validate IP address
     */
    private function validateIpAddress(Request $request): bool
    {
        $clientIp = $request->ip();
        $allowedIps = config('payment.paymob.allowed_ips', []);

        // If no IPs configured, allow all (for testing)
        if (empty($allowedIps)) {
            Log::warning('No IP whitelist configured for Paymob callbacks');
            return true;
        }

        return $this->ipWhitelist->isAllowed($clientIp, $allowedIps);
    }

    /**
     * Validate HMAC signature
     */
    private function validateHmacSignature(Request $request): bool
    {
        $hmacSecret = config('payment.paymob.hmac_secret');

        // If no HMAC secret configured, skip validation (for testing)
        if (empty($hmacSecret)) {
            Log::warning('No HMAC secret configured for Paymob callbacks');
            return true;
        }

        // Get HMAC from header
        $receivedHmac = $request->header('X-HMAC-Signature');
        if (empty($receivedHmac)) {
            return false;
        }

        // Generate HMAC from request body
        $requestBody = $request->getContent();
        $expectedHmac = hash_hmac('sha256', $requestBody, $hmacSecret);

        return hash_equals($expectedHmac, $receivedHmac);
    }

    /**
     * Parse callback data
     */
    private function parseCallbackData(Request $request): array
    {
        $data = $request->all();

        // Define validation rules based on callback type
        $validator = Validator::make($data, [
            'type' => 'required|in:transaction,tokenization,refund',
            'transaction_id' => 'required_if:type,transaction,refund',
            'order_id' => 'required_if:type,transaction',
            'amount' => 'required_if:type,transaction,refund|numeric|min:0',
            'success' => 'required_if:type,transaction|boolean',
            'currency' => 'required_if:type,transaction,refund|string|size:3',
            'created_at' => 'required|date',
            'hmac' => 'required|string'
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'error' => 'Invalid callback data: ' . $validator->errors()->first(),
                'errors' => $validator->errors()->toArray()
            ];
        }

        return array_merge($data, [
            'valid' => true,
            'received_at' => now()->toIso8601String(),
            'ip_address' => $request->ip()
        ]);
    }

    /**
     * Generate idempotency key
     */
    private function generateIdempotencyKey(array $callbackData): string
    {
        $keyData = [
            'transaction_id' => $callbackData['transaction_id'] ?? null,
            'order_id' => $callbackData['order_id'] ?? null,
            'type' => $callbackData['type'],
            'timestamp' => $callbackData['created_at']
        ];

        return 'paymob_callback_' . md5(json_encode($keyData));
    }

    /**
     * Check if callback already processed
     */
    private function isAlreadyProcessed(string $idempotencyKey): bool
    {
        $existing = IdempotencyKey::where('key', $idempotencyKey)
            ->where('status', 'completed')
            ->where('expires_at', '>', now())
            ->first();

        return $existing !== null;
    }

    /**
     * Acquire idempotency lock
     */
    private function acquireIdempotencyLock(string $key, array $requestData): ?IdempotencyKey
    {
        $requestHash = md5(json_encode($requestData));

        return IdempotencyKey::acquireLock(
            $key,
            $requestHash,
            3600,
            'user'
        );
    }

    /**
     * Get filtered headers (remove sensitive data)
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
     * Get filtered body params (remove sensitive data)
     */
    private function getFilteredBodyParams(array $params): array
    {
        $sensitiveParams = ['card_number', 'card_cvv', 'card_expiry', 'token', 'hmac'];

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

    /**
     * Success response
     */
    private function successResponse(string $message, array $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Error response
     */
    private function errorResponse(string $message, int $status = 400, array $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String()
        ], $status);
    }
}
