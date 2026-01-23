<?php

namespace App\Services\Payment\Gateways;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymobGateway extends BaseGateway
{
    protected function initializeConfig(): void
    {
        $this->config = config('services.paymob', []);
    }

    protected function getGatewayName(): string
    {
        return 'paymob';
    }

    protected function getBaseUrl(): string
    {
        return $this->isSandbox
            ? 'https://accept.paymobsolutions.com/api'
            : 'https://accept.paymobsolutions.com/api';
    }

    protected function fetchAuthToken(): string
    {
        $response = Http::post($this->getBaseUrl() . '/auth/tokens', [
            'api_key' => $this->config['api_key'],
        ]);

        if ($response->successful()) {
            return $response->json()['token'];
        }

        throw new \Exception('Failed to get Paymob auth token');
    }

    public function initiatePayment(array $data): array
    {
        try {
            $authToken = $this->getAuthToken();

            // Step 1: Create Order
            $orderResponse = $this->makeRequest('POST', '/ecommerce/orders', [
                'auth_token' => $authToken,
                'delivery_needed' => 'false',
                'amount_cents' => $data['amount'] * 100,
                'currency' => $this->currency,
                'merchant_order_id' => $data['order_id'],
                'items' => [],
            ]);

            if (!$orderResponse['success']) {
                throw new \Exception('Failed to create order: ' . ($orderResponse['error'] ?? 'Unknown error'));
            }

            $orderId = $orderResponse['data']['id'] ?? null;

            // Step 2: Create Payment Key
            $paymentKeyResponse = $this->makeRequest('POST', '/acceptance/payment_keys', [
                'auth_token' => $authToken,
                'amount_cents' => $data['amount'] * 100,
                'expiration' => 3600,
                'order_id' => $orderId,
                'billing_data' => [
                    'apartment' => 'NA',
                    'email' => $data['customer']['email'] ?? 'customer@example.com',
                    'floor' => 'NA',
                    'first_name' => $data['customer']['first_name'] ?? 'Customer',
                    'street' => 'NA',
                    'building' => 'NA',
                    'phone_number' => $data['customer']['phone'] ?? '+966500000000',
                    'shipping_method' => 'NA',
                    'postal_code' => 'NA',
                    'city' => 'Riyadh',
                    'country' => 'SA',
                    'last_name' => $data['customer']['last_name'] ?? '',
                    'state' => 'Riyadh',
                ],
                'currency' => $this->currency,
                'integration_id' => $this->config['integration_id'],
                'lock_order_when_paid' => 'false',
            ]);

            if (!$paymentKeyResponse['success']) {
                throw new \Exception('Failed to create payment key: ' . ($paymentKeyResponse['error'] ?? 'Unknown error'));
            }

            $paymentToken = $paymentKeyResponse['data']['token'] ?? null;

            return [
                'success' => true,
                'gateway' => 'paymob',
                'payment_id' => $paymentToken,
                'order_id' => $orderId,
                'iframe_id' => $this->config['iframe_id'],
                'payment_url' => "https://accept.paymobsolutions.com/api/acceptance/iframes/{$this->config['iframe_id']}?payment_token={$paymentToken}",
                'expires_at' => now()->addHour()->toIso8601String(),
                'raw_response' => $paymentKeyResponse['data'],
            ];
        } catch (\Exception $e) {
            Log::channel('payment')->error('Paymob Payment Initiation Failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'gateway' => 'paymob',
                'error' => $e->getMessage(),
                'error_code' => 'PAYMOB_INIT_FAILED',
            ];
        }
    }

