<?php

namespace App\Services\Payment\Gateways;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TamaraGateway extends BaseGateway
{
    protected function initializeConfig(): void
    {
        $this->config = config('services.tamara', []);
    }

    protected function getGatewayName(): string
    {
        return 'tamara';
    }

    protected function getBaseUrl(): string
    {
        return $this->isSandbox
            ? 'https://api-sandbox.tamara.co'
            : 'https://api.tamara.co';
    }

    protected function fetchAuthToken(): string
    {
        $response = Http::post($this->getBaseUrl() . '/auth/tokens', [
            'username' => $this->config['username'],
            'password' => $this->config['password'],
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Failed to get Tamara auth token');
    }

    public function initiatePayment(array $data): array
    {
        try {
            // التحقق من البيانات المطلوبة
            $required = ['order_id', 'amount', 'customer', 'items'];
            if (!$this->validateRequired($data, $required)) {
                throw new \Exception('Missing required payment data');
            }

            $authToken = $this->getAuthToken();

            // إنشاء طلب الدفع
            $paymentData = [
                'order_reference_id' => $data['order_id'],
                'total_amount' => [
                    'amount' => $data['amount'],
                    'currency' => $this->currency,
                ],
                'description' => $data['description'] ?? 'Order Payment',
                'country_code' => 'SA',
                'payment_type' => 'PAY_BY_INSTALMENTS',
                'locale' => 'ar_SA',
                'platform' => 'API',
                'items' => $data['items'],
                'consumer' => $this->prepareConsumerData($data['customer']),
                'billing_address' => $data['billing_address'] ?? $this->getDefaultAddress(),
                'shipping_address' => $data['shipping_address'] ?? $this->getDefaultAddress(),
                'merchant_url' => [
                    'success' => $data['callback_urls']['success'] ?? route('payment.callback.success'),
                    'failure' => $data['callback_urls']['failure'] ?? route('payment.callback.failure'),
                    'cancel' => $data['callback_urls']['cancel'] ?? route('payment.callback.cancel'),
                    'notification' => route('payment.webhook.tamara'),
                ],
            ];

            // إضافة خيارات التقسيط إذا كانت موجودة
            if (isset($data['installments'])) {
                $paymentData['instalments'] = $data['installments'];
            }

            $response = $this->makeRequest(
                'POST',
                '/checkout',
                $paymentData,
                ['Authorization' => "Bearer {$authToken}"]
            );

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to initiate payment');
            }

            return [
                'success' => true,
                'gateway' => 'tamara',
                'payment_id' => $response['data']['order_id'] ?? null,
                'checkout_url' => $response['data']['checkout_url'] ?? null,
                'checkout_id' => $response['data']['checkout_id'] ?? null,
                'expires_at' => now()->addHours(2)->toIso8601String(),
                'raw_response' => $response['data'],
            ];
        } catch (\Exception $e) {
            Log::channel('payment')->error('Tamara Payment Initiation Failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'gateway' => 'tamara',
                'error' => $e->getMessage(),
                'error_code' => 'TAMARA_INIT_FAILED',
            ];
        }
    }

    public function verifyPayment(array $data): array
    {
        try {
            $orderId = $data['order_id'] ?? null;
            $paymentId = $data['payment_id'] ?? null;

            if (!$paymentId) {
                throw new \Exception('Payment ID is required');
            }

            $authToken = $this->getAuthToken();

            $response = $this->makeRequest(
                'GET',
                "/orders/{$paymentId}",
                [],
                ['Authorization' => "Bearer {$authToken}"]
            );

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to verify payment');
            }

            $paymentData = $response['data'];

            $status = $this->mapPaymentStatus($paymentData['status'] ?? '');

            return [
                'success' => true,
                'gateway' => 'tamara',
                'payment_id' => $paymentId,
                'order_id' => $paymentData['order_reference_id'] ?? $orderId,
                'status' => $status,
                'amount' => $paymentData['total_amount']['amount'] ?? 0,
                'currency' => $paymentData['total_amount']['currency'] ?? 'SAR',
                'captured' => $paymentData['captured'] ?? false,
                'refunded' => $paymentData['refunded'] ?? false,
                'payment_details' => $paymentData,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'gateway' => 'tamara',
                'error' => $e->getMessage(),
                'error_code' => 'TAMARA_VERIFY_FAILED',
            ];
        }
    }

