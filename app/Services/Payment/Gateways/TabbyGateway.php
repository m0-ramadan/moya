<?php

namespace App\Services\Payment\Gateways;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TabbyGateway extends BaseGateway
{
    protected function initializeConfig(): void
    {
        $this->config = config('services.tabby', []);
    }

    protected function getGatewayName(): string
    {
        return 'tabby';
    }

    protected function getBaseUrl(): string
    {
        return $this->isSandbox
            ? 'https://api.tabby.ai/api/v2'
            : 'https://api.tabby.ai/api/v2';
    }

    protected function fetchAuthToken(): string
    {
        return $this->config['secret_key'];
    }

    public function initiatePayment(array $data): array
    {
        try {
            $authToken = $this->fetchAuthToken();

            $payload = [
                'payment' => [
                    'amount' => $data['amount'],
                    'currency' => $this->currency,
                    'description' => $data['description'] ?? 'Order Payment',
                    'buyer' => [
                        'phone' => $data['customer']['phone'] ?? '+966500000000',
                        'email' => $data['customer']['email'] ?? 'customer@example.com',
                        'name' => trim(($data['customer']['first_name'] ?? '') . ' ' . ($data['customer']['last_name'] ?? '')),
                    ],
                    'shipping_address' => $data['shipping_address'] ?? 'no',
                    'order' => [
                        'reference_id' => $data['order_id'],
                        'items' => $data['items'] ?? [],
                    ],
                    'buyer_history' => [
                        'registered_since' => now()->subYear()->toIso8601String(),
                        'loyalty_level' => 0,
                    ],
                ],
                'lang' => 'ar',
                'merchant_code' => $this->config['merchant_code'],
                'merchant_urls' => [
                    'success' => $data['callback_urls']['success'] ?? route('payment.callback.success'),
                    'failure' => $data['callback_urls']['failure'] ?? route('payment.callback.failure'),
                    'cancel' => $data['callback_urls']['cancel'] ?? route('payment.callback.cancel'),
                    'webhook' => route('payment.webhook.tabby'),
                ],
            ];

            $response = $this->makeRequest(
                'POST',
                '/checkout',
                $payload,
                [
                    'Authorization' => "Bearer {$authToken}",
                    'Content-Type' => 'application/json',
                ]
            );

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to initiate Tabby payment');
            }

            return [
                'success' => true,
                'gateway' => 'tabby',
                'payment_id' => $response['data']['id'] ?? null,
                'checkout_url' => $response['data']['payment']['web_url'] ?? null,
                'status' => $response['data']['status'] ?? 'created',
                'expires_at' => now()->addHours(24)->toIso8601String(),
                'raw_response' => $response['data'],
            ];
        } catch (\Exception $e) {
            Log::channel('payment')->error('Tabby Payment Initiation Failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'gateway' => 'tabby',
                'error' => $e->getMessage(),
                'error_code' => 'TABBY_INIT_FAILED',
            ];
        }
    }

    public function verifyPayment(array $data): array
    {
        try {
            $paymentId = $data['payment_id'] ?? null;

            if (!$paymentId) {
                throw new \Exception('Payment ID is required');
            }

            $authToken = $this->fetchAuthToken();

            $response = $this->makeRequest(
                'GET',
                "/payments/{$paymentId}",
                [],
                [
                    'Authorization' => "Bearer {$authToken}",
                    'Content-Type' => 'application/json',
                ]
            );

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to verify Tabby payment');
            }

            $paymentData = $response['data'];

            return [
                'success' => true,
                'gateway' => 'tabby',
                'payment_id' => $paymentId,
                'order_id' => $paymentData['order']['reference_id'] ?? null,
                'status' => $this->mapTabbyStatus($paymentData['status'] ?? ''),
                'amount' => $paymentData['amount'] ?? 0,
                'currency' => $paymentData['currency'] ?? 'SAR',
                'buyer' => $paymentData['buyer'] ?? [],
                'payment_details' => $paymentData,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'gateway' => 'tabby',
                'error' => $e->getMessage(),
                'error_code' => 'TABBY_VERIFY_FAILED',
            ];
        }
    }

    public function refundPayment(string $transactionId, float $amount, string $reason = ''): array
    {
        try {
            $authToken = $this->fetchAuthToken();

            $payload = [
                'amount' => $amount,
                'reason' => $reason,
                'currency' => $this->currency,
            ];

            $response = $this->makeRequest(
                'POST',
                "/payments/{$transactionId}/refunds",
                $payload,
                [
                    'Authorization' => "Bearer {$authToken}",
                    'Content-Type' => 'application/json',
                ]
            );

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to process Tabby refund');
            }

            return [
                'success' => true,
                'gateway' => 'tabby',
                'refund_id' => $response['data']['id'] ?? null,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'refund_status' => 'created',
                'raw_response' => $response['data'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'gateway' => 'tabby',
                'error' => $e->getMessage(),
                'error_code' => 'TABBY_REFUND_FAILED',
            ];
        }
    }

    public function checkPaymentStatus(string $transactionId): array
    {
        return $this->verifyPayment(['payment_id' => $transactionId]);
    }

    public function isWebhookValid(array $data): bool
    {
        $signature = $data['signature'] ?? '';
        $webhookSecret = $this->config['webhook_secret'] ?? '';

        if (!$webhookSecret || !$signature) {
            return false;
        }

        $payload = json_encode($data);
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        return hash_equals($expectedSignature, $signature);
    }

    public function handleWebhook(array $data): array
    {
        try {
            if (!$this->isWebhookValid($data)) {
                throw new \Exception('Invalid webhook signature');
            }

            $eventType = $data['event'] ?? '';
            $paymentId = $data['data']['id'] ?? null;

            $result = [
                'success' => true,
                'gateway' => 'tabby',
                'event_type' => $eventType,
                'payment_id' => $paymentId,
                'handled' => false,
            ];

            switch ($eventType) {
                case 'payment_approved':
                    $result['status'] = 'approved';
                    $result['handled'] = true;
                    break;

                case 'payment_declined':
                    $result['status'] = 'declined';
                    $result['handled'] = true;
                    break;

                case 'payment_expired':
                    $result['status'] = 'expired';
                    $result['handled'] = true;
                    break;

                case 'payment_captured':
                    $result['status'] = 'captured';
                    $result['handled'] = true;
                    break;

                case 'payment_refunded':
                    $result['status'] = 'refunded';
                    $result['handled'] = true;
                    break;

                default:
                    $result['status'] = 'unknown';
            }

            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'gateway' => 'tabby',
                'error' => $e->getMessage(),
                'error_code' => 'TABBY_WEBHOOK_FAILED',
            ];
        }
    }

    private function mapTabbyStatus(string $status): string
    {
        $statusMap = [
            'created' => 'created',
            'approved' => 'approved',
            'authorized' => 'authorized',
            'captured' => 'captured',
            'declined' => 'declined',
            'expired' => 'expired',
            'refunded' => 'refunded',
            'voided' => 'cancelled',
        ];

        return $statusMap[$status] ?? 'unknown';
    }
}
