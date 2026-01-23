<?php

namespace App\Services\Payment\Wallet;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Wallet\ExchangeRateService;
use App\Services\Payment\Wallet\PaymentGatewayInterface;

class PaymobService implements PaymentGatewayInterface
{
    private string $username;
    private string $password;
    private string $apiKey;
    private string $integrationId;
    private string $iframeId;
    private string $hmacSecret;
    private string $baseUrl;
    private string $currency;
    private ExchangeRateService $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->username = config('services.paymob.username');
        $this->password = config('services.paymob.password');
        $this->apiKey = config('services.paymob.api_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->iframeId = config('services.paymob.iframe_id');
        $this->hmacSecret = config('services.paymob.hmac_secret');
        $this->baseUrl = config('services.paymob.base_url');
        $this->currency = config('services.paymob.currency', 'SAR');
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Create payment order for KSA Paymob
     */
    public function createPaymentOrder(array $data): array
    {
        try {
            $user = $data['user'];
            $amount = $data['amount'];
            $walletCurrency = $data['wallet_currency'] ?? 'SAR';
            $orderId = $data['order_id'] ?? 'DEP-' . $amount . '-' . $walletCurrency . '-' . time();

            // Convert amount if needed (للسعودية عادة لا نحتاج تحويل)
            if ($walletCurrency !== $this->currency) {
                $conversion = $this->exchangeRateService->convert(
                    $amount,
                    $walletCurrency,
                    $this->currency
                );
                $amountCents = round($conversion['converted_amount'] * 100);
                $originalAmount = $amount;
                $convertedAmount = $conversion['converted_amount'];
                $exchangeRate = $conversion['exchange_rate'];
            } else {
                $amountCents = round($amount * 100);
                $originalAmount = $amount;
                $convertedAmount = $amount;
                $exchangeRate = 1;
            }

            // Get auth token using username/password (الطريقة الصحيحة لـ KSA)
            $authResult = $this->getAuthToken();
            if (!$authResult['success']) {
                throw new \Exception($authResult['error']);
            }

            $authToken = $authResult['token'];
            // Create payment link (الطريقة الموصى بها لـ KSA)
            $paymentLink = $this->createPaymentLink(
                $authToken,
                $amountCents,
                $orderId,
                $user,
                $data['callback_url'] ?? null
            );

            if (!$paymentLink['success']) {
                throw new \Exception($paymentLink['error'] ?? 'Failed to create payment link');
            }

            return [
                'success' => true,
                'payment_url' => $paymentLink['payment_url'],
                'shorten_url' => $paymentLink['shorten_url'] ?? null,
                'payment_token' => $paymentLink['token'] ?? null,
                'order_id' => $orderId,
                'merchant_order_id' => $orderId,
                'amount_original' => $originalAmount,
                'amount_charged' => $convertedAmount,
                'exchange_rate' => $exchangeRate,
                'currency_original' => $walletCurrency,
                'currency_charged' => $this->currency,
                'expires_at' => now()->addHours(24)->toIso8601String()
            ];
        } catch (\Exception $e) {
            Log::error('Paymob KSA payment creation failed', [
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
     * Get auth token for KSA Paymob
     */
    private function getAuthToken(): array
    {
        try {
            $response = Http::post($this->baseUrl . '/api/auth/tokens', [
                'username' => $this->username,
                'password' => $this->password
            ]);

            if ($response->failed()) {
                Log::error('Paymob KSA auth failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => 'Authentication failed: ' . $response->status()
                ];
            }

            $data = $response->json();

            if (!isset($data['token'])) {
                return [
                    'success' => false,
                    'error' => 'No token in response'
                ];
            }

            return [
                'success' => true,
                'token' => $data['token'],
                'profile_token' => $data['profile_token'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error('Paymob KSA auth exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create payment link (الطريقة الحديثة)
     */
    private function createPaymentLink(string $authToken, int $amountCents, string $orderId, $user, ?string $callbackUrl = null): array
    {
        try {
            $payload = [
                'amount_cents' => $amountCents,
                'currency' => $this->currency,
                'reference_id' => $orderId,
                'payment_methods' => [$this->integrationId], // تأكد من أن هذا صحيح
                'full_name' => isset($user->name) ? trim(preg_replace('/\d+/', '', $user->name)) : 'Customer',
                'email' =>  'customer@talaaljazeera.com',
                'phone_number' => $user->phone ?? $user->full_phone ?? '+966500000000',
                'expires_at' => now()->setTimezone('Asia/Riyadh')->addHours(2)->format('Y-m-d\TH:i:s'),
                'save_selection' => true,
                'is_live' => config('services.paymob.mode') === 'live',
                'redirect_url' => url("/payment/success?order_id={$orderId}"),
                'cancel_url' => url("/payment/cancel?order_id={$orderId}"),
            ];
            // Add callback URL if provided
            if ($callbackUrl) {
                $payload['callback_url'] = $callbackUrl;
            }
            $response = Http::withToken($authToken)
                ->asForm()
                ->post($this->baseUrl . '/api/ecommerce/payment-links', $payload);
            if ($response->failed()) {
                Log::error('Paymob payment link creation failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload
                ]);

                return [
                    'success' => false,
                    'error' => 'Payment link creation failed: ' . $response->status(),
                    'details' => $response->json()
                ];
            }

            $data = $response->json();

            return [
                'success' => true,
                'payment_url' => $data['client_url'] ?? $data['shorten_url'] ?? null,
                'shorten_url' => $data['shorten_url'] ?? null,
                'token' => $data['token'] ?? null,
                'id' => $data['id'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error('Paymob payment link exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify transaction from webhook
     */
    public function verifyTransaction(array $data): array
    {
        try {
            // التحقق من HMAC
            if (!$this->validateHmac($data)) {
                throw new \Exception('Invalid HMAC signature');
            }

            $obj = $data['obj'];

            // استخراج تفاصيل المعاملة
            $transactionId = $obj['id'] ?? null;
            $amountCents = $obj['amount_cents'] ?? 0;
            $amount = $amountCents / 100;
            $currency = $obj['currency'] ?? $this->currency;
            $success = $obj['success'] ?? false;
            $orderId = $obj['merchant_reference'] ?? $obj['reference_id'] ?? null;
            $isCapture = $obj['is_capture'] ?? false;

            if (!$success || !$isCapture) {
                return [
                    'success' => false,
                    'error' => 'Payment not completed',
                    'error_code' => 'PAYMENT_INCOMPLETE',
                    'transaction_id' => $transactionId
                ];
            }

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'order_id' => $orderId,
                'merchant_order_id' => $orderId,
                'amount_charged' => $amount,
                'amount_original' => $amount, // للسعودية نفس المبلغ عادة
                'currency_charged' => $currency,
                'currency_original' => $currency,
                'exchange_rate' => 1,
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
     * Validate HMAC signature
     */
    private function validateHmac(array $data): bool
    {
        if (!isset($data['hmac'])) {
            return false;
        }

        $hmac = $data['hmac'];
        unset($data['hmac']);

        // فرز المصفوفة أبجديًا
        ksort($data);

        // تسلسل القيم
        $concatenated = '';
        foreach ($data as $value) {
            if (is_array($value)) {
                $concatenated .= json_encode($value, JSON_UNESCAPED_SLASHES);
            } else {
                $concatenated .= $value;
            }
        }

        // حساب HMAC
        $calculatedHmac = hash_hmac('sha512', $concatenated, $this->hmacSecret);

        return hash_equals($hmac, $calculatedHmac);
    }

    /**
     * Refund transaction
     */
    public function refund(string $transactionId, float $amount): array
    {
        // Implementation for refund
        // Note: قد تحتاج لطريقة مختلفة لـ KSA
        return [
            'success' => false,
            'error' => 'Refund not implemented for KSA Paymob'
        ];
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $authResult = $this->getAuthToken();
            if (!$authResult['success']) {
                throw new \Exception($authResult['error']);
            }

            $response = Http::withToken($authResult['token'])
                ->get($this->baseUrl . "/api/acceptance/transactions/{$transactionId}");

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
}