    public function refundPayment(string $transactionId, float $amount, string $reason = ''): array
    {
        try {
            $authToken = $this->getAuthToken();

            $payload = [
                'order_id' => $transactionId,
                'refund_amount' => [
                    'amount' => $amount,
                    'currency' => $this->currency,
                ],
                'comment' => $reason,
                'refund_reason' => 'customer_request',
            ];

            $response = $this->makeRequest(
                'POST',
                '/refunds',
                $payload,
                ['Authorization' => "Bearer {$authToken}"]
            );

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to process refund');
            }

            return [
                'success' => true,
                'gateway' => 'tamara',
                'refund_id' => $response['data']['refund_id'] ?? null,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'refund_status' => 'pending',
                'raw_response' => $response['data'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'gateway' => 'tamara',
                'error' => $e->getMessage(),
                'error_code' => 'TAMARA_REFUND_FAILED',
            ];
        }
    }

    public function checkPaymentStatus(string $transactionId): array
    {
        return $this->verifyPayment(['payment_id' => $transactionId]);
    }

    public function isWebhookValid(array $data): bool
    {
        $token = $data['token'] ?? '';
        $expectedToken = config('services.tamara.webhook_token');

        if (!$expectedToken || !$token) {
            return false;
        }

        return hash_equals($expectedToken, $token);
    }

    public function handleWebhook(array $data): array
    {
        try {
            if (!$this->isWebhookValid($data)) {
                throw new \Exception('Invalid webhook signature');
            }

            $eventType = $data['event_type'] ?? '';
            $orderId = $data['order_reference_id'] ?? null;
            $paymentId = $data['order_id'] ?? null;

            $result = [
                'success' => true,
                'gateway' => 'tamara',
                'event_type' => $eventType,
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'handled' => false,
            ];

            switch ($eventType) {
                case 'order_approved':
                case 'payment_success':
                    $result['status'] = 'approved';
                    $result['handled'] = true;
                    break;

                case 'order_declined':
                case 'payment_failed':
                    $result['status'] = 'declined';
                    $result['handled'] = true;
                    break;

                case 'order_cancelled':
                    $result['status'] = 'cancelled';
                    $result['handled'] = true;
                    break;

                case 'order_refunded':
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
                'gateway' => 'tamara',
                'error' => $e->getMessage(),
                'error_code' => 'TAMARA_WEBHOOK_FAILED',
            ];
        }
    }

    private function prepareConsumerData(array $customer): array
    {
        return [
            'first_name' => $customer['first_name'] ?? 'Customer',
            'last_name' => $customer['last_name'] ?? '',
            'phone_number' => $customer['phone'] ?? '+966500000000',
            'email' => $customer['email'] ?? 'customer@example.com',
            'national_id' => $customer['national_id'] ?? '',
            'date_of_birth' => $customer['date_of_birth'] ?? null,
            'is_first_order' => $customer['is_first_order'] ?? true,
        ];
    }

    private function getDefaultAddress(): array
    {
        return [
            'first_name' => 'Customer',
            'last_name' => '',
            'line1' => 'Address not specified',
            'city' => 'Riyadh',
            'country_code' => 'SA',
            'phone_number' => '+966500000000',
        ];
    }

    private function mapPaymentStatus(string $status): string
    {
        $statusMap = [
            'approved' => 'approved',
            'authorised' => 'authorized',
            'captured' => 'captured',
            'declined' => 'declined',
            'cancelled' => 'cancelled',
            'expired' => 'expired',
            'refunded' => 'refunded',
            'partially_refunded' => 'partially_refunded',
        ];

        return $statusMap[$status] ?? 'pending';
    }
}
