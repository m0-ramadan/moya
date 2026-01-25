<?php

namespace App\Services\Payment;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\Wallet\ExchangeRateService;
use App\Services\Payment\Gateways\BaseGateway;
use App\Services\Payment\Gateways\PaymobGateway;
use App\Contracts\Payment\PaymentGatewayInterface;

class PaymobService extends BaseGateway
{ 
    //use PaymobGateway;
    private string $username;
    private string $password;
    private string $apiKey;
    private string $integrationId;
    private string $iframeId;
    private string $hmacSecret;
    private  $paymobGateway;
    private ExchangeRateService $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService, PaymobGateway $paymobGateway)
    {
        parent::__construct();
        $this->exchangeRateService = $exchangeRateService;
        $this->paymobGateway = $paymobGateway;
    }

    // ----------------------------
    // Implementation of PaymentGatewayInterface
    // ----------------------------

    public function initiatePayment(array $data): array
    {
        return $this->createPaymentOrder($data);
    }

    public function verifyPayment(array $data): array
    {
        return $this->verifyTransaction($data);
    }

    public function refundPayment(string $transactionId, float $amount, string $reason = ''): array
    {
        return $this->refund($transactionId, $amount, $reason);
    }

    public function checkPaymentStatus(string $transactionId): array
    {
        return $this->getTransactionStatus($transactionId);
    }

    public function isWebhookValid(array $data): bool
    {
        return $this->validateHmac($data);
    }

    public function handleWebhook(array $data): array
    {
        return $this->verifyTransaction($data);
    }

    // ----------------------------
    // Abstract methods implementation
    // ----------------------------

    protected function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function getGatewayName(): string
    {
        return 'paymob';
    }

    protected function initializeConfig(): void
    {
        $this->username = config('services.paymob.username');
        $this->password = config('services.paymob.password');
        $this->apiKey = config('services.paymob.api_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->iframeId = config('services.paymob.iframe_id');
        $this->hmacSecret = config('services.paymob.hmac_secret');
        $this->baseUrl = config('services.paymob.base_url', 'https://ksa.paymob.com');
        $this->currency = config('services.paymob.currency', 'SAR');
    }

    // Override the auth token initialization if needed
    protected function initializeAuthToken(): void
    {
        // You can customize this if needed
        parent::initializeAuthToken();
    }

    // ----------------------------
    // Custom Methods
    // ----------------------------

    public function createPaymentOrder(array $data): array
    {
        try {
            $user = $data['user'] ?? null;
            $amount = $data['amount'] ?? 0;
            $walletCurrency = $data['wallet_currency'] ?? $this->currency;
            $orderId = $data['order_id'] ?? 'ORD-' . time();

            // Convert currency if needed
            if ($walletCurrency !== $this->currency) {
                $conversion = $this->exchangeRateService->convert(
                    $amount,
                    $walletCurrency,
                    $this->currency
                );
                $amountCents = round($conversion['converted_amount'] * 100);
            } else {
                $amountCents = round($amount * 100);
            }

            $authToken = $this->getAuthToken('paymob');

            if (!$authToken) {
                throw new \Exception('Paymob authentication failed');
            }

            $paymentLink = $this->paymobGateway->createPaymentLink(
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
                'token' => $paymentLink['token'] ?? null,
                'order_id' => $orderId,
            ];

        } catch (\Exception $e) {
            Log::error('Paymob payment creation failed', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

//     private function createPaymentLink(string $authToken, int $amountCents, string $orderId, $user, ?string $callbackUrl = null): array
//     {
//         try {
//             $payload = [
//                 'amount_cents' => $amountCents,
//                 'currency' => $this->currency,
//                 'reference_id' => $orderId.rand(1000,10000),
//                 'payment_methods' => [$this->integrationId],
//                 'full_name' => $user->name ?? 'Customer',
//                 'email' => $user->email ?? 'customer@example.com',
//                 'phone_number' => $user->phone ?? '+966500000000',
//                 'redirect_url' => url(path: "/payment/success?order_id={$orderId}"),
//                 'cancel_url' => url("/payment/cancel?order_id={$orderId}"),
//             ];

//             if ($callbackUrl) {
//                 $payload['callback_url'] = $callbackUrl;
//             }

//             $response = Http::withToken($authToken)
//                 ->asForm()
//                 ->post($this->baseUrl . '/api/ecommerce/payment-links', $payload);

//             if ($response->failed()) {
//                 return [
//                     'success' => false,
//                     'error' => 'Payment link creation failed',
//                     'details' => $response->json()
//                 ];
//             }
// dd($response->json());
//             $data = $response->json();

//             return [
//                 'success' => true,
//                 'payment_url' => $data['client_url'] ?? null,
//                 'shorten_url' => $data['shorten_url'] ?? null,
//                 'token' => $data['token'] ?? null
//             ];

//         } catch (\Exception $e) {
//             return [
//                 'success' => false,
//                 'error' => $e->getMessage()
//             ];
//         }
//     }

 
public function verifyTransaction(array $data): array
    {
        try {
            if (!$this->validateHmac($data)) {
                throw new \Exception('Invalid HMAC');
            }

            $obj = $data['obj'] ?? [];
            $success = $obj['success'] ?? false;
            $isCapture = $obj['is_capture'] ?? false;

            if (!$success || !$isCapture) {
                return [
                    'success' => false,
                    'error' => 'Payment not completed'
                ];
            }

            return [
                'success' => true,
                'transaction_id' => $obj['id'] ?? null,
                'order_id' => $obj['merchant_reference'] ?? null
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function validateHmac(array $data): bool
    {
        if (!isset($data['hmac'])) return false;

        $hmac = $data['hmac'];
        unset($data['hmac']);

        ksort($data);

        $concatenated = '';
        foreach ($data as $value) {
            $concatenated .= is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES) : $value;
        }

        $calculatedHmac = hash_hmac('sha512', $concatenated, $this->hmacSecret);

        return hash_equals($hmac, $calculatedHmac);
    }

    public function refund(string $transactionId, float $amount, string $reason = ''): array
    {
        return [
            'success' => false,
            'error' => 'Refund not implemented yet'
        ];
    }

    public function getTransactionStatus(string $transactionId): array
    {
        $authToken = $this->getAuthToken('paymob');
        if (!$authToken) return ['success' => false, 'error' => 'Auth failed'];

        try {
            $response = Http::withToken($authToken)
                ->get($this->baseUrl . "/api/acceptance/transactions/{$transactionId}");

            if (!$response->successful()) {
                return ['success' => false, 'error' => 'Failed to fetch status'];
            }

            $data = $response->json();

            return [
                'success' => true,
                'status' => $data['success'] ? 'success' : 'failed',
                'data' => $data
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}