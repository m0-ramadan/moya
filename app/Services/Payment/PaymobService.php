<?php

namespace App\Services\Payment;

use App\Services\Wallet\ExchangeRateService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymobService implements PaymentGatewayInterface
{

    private string $apiKey;
    private string $integrationId;
    private string $iframeId;
    private string $hmacSecret;
    private ExchangeRateService $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->apiKey = config('services.paymob.api_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->iframeId = config('services.paymob.iframe_id');
        $this->hmacSecret = config('services.paymob.hmac_secret');
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Create payment order with currency conversion
     */
    public function createPaymentOrder(array $data): array
    {
        try {
            $user = $data['user'];
            $amount = $data['amount'];
            $walletCurrency = $data['wallet_currency'] ?? 'SAR';
            $orderId = $data['order_id'] ?? 'ORDER-' . Str::uuid();

            // Convert amount to Paymob currency
            $conversion = $this->exchangeRateService->convert(
                $amount,
                $walletCurrency,
                config('services.paymob.currency', 'EGP')
            );

            // Get auth token
            $authToken = $this->getAuthToken();

            // Create order
            $orderData = $this->createOrder($authToken, $conversion['converted_amount'], $user, $orderId);

            // Get payment key
            $paymentKey = $this->getPaymentKey($authToken, $conversion['converted_amount'], $orderData['id'], $user);

            return [
                'success' => true,
                'payment_token' => $paymentKey,
                'order_id' => $orderData['id'],
                'merchant_order_id' => $orderId,
                'amount_original' => $amount,
                'amount_charged' => $conversion['converted_amount'],
                'exchange_rate' => $conversion['exchange_rate'],
                'currency_original' => $walletCurrency,
                'currency_charged' => config('services.paymob.currency', 'EGP'),
                'iframe_url' => "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}",
                'expires_at' => now()->addHours(24)->toIso8601String()
            ];
        } catch (\Exception $e) {
            Log::error('Paymob payment creation failed', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'PAYMENT_CREATION_FAILED'
            ];
        }
    }

    /**
     * Verify transaction
     */
    public function verifyTransaction(array $data): array
    {
        try {
            if (!$this->validateHmac($data)) {
                throw new \Exception('Invalid HMAC signature');
            }

            $obj = $data['obj'];

            // Extract transaction details
            $transactionId = $obj['id'];
            $amountCents = $obj['amount_cents'];
            $amount = $amountCents / 100;
            $currency = $obj['currency'] ?? 'EGP';
            $success = $obj['success'] ?? false;
            $orderId = $obj['order']['id'] ?? null;
            $merchantOrderId = $obj['order']['merchant_order_id'] ?? null;

            if (!$success) {
                return [
                    'success' => false,
                    'error' => 'Payment failed',
                    'error_code' => 'PAYMENT_FAILED',
                    'transaction_id' => $transactionId
                ];
            }

            // Get original amount from merchant order ID
            $originalAmount = $this->extractOriginalAmount($merchantOrderId);

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'order_id' => $orderId,
                'merchant_order_id' => $merchantOrderId,
                'amount_charged' => $amount,
                'amount_original' => $originalAmount['amount'] ?? $amount,
                'currency_charged' => $currency,
                'currency_original' => $originalAmount['currency'] ?? $currency,
                'exchange_rate' => $amount / ($originalAmount['amount'] ?? $amount),
                'data' => $obj,
                'verified_at' => now()->toIso8601String()
            ];
        } catch (\Exception $e) {
            Log::error('Paymob verification failed', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'VERIFICATION_FAILED'
            ];
        }
    }

    /**
     * Refund transaction
     */
    public function refund(string $transactionId, float $amount): array
    {
        try {
            $authToken = $this->getAuthToken();

            $response = Http::post("https://accept.paymob.com/api/acceptance/void_refund/refund", [
                'auth_token' => $authToken,
                'transaction_id' => $transactionId,
                'amount_cents' => round($amount * 100)
            ]);

            if (!$response->successful()) {
                throw new \Exception('Refund failed: ' . $response->body());
            }

            return [
                'success' => true,
                'refund_id' => $response->json('id'),
                'amount' => $amount,
                'status' => 'success'
            ];
        } catch (\Exception $e) {
            Log::error('Paymob refund failed', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $authToken = $this->getAuthToken();

            $response = Http::get("https://accept.paymob.com/api/acceptance/transactions/{$transactionId}", [
                'auth_token' => $authToken
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to get transaction status');
            }

            $data = $response->json();

            return [
                'success' => true,
                'status' => $data['success'] ? 'success' : 'failed',
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function getAuthToken(): string
    {
        $response = Http::retry(3, 100)->post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $this->apiKey
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to authenticate with Paymob');
        }

        return $response->json('token');
    }

    private function createOrder(string $authToken, float $amount, $user, string $orderId): array
    {
        $response = Http::retry(2, 100)->post('https://accept.paymob.com/api/ecommerce/orders', [
            'auth_token' => $authToken,
            'delivery_needed' => false,
            'amount_cents' => round($amount * 100),
            'currency' => config('services.paymob.currency', 'EGP'),
            'items' => [],
            'merchant_order_id' => $orderId
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to create order: ' . $response->body());
        }

        return $response->json();
    }

    private function getPaymentKey(string $authToken, float $amount, int $orderId, $user): string
    {
        $response = Http::retry(2, 100)->post('https://accept.paymob.com/api/acceptance/payment_keys', [
            'auth_token' => $authToken,
            'amount_cents' => round($amount * 100),
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => [
                'first_name' => $user->name,
                'phone_number' => $user->full_phone,
                'email' => $user->email,
            ],
            'currency' => config('services.paymob.currency', 'EGP'),
            'integration_id' => $this->integrationId,
            'lock_order_when_paid' => true,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get payment key: ' . $response->body());
        }

        return $response->json('token');
    }

    private function validateHmac(array $data): bool
    {
        $hmac = $data['hmac'] ?? '';
        $calculatedHmac = $this->calculateHmac($data['obj'] ?? []);

        return hash_equals($hmac, $calculatedHmac);
    }

    private function calculateHmac(array $obj): string
    {
        $hmacString = '';
        $keys = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order.id',
            'owner',
            'pending',
            'source_data.pan',
            'source_data.sub_type',
            'source_data.type',
            'success'
        ];

        foreach ($keys as $key) {
            $value = data_get($obj, $key, '');
            $hmacString .= $value;
        }

        return hash_hmac('sha512', $hmacString, $this->hmacSecret);
    }

    private function extractOriginalAmount(string $merchantOrderId): array
    {
        // Parse merchant order ID to extract original amount
        // Format: DEP-{amount}-{currency}-{timestamp}
        $parts = explode('-', $merchantOrderId);

        if (count($parts) >= 3) {
            return [
                'amount' => floatval($parts[1] ?? 0),
                'currency' => $parts[2] ?? 'SAR'
            ];
        }

        return ['amount' => 0, 'currency' => 'SAR'];
    }
}
