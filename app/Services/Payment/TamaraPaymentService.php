<?php

namespace App\Services\Payment;

use App\Services\Payment\Interfaces\PaymentServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TamaraPaymentService implements PaymentServiceInterface
{
    private $client;
    private $config;
    private $apiUrl;

    public function __construct()
    {
        $this->config = config('services.tamara');

        // Determine API URL based on environment
        $this->apiUrl = $this->config['sandbox']
            ? 'https://api-sandbox.tamara.co'
            : 'https://api.tamara.co';

        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config['token'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Create a payment order
     */
    public function createPayment(array $orderData): array
    {
        try {
            $response = $this->client->post('/checkout', [
                'json' => $this->prepareCheckoutData($orderData),
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'checkout_id' => $data['checkout_id'],
                'checkout_url' => $data['checkout_url'],
                'order_reference_id' => $data['order_reference_id'],
                'data' => $data,
            ];
        } catch (RequestException $e) {
            Log::error('Tamara payment creation failed', [
                'error' => $e->getMessage(),
                'order_data' => $orderData,
            ]);

            return [
                'success' => false,
                'error' => $this->parseError($e),
            ];
        }
    }

    /**
     * Authorize payment
     */
    public function authorizePayment(string $orderId): array
    {
        try {
            $response = $this->client->post("/orders/{$orderId}/authorise");

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'order_id' => $data['order_id'],
                'status' => $data['status'],
                'authorised_amount' => $data['authorised_amount'] ?? null,
                'data' => $data,
            ];
        } catch (RequestException $e) {
            Log::error('Tamara payment authorization failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            return [
                'success' => false,
                'error' => $this->parseError($e),
            ];
        }
    }

    /**
     * Capture payment
     */
    public function capturePayment(string $orderId, array $captureData): array
    {
        try {
            $response = $this->client->post("/orders/{$orderId}/capture", [
                'json' => $captureData,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'capture_id' => $data['capture_id'],
                'status' => $data['status'],
                'data' => $data,
            ];
        } catch (RequestException $e) {
            Log::error('Tamara payment capture failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'capture_data' => $captureData,
            ]);

            return [
                'success' => false,
                'error' => $this->parseError($e),
            ];
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment(string $orderId, array $refundData): array
    {
        try {
            $response = $this->client->post("/orders/{$orderId}/refund", [
                'json' => $refundData,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'refund_id' => $data['refund_id'],
                'status' => $data['status'],
                'data' => $data,
            ];
        } catch (RequestException $e) {
            Log::error('Tamara payment refund failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'refund_data' => $refundData,
            ]);

            return [
                'success' => false,
                'error' => $this->parseError($e),
            ];
        }
    }

    /**
     * Cancel payment
     */
    public function cancelPayment(string $orderId): array
    {
        try {
            $response = $this->client->post("/orders/{$orderId}/cancel");

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'order_id' => $data['order_id'],
                'status' => $data['status'],
                'data' => $data,
            ];
        } catch (RequestException $e) {
            Log::error('Tamara payment cancellation failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            return [
                'success' => false,
                'error' => $this->parseError($e),
            ];
        }
    }

    /**
     * Get order details
     */
    public function getOrder(string $orderId): array
    {
        try {
            $response = $this->client->get("/orders/{$orderId}");

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'order' => $data,
            ];
        } catch (RequestException $e) {
            Log::error('Tamara get order failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            return [
                'success' => false,
                'error' => $this->parseError($e),
            ];
        }
    }

    /**
     * Get payment methods for customer
     */
    public function getPaymentMethods(array $customerData): array
    {
        $cacheKey = 'tamara_methods_' . md5(json_encode($customerData));

        return Cache::remember($cacheKey, 3600, function () use ($customerData) {
            try {
                $response = $this->client->get('/checkout/payment-methods', [
                    'query' => [
                        'country' => $customerData['country'] ?? 'SA',
                        'currency' => $customerData['currency'] ?? 'SAR',
                        'order_value' => $customerData['order_value'] ?? 0,
                    ],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);

                return [
                    'success' => true,
                    'methods' => $data,
                ];
            } catch (RequestException $e) {
                Log::error('Tamara get payment methods failed', [
                    'error' => $e->getMessage(),
                    'customer_data' => $customerData,
                ]);

                return [
                    'success' => false,
                    'error' => $this->parseError($e),
                ];
            }
        });
    }

    /**
     * Handle webhook notification
     */
    public function handleWebhook(array $payload): array
    {
        try {
            // Verify webhook signature
            if (!$this->verifyWebhookSignature($payload)) {
                throw new \Exception('Invalid webhook signature');
            }

            $eventType = $payload['event_type'];
            $orderId = $payload['order_id'];

            // Process based on event type
            switch ($eventType) {
                case 'order_approved':
                    return $this->handleOrderApproved($orderId, $payload);

                case 'order_authorised':
                    return $this->handleOrderAuthorised($orderId, $payload);

                    // case 'order_captured':
                    //     return $this->handleOrderCaptured($orderId, $payload);

                    // case 'order_declined':
                    //     return $this->handleOrderDeclined($orderId, $payload);

                    // case 'order_cancelled':
                    //     return $this->handleOrderCancelled($orderId, $payload);

                default:
                    Log::warning("Unhandled Tamara webhook event: {$eventType}", $payload);
                    return ['success' => false, 'error' => 'Unhandled event type'];
            }
        } catch (\Exception $e) {
            Log::error('Tamara webhook handling failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Prepare checkout data for Tamara API
     */
    private function prepareCheckoutData(array $orderData): array
    {
        return [
            'order_reference_id' => $orderData['order_reference_id'],
            'total_amount' => [
                'amount' => $orderData['total_amount'],
                'currency' => $orderData['currency'] ?? 'SAR',
            ],
            'description' => $orderData['description'] ?? 'Order Payment',
            'country_code' => $orderData['country_code'] ?? 'SA',
            'payment_type' => $orderData['payment_type'] ?? 'PAY_BY_INSTALMENTS',
            'locale' => $orderData['locale'] ?? 'en_US',
            'items' => $orderData['items'] ?? [],
            'consumer' => $orderData['consumer'] ?? [],
            'shipping_address' => $orderData['shipping_address'] ?? [],
            'billing_address' => $orderData['billing_address'] ?? [],
            'discount' => $orderData['discount'] ?? null,
            'tax_amount' => $orderData['tax_amount'] ?? null,
            'shipping_amount' => $orderData['shipping_amount'] ?? null,
            'merchant_url' => [
                'success' => $orderData['success_url'] ?? route('payment.success'),
                'failure' => $orderData['failure_url'] ?? route('payment.failure'),
                'cancel' => $orderData['cancel_url'] ?? route('payment.cancel'),
                'notification' => $orderData['webhook_url'] ?? route('payment.webhook.tamara'),
            ],
            'platform' => 'Laravel',
            'expires_in_minutes' => 60,
        ];
    }

    /**
     * Verify webhook signature
     */
    private function verifyWebhookSignature(array $payload): bool
    {
        if (!isset($payload['signature'])) {
            return false;
        }

        $computedSignature = hash_hmac(
            'sha256',
            json_encode($payload['data']),
            $this->config['webhook_secret']
        );

        return hash_equals($computedSignature, $payload['signature']);
    }

    /**
     * Handle order approved webhook
     */
    private function handleOrderApproved(string $orderId, array $payload): array
    {
        // Implement your business logic here
        // e.g., update order status, send notifications, etc.

        return [
            'success' => true,
            'message' => 'Order approved handled successfully',
            'order_id' => $orderId,
        ];
    }

    /**
     * Handle order authorised webhook
     */
    private function handleOrderAuthorised(string $orderId, array $payload): array
    {
        // Implement your business logic here

        return [
            'success' => true,
            'message' => 'Order authorised handled successfully',
            'order_id' => $orderId,
        ];
    }

    /**
     * Parse error from exception
     */
    private function parseError(RequestException $e): string
    {
        if ($e->hasResponse()) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
            return $response['message'] ?? $response['detail'] ?? $e->getMessage();
        }

        return $e->getMessage();
    }

    /**
     * Get service name
     */
    public function getServiceName(): string
    {
        return 'tamara';
    }
}
