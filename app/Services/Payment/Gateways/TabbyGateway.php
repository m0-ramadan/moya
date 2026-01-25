<?php

namespace App\Services\Payment\Gateways;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Contracts\Payment\PaymentGatewayInterface;

class TabbyGateway extends BaseGateway
{
    // ----------------------------
    // Abstract methods implementation from BaseGateway
    // ----------------------------

protected function getBaseUrl(): string
{
    return $this->isSandbox
        ? 'https://api.tabby.ai/api/v2'
        : 'https://api.tabby.ai/api/v2';
}


    protected function getGatewayName(): string
    {
        return 'tabby';
    }

    protected function initializeConfig(): void
    {
        $this->config = config('services.tabby', []);
        $this->currency = config('services.tabby.currency', 'SAR');
        $this->isSandbox = config('services.tabby.sandbox', true);
    }

    // ----------------------------
    // Interface methods implementation
    // ----------------------------

    public function createPaymentOrder(array $data): array
    {
        return $this->initiatePayment($data);
    }

    public function initiatePayment(array $data): array
    {
        try {
            $authToken = $this->getAuthToken('tabby');
            if (!$authToken) {
                throw new \Exception('Tabby authentication failed');
            }

            // التحضير للبيانات المطلوبة من Tabby
            $sessionData = $this->prepareTabbySessionData($data);
            Log::channel('payment')->debug('Tabby session payload', [
                'payload' => $sessionData,
                'auth_token_prefix' => substr($authToken, 0, 10) . '...',
            ]);

            // استخدام makeRequest من BaseGateway
            $response = $this->makeRequest(
                'POST',
                '/checkout',
                $sessionData,
                [
                    'Authorization' => "Bearer {$authToken}",
                ]
            );

            if (!$response['success']) {
                Log::channel('payment')->error('Tabby API Error Response', [
                    'error' => $response['error'] ?? 'Unknown error',
                    'status' => $response['status'] ?? null,
                    'raw_response' => $response['raw_response'] ?? null,
                ]);
                throw new \Exception($response['error'] ?? 'Failed to create Tabby session');
            }

            $responseData = $response['data'];

            // التحقق من توفر منتج التقسيط
            // $installmentProduct = $responseData['configuration']['available_products']['installments'][0] ?? null;
            
            // if (!$installmentProduct || !($installmentProduct['is_available'] ?? false)) {
            //     throw new \Exception('Tabby installments not available for this transaction');
            // }

            return [
                'success' => true,
                'gateway' => 'tabby',
                'session_id' => $responseData['id'] ?? null,
                'payment_id' => $responseData['payment']['id'] ?? null,
                'payment_url' => $installmentProduct['web_url'] ?? null,
                'checkout_url' => $installmentProduct['web_url'] ?? null,
                'qr_code_url' => $installmentProduct['qr_code'] ?? null,
                'status' => $responseData['status'] ?? 'created',
                'payment_status' => $responseData['payment']['status'] ?? 'CREATED',
                'order_id' => $data['order_id'],
                'expires_at' => $responseData['payment']['expires_at'] ?? now()->addHours(24)->toIso8601String(),
                'raw_response' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::channel('payment')->error('Tabby Payment Initiation Failed', [
                'order_id' => $data['order_id'] ?? null,
                'amount' => $data['amount'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'gateway' => 'tabby',
                'error' => $e->getMessage(),
                'error_code' => 'TABBY_INIT_FAILED',
            ];
        }
    }

    private function prepareTabbySessionData(array $data): array
    {
        $customer = $data['customer'] ?? [];
        $shippingAddress = $data['shipping_address'] ?? [];
        $billingAddress = $data['billing_address'] ?? $shippingAddress;
        $items = $data['items'] ?? [];
        $orderHistory = $this->getCustomerOrderHistory($customer['email'] ?? null, $data['order_id'] ?? null);

        // تحضير عناصر الطلب الحالي
        $orderItems = [];
        foreach ($items as $item) {
            $orderItems[] = [
                'title' => $item['name'] ?? 'Water Delivery Service',
                'description' => $item['description'] ?? 'Water delivery to your location',
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $this->formatAmount($item['unit_price'] ?? $data['amount'] ?? 0),
                'category' => $item['category'] ?? 'services',
                'reference_id' => $item['sku'] ?? 'ITEM-' . uniqid(),
                'discount_amount' => $this->formatAmount($item['discount_amount'] ?? 0),
                'is_refundable' => $item['is_refundable'] ?? true,
            ];
        }

        // إذا لم تكن هناك عناصر، نضيف عنصر افتراضي
        if (empty($orderItems)) {
            $orderItems[] = [
                'title' => 'Water Delivery Service',
                'description' => 'Water delivery to your location',
                'quantity' => 1,
                'unit_price' => $this->formatAmount($data['amount'] ?? 0),
                'category' => 'services',
                'reference_id' => 'SERVICE-' . ($data['order_id'] ?? uniqid()),
                'discount_amount' => '0.00',
                'is_refundable' => true,
            ];
        }

        return [
            'payment' => [
                'amount' => $this->formatAmount($data['amount'] ?? 0),
                'currency' => $this->currency,
                'description' => $data['description'] ?? 'Water Delivery Order',
                'buyer' => [
                    'name' => trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? 'Customer')),
                    'email' => $customer['email'] ?? 'customer@example.com',
                    'phone' => $customer['phone'] ?? '+966500000000',
                    'dob' => $customer['dob'] ?? null,
                ],
                'shipping_address' => [
                    'city' => $shippingAddress['city'] ?? 'Riyadh',
                    'address' => $shippingAddress['address_line1'] ?? 'Not specified',
                    'zip' => $shippingAddress['postal_code'] ?? '11111',
                ],
                'order' => [
                    'reference_id' => $data['order_id'] ?? 'ORD-' . time(),
                    'items' => $orderItems,
                    'updated_at' => now()->toIso8601String(),
                    'tax_amount' => $this->formatAmount($data['tax_amount'] ?? 0),
                    'shipping_amount' => $this->formatAmount($data['shipping_amount'] ?? 0),
                    'discount_amount' => $this->formatAmount($data['discount_amount'] ?? 0),
                ],
                'buyer_history' => [
                    'registered_since' => $customer['created_at'] ?? now()->subYear()->toIso8601String(),
                    'loyalty_level' => $customer['order_count'] ?? 0,
                    'wishlist_count' => 0,
                    'is_social_networks_connected' => false,
                    'is_phone_number_verified' => true,
                    'is_email_verified' => true,
                ],
                'order_history' => $orderHistory,
                'meta' => [
                    'customer' => '#customer-' . ($customer['id'] ?? uniqid()),
                    'order_id' => '#' . ($data['order_id'] ?? 'unknown'),
                ],
                'attachment' => [
                    'body' => json_encode([
                        'payment_history_simple' => [
                            'unique_account_identifier' => 'customer-' . ($customer['id'] ?? uniqid()),
                            'paid_before_flag' => ($customer['order_count'] ?? 0) > 0,
                            'date_of_last_paid_purchase' => $customer['last_order_date'] ?? null,
                            'date_of_first_paid_purchase' => $customer['first_order_date'] ?? null,
                        ],
                    ]),
                    'content_type' => 'application/vnd.tabby.v1+json',
                ],
            ],
            'lang' => 'ar',
            'merchant_code' => $this->config['merchant_code'] ?? '',
            'merchant_urls' => [
                'success' => $data['callback_urls']['success'] ?? route('payment.success.tabby'),
                'failure' => $data['callback_urls']['failure'] ?? route('payment.failure.tabby'),
                'cancel' => $data['callback_urls']['cancel'] ?? route('payment.cancel.tabby'),
            ],
        ];
    }

    private function getCustomerOrderHistory(?string $email, ?string $excludeOrderId): array
    {
        // هنا يمكنك جلب تاريخ طلبات العميل من قاعدة البيانات
        // هذا مثال افتراضي
        
        if (!$email) {
            return [];
        }

        try {
            // جلب الطلبات السابقة للعميل (استثناء الطلب الحالي)
            // $orders = Order::where('user_email', $email)
            //     ->where('id', '!=', $excludeOrderId)
            //     ->orderBy('created_at', 'desc')
            //     ->limit(5)
            //     ->get();

            // لأغراض الاختبار، نرجع بيانات افتراضية
            return [
                [
                    'purchased_at' => now()->subDays(30)->toIso8601String(),
                    'amount' => '100.00',
                    'status' => 'delivered',
                    'buyer' => [
                        'name' => 'Customer Name',
                        'email' => $email,
                        'phone' => '+966500000000',
                    ],
                    'shipping_address' => [
                        'city' => 'Riyadh',
                        'address' => 'Sample Address',
                        'zip' => '11111',
                    ],
                    'payment_method' => 'card',
                    'items' => [
                        [
                            'title' => 'Water Delivery',
                            'quantity' => 1,
                            'unit_price' => '100.00',
                            'category' => 'services',
                        ],
                    ],
                ],
            ];
        } catch (\Exception $e) {
            Log::channel('payment')->warning('Failed to fetch customer order history', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    private function formatAmount(float $amount): string
    {
        // Tabby يتطلب سلسلة نصية للمبالغ مع منزلتين عشريتين
        return number_format($amount, 2, '.', '');
    }

    public function verifyTransaction(array $data): array
    {
        return $this->verifyPayment($data);
    }

    public function verifyPayment(array $data): array
    {
        try {
            $paymentId = $data['payment_id'] ?? $data['id'] ?? null;
            $sessionId = $data['session_id'] ?? null;
            $orderId = $data['order_id'] ?? null;

            if (!$paymentId && !$sessionId && !$orderId) {
                throw new \Exception('Payment ID, Session ID or Order ID is required');
            }

            $authToken = $this->getAuthToken('tabby');
            if (!$authToken) {
                throw new \Exception('Tabby authentication failed');
            }

            // الأولوية: sessionId ثم paymentId ثم orderId
            if ($sessionId) {
                $response = $this->makeRequest(
                    'GET',
                    "/checkout/{$sessionId}",
                    [],
                    [
                        'Authorization' => "Bearer {$authToken}",
                    ]
                );
            } elseif ($paymentId) {
                $response = $this->makeRequest(
                    'GET',
                    "/payments/{$paymentId}",
                    [],
                    [
                        'Authorization' => "Bearer {$authToken}",
                    ]
                );
            } else {
                // البحث باستخدام order reference
                $response = $this->makeRequest(
                    'GET',
                    "/payments?order.reference_id={$orderId}",
                    [],
                    [
                        'Authorization' => "Bearer {$authToken}",
                    ]
                );
            }

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to verify Tabby payment');
            }

            $responseData = $response['data'];
            
            // إذا كان الرد قائمة، خذ أول عنصر
            if (isset($responseData[0])) {
                $responseData = $responseData[0];
            }

            $paymentData = $responseData['payment'] ?? $responseData;
            $status = $this->mapTabbyStatus($paymentData['status'] ?? '');

            return [
                'success' => true,
                'gateway' => 'tabby',
                'session_id' => $sessionId ?? $responseData['id'] ?? null,
                'payment_id' => $paymentData['id'] ?? $paymentId,
                'order_id' => $paymentData['order']['reference_id'] ?? $orderId,
                'status' => $status,
                'amount' => $paymentData['amount'] ?? 0,
                'currency' => $paymentData['currency'] ?? $this->currency,
                'is_paid' => in_array($status, ['AUTHORIZED', 'CLOSED', 'CAPTURED']),
                'buyer' => $paymentData['buyer'] ?? [],
                'payment_details' => $paymentData,
                'raw_response' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::channel('payment')->error('Tabby Payment Verification Failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'gateway' => 'tabby',
                'error' => $e->getMessage(),
                'error_code' => 'TABBY_VERIFY_FAILED',
            ];
        }
    }

    public function refund(string $transactionId, float $amount, string $reason = ''): array
    {
        try {
            $authToken = $this->getAuthToken('tabby');
            if (!$authToken) {
                throw new \Exception('Tabby authentication failed');
            }

            $payload = [
                'amount' => $this->formatAmount($amount),
                'reason' => $reason ?: 'Customer request',
                'currency' => $this->currency,
            ];

            Log::channel('payment')->debug('Tabby refund payload', [
                'transaction_id' => $transactionId,
                'payload' => $payload,
            ]);

            $response = $this->makeRequest(
                'POST',
                "/payments/{$transactionId}/refunds",
                $payload,
                [
                    'Authorization' => "Bearer {$authToken}",
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
            Log::channel('payment')->error('Tabby Refund Failed', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'gateway' => 'tabby',
                'error' => $e->getMessage(),
                'error_code' => 'TABBY_REFUND_FAILED',
            ];
        }
    }

    public function refundPayment(string $transactionId, float $amount, string $reason = ''): array
    {
        return $this->refund($transactionId, $amount, $reason);
    }

    public function getTransactionStatus(string $transactionId): array
    {
        return $this->verifyPayment(['payment_id' => $transactionId]);
    }

    public function checkPaymentStatus(string $transactionId): array
    {
        return $this->getTransactionStatus($transactionId);
    }

    public function isWebhookValid(array $data): bool
    {
        // Tabby يرسل التوقيع في header: X-Tabby-Signature
        // هذا التحقق يجب أن يتم في الـ Controller
        
        $webhookSecret = $this->config['webhook_secret'] ?? config('services.tabby.webhook_secret', '');

        if (!$webhookSecret) {
            Log::channel('payment')->warning('Missing Tabby webhook secret');
            return false;
        }

        // في الـ Controller، ستحتاج إلى:
        // $signature = $request->header('X-Tabby-Signature');
        // $payload = $request->getContent();
        // $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        // return hash_equals($expectedSignature, $signature);

        return true; // مؤقتاً، سيتم التحقق في الـ Controller
    }

    public function handleWebhook(array $data): array
    {
        try {
            // Note: Tabby webhook validation is done via headers in controller
            
            $eventType = $data['event'] ?? '';
            $paymentId = $data['payment']['id'] ?? $data['id'] ?? null;
            $orderId = $data['payment']['order']['reference_id'] ?? $data['order']['reference_id'] ?? null;

            $result = [
                'success' => true,
                'gateway' => 'tabby',
                'event_type' => $eventType,
                'payment_id' => $paymentId,
                'order_id' => $orderId,
                'handled' => false,
                'status' => 'unknown',
            ];

            switch ($eventType) {
                case 'payment_approved':
                case 'payment_authorized':
                    $result['status'] = 'AUTHORIZED';
                    $result['handled'] = true;
                    break;

                case 'payment_captured':
                    $result['status'] = 'CLOSED';
                    $result['handled'] = true;
                    break;

                case 'payment_declined':
                case 'payment_rejected':
                    $result['status'] = 'REJECTED';
                    $result['handled'] = true;
                    break;

                case 'payment_expired':
                    $result['status'] = 'EXPIRED';
                    $result['handled'] = true;
                    break;

                case 'payment_refunded':
                    $result['status'] = 'REFUNDED';
                    $result['handled'] = true;
                    break;

                default:
                    $result['status'] = 'UNKNOWN';
                    Log::channel('payment')->warning('Unknown Tabby webhook event', [
                        'event_type' => $eventType,
                        'data' => $data,
                    ]);
            }

            Log::channel('payment')->info('Tabby webhook processed', $result);

            return $result;
        } catch (\Exception $e) {
            Log::channel('payment')->error('Tabby Webhook Processing Failed', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

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
            'CREATED' => 'created',
            'AUTHORIZED' => 'authorized',
            'CLOSED' => 'closed',
            'CAPTURED' => 'captured',
            'REJECTED' => 'rejected',
            'EXPIRED' => 'expired',
            'REFUNDED' => 'refunded',
            'CANCELLED' => 'cancelled',
            'PENDING' => 'pending',
        ];

        return $statusMap[strtoupper($status)] ?? 'unknown';
    }
}