    public function verifyPayment(array $data): array
    {
        try {
            $transactionId = $data['transaction_id'] ?? null;

            if (!$transactionId) {
                throw new \Exception('Transaction ID is required');
            }

            $authToken = $this->getAuthToken();

            $response = $this->makeRequest('GET', "/acceptance/transactions/{$transactionId}", [
                'auth_token' => $authToken,
            ]);

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to verify payment');
            }

            $transactionData = $response['data'];

            return [
                'success' => true,
                'gateway' => 'paymob',
                'transaction_id' => $transactionId,
                'order_id' => $transactionData['order']['merchant_order_id'] ?? null,
                'status' => $this->mapPaymobStatus($transactionData),
                'amount' => $transactionData['amount_cents'] / 100,
                'currency' => $transactionData['currency'],
                'is_capture' => $transactionData['is_capture'] ?? false,
                'is_refunded' => $transactionData['is_refunded'] ?? false,
                'is_voided' => $transactionData['is_voided'] ?? false,
                'payment_details' => $transactionData,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'gateway' => 'paymob',
                'error' => $e->getMessage(),
                'error_code' => 'PAYMOB_VERIFY_FAILED',
            ];
        }
    }

    public function refundPayment(string $transactionId, float $amount, string $reason = ''): array
    {
        try {
            $authToken = $this->getAuthToken();

            $response = $this->makeRequest('POST', '/acceptance/void_refund/refund', [
                'auth_token' => $authToken,
                'transaction_id' => $transactionId,
                'amount_cents' => $amount * 100,
            ]);

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to process refund');
            }

            return [
                'success' => true,
                'gateway' => 'paymob',
                'refund_id' => $response['data']['id'] ?? null,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'refund_status' => 'pending',
                'raw_response' => $response['data'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'gateway' => 'paymob',
                'error' => $e->getMessage(),
                'error_code' => 'PAYMOB_REFUND_FAILED',
            ];
        }
    }

    public function checkPaymentStatus(string $transactionId): array
    {
        return $this->verifyPayment(['transaction_id' => $transactionId]);
    }

    public function isWebhookValid(array $data): bool
    {
        if (!isset($data['hmac'])) {
            return false;
        }

        $hmac = $data['hmac'];
        unset($data['hmac']);

        ksort($data);

        $concatenated = '';
        foreach ($data as $value) {
            if (is_array($value)) {
                $concatenated .= json_encode($value, JSON_UNESCAPED_SLASHES);
            } else {
                $concatenated .= $value;
            }
        }

        $calculatedHmac = hash_hmac('sha512', $concatenated, $this->config['hmac_secret'] ?? '');

        return hash_equals($hmac, $calculatedHmac);
    }

    public function handleWebhook(array $data): array
    {
        try {
            if (!$this->isWebhookValid($data)) {
                throw new \Exception('Invalid HMAC signature');
            }

            $obj = $data['obj'] ?? [];
            $type = $data['type'] ?? '';

            $result = [
                'success' => true,
                'gateway' => 'paymob',
                'event_type' => $type,
                'transaction_id' => $obj['id'] ?? null,
                'order_id' => $obj['merchant_order_id'] ?? $obj['order']['merchant_order_id'] ?? null,
                'handled' => false,
            ];

            switch ($type) {
                case 'TRANSACTION':
                    if ($obj['success'] ?? false) {
                        $result['status'] = 'success';
                        $result['handled'] = true;
                    } else {
                        $result['status'] = 'failed';
                        $result['handled'] = true;
                    }
                    break;

                case 'TOKEN':
                    $result['status'] = 'token_created';
                    break;

                case 'DISPUTE':
                    $result['status'] = 'dispute';
                    $result['handled'] = true;
                    break;

                default:
                    $result['status'] = 'unknown';
            }

            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'gateway' => 'paymob',
                'error' => $e->getMessage(),
                'error_code' => 'PAYMOB_WEBHOOK_FAILED',
            ];
        }
    }

    private function mapPaymobStatus(array $transaction): string
    {
        if ($transaction['is_refunded'] ?? false) {
            return 'refunded';
        }

        if ($transaction['is_voided'] ?? false) {
            return 'voided';
        }

        if ($transaction['is_capture'] ?? false) {
            return 'captured';
        }

        if ($transaction['success'] ?? false) {
            return 'success';
        }

        if ($transaction['pending'] ?? false) {
            return 'pending';
        }

        return 'failed';
    }
}
