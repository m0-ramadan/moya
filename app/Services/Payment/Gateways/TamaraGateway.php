<?php

namespace App\Services\Payment\Gateways;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TamaraGateway extends BaseGateway
{
    protected function initializeConfig(): void
    {
        $this->config = config('services.tamara', []);
        $this->currency = config('services.tamara.currency', 'SAR');
        $this->isSandbox = config('services.tamara.sandbox', true);
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
        $apiToken = $this->config['api_token'] ?? '';

        if (!$apiToken) {
            throw new \Exception('Tamara API token is not configured');
        }

        return $apiToken;
    }

    public function createPaymentOrder(array $data): array
    {
        return $this->initiatePayment($data);
    }

    public function initiatePayment(array $data): array
    {
        try {
            // التحقق من البيانات المطلوبة
            $required = ['order_id', 'amount', 'customer'];
            if (!$this->validateRequired($data, $required)) {
                throw new \Exception('Missing required payment data');
            }

            $authToken = $this->fetchAuthToken();

            // تحضير بيانات طلب الدفع
            $checkoutData = $this->prepareCheckoutData($data);

            Log::channel('payment')->debug('Tamara checkout payload', [
                'payload' => $checkoutData,
            ]);

            // استخدام makeRequest من BaseGateway
            $response = $this->makeRequest(
                'POST',
                '/checkout',
                $checkoutData,
                ['Authorization' => "Bearer {$authToken}"]
            );
            if (!$response['success']) {
                Log::channel('payment')->error('Tamara API Error Response', [
                    'error' => $response['error'] ?? 'Unknown error',
                    'status' => $response['status'] ?? null,
                    'raw_response' => $response['raw_response'] ?? null,
                ]);
                throw new \Exception($response['error'] ?? 'Failed to create Tamara checkout session');
            }

            $responseData = $response['data'];

            return [
                'success' => true,
                'gateway' => 'tamara',
                'order_id' => $responseData['order_id'] ?? null,
                'checkout_id' => $responseData['checkout_id'] ?? null,
                'checkout_url' => $responseData['checkout_url'] ?? null,
                'status' => $responseData['status'] ?? 'new',
                'expires_at' => now()->addMinutes(30)->toIso8601String(), // Tamara default expiry is 30 minutes
                'raw_response' => $responseData,
            ];
        } catch (\Exception $e) {
            Log::channel('payment')->error('Tamara Payment Initiation Failed', [
                'order_id' => $data['order_id'] ?? null,
                'amount' => $data['amount'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'gateway' => 'tamara',
                'error' => $e->getMessage(),
                'error_code' => 'TAMARA_INIT_FAILED',
            ];
        }
    }

    private function prepareCheckoutData(array $data): array
    {
        $customer = $data['customer'] ?? [];
        $items = $data['items'] ?? [];
        $shippingAddress = $data['shipping_address'] ?? [];
        $billingAddress = $data['billing_address'] ?? $shippingAddress;
        $metadata = $data['metadata'] ?? [];

        // تحضير عناصر الطلب
        $orderItems = [];
        foreach ($items as $item) {
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 1);
            $taxAmount = (float) ($item['tax_amount'] ?? 0);
            $discountAmount = (float) ($item['discount_amount'] ?? 0);
            $totalAmount = (float) ($item['total_price'] ?? ($unitPrice * $quantity));

            $orderItems[] = [
                'reference_id' => $item['sku'] ?? 'ITEM-' . uniqid(),
                'type' => $item['type'] ?? ($item['category'] ?? 'physical') === 'services' ? 'digital' : 'physical',
                'name' => $item['name'] ?? 'Item',
                'sku' => $item['sku'] ?? 'SKU-UNKNOWN',
                'quantity' => $quantity,
                'item_url' => $item['item_url'] ?? $item['product_url'] ?? null,
                'image_url' => $item['image_url'] ?? null,
                'unit_price' => [
                    'amount' => $unitPrice,
                    'currency' => $this->currency,
                ],
                'tax_amount' => [
                    'amount' => $taxAmount,
                    'currency' => $this->currency,
                ],
                'discount_amount' => [
                    'amount' => $discountAmount,
                    'currency' => $this->currency,
                ],
                'total_amount' => [
                    'amount' => $totalAmount,
                    'currency' => $this->currency,
                ],
            ];
        }

        // إذا لم تكن هناك عناصر، نضيف عنصر افتراضي
        if (empty($orderItems)) {
            $orderItems[] = [
                'reference_id' => 'SERVICE-' . ($data['order_id'] ?? uniqid()),
                'type' => 'digital',
                'name' => 'Water Delivery Service',
                'sku' => 'WATER-DELIVERY',
                'quantity' => 1,
                'item_url' => $metadata['order_url'] ?? null,
                'image_url' => $metadata['service_image'] ?? null,
                'unit_price' => [
                    'amount' => (float) ($data['amount'] ?? 0),
                    'currency' => $this->currency,
                ],
                'tax_amount' => [
                    'amount' => 0.00,
                    'currency' => $this->currency,
                ],
                'discount_amount' => [
                    'amount' => 0.00,
                    'currency' => $this->currency,
                ],
                'total_amount' => [
                    'amount' => (float) ($data['amount'] ?? 0),
                    'currency' => $this->currency,
                ],
            ];
        }

        // حساب المبالغ الإجمالية
        $totalAmount = (float) ($data['amount'] ?? 0);
        $shippingAmount = (float) ($data['shipping_amount'] ?? 0);
        $taxAmount = (float) ($data['tax_amount'] ?? 0);
        $discountAmount = (float) ($data['discount_amount'] ?? 0);

        // بيانات المخاطرة
        $riskAssessment = $this->prepareRiskAssessment($customer);

        return [
            'total_amount' => [
                'amount' => $totalAmount,
                'currency' => $this->currency,
            ],
            'shipping_amount' => [
                'amount' => $shippingAmount,
                'currency' => $this->currency,
            ],
            'tax_amount' => [
                'amount' => $taxAmount,
                'currency' => $this->currency,
            ],
            'order_reference_id' => (string) ($data['order_id'] ?? 'ORD-' . time()),
            'order_number' => $data['order_number'] ?? $data['order_id'] ?? 'ORD-' . time(),
            'items' => $orderItems,
            'consumer' => [
                'first_name' => $customer['first_name'] ?? 'Customer',
                'last_name' => $customer['last_name'] ?? '',
                'phone_number' => $this->formatPhoneNumber($customer['phone'] ?? '+966500000000'),
                'email' => $customer['email'] ?? 'customer@example.com',
                'national_id' => $customer['national_id'] ?? '',
                'date_of_birth' => $customer['date_of_birth'] ?? null,
                'is_first_order' => $customer['is_first_order'] ?? true,
            ],
            'country_code' => 'SA',
            'description' => $data['description'] ?? 'Water Delivery Order',
            'merchant_url' => [
                'success' => $data['callback_urls']['success'] ?? route('payment.success.tamara'),
                'failure' => $data['callback_urls']['failure'] ?? route('payment.failure.tamara'),
                'cancel' => $data['callback_urls']['cancel'] ?? route('payment.cancel.tamara'),
                'notification' => route('payment.webhook.tamara'),
            ],
            'shipping_address' => [
                'first_name' => $shippingAddress['first_name'] ?? 'Customer',
                'last_name' => $shippingAddress['last_name'] ?? '',
                'line1' => $shippingAddress['address_line1'] ?? 'Not specified',
                'line2' => $shippingAddress['address_line2'] ?? '',
                'city' => $shippingAddress['city'] ?? 'Riyadh',
                'country_code' => 'SA',
                'region' => $shippingAddress['region'] ?? 'Riyadh',
                'phone_number' => $this->formatPhoneNumber($shippingAddress['phone'] ?? '+966500000000'),
            ],
            'billing_address' => [
                'first_name' => $billingAddress['first_name'] ?? 'Customer',
                'last_name' => $billingAddress['last_name'] ?? '',
                'line1' => $billingAddress['address_line1'] ?? 'Not specified',
                'line2' => $billingAddress['address_line2'] ?? '',
                'city' => $billingAddress['city'] ?? 'Riyadh',
                'country_code' => 'SA',
                'region' => $billingAddress['region'] ?? 'Riyadh',
                'phone_number' => $this->formatPhoneNumber($billingAddress['phone'] ?? '+966500000000'),
            ],
            'platform' => 'API',
            'is_mobile' => false,
            'locale' => 'ar_SA',
            'risk_assessment' => $riskAssessment,
            'additional_data' => [
                'delivery_method' => 'Home Delivery',
                'vendor_amount' => 0,
                'merchant_settlement_amount' => $totalAmount,
                'vendor_reference_code' => 'VENDOR-' . ($data['order_id'] ?? uniqid()),
            ],
            // 'discount' => $discountAmount > 0 ? [
            //     'name' => 'Customer Discount',
            //     'amount' => [
            //         'amount' => $discountAmount,
            //         'currency' => $this->currency,
            //     ],
            // ] : null,
            // 'expires_in_minutes' => 30, // Contact support to enable
        ];
    }
/**
 * يحول أي قيمة تاريخية إلى Carbon بصيغة YYYY-MM-DD بشكل آمن
 */
private function safeParseDate(mixed $date): \Carbon\Carbon
{
    try {
        return \Carbon\Carbon::parse($date);
    } catch (\Exception $e) {
        return now();
    }
}
private function prepareRiskAssessment(array $customer): array
{
    // إذا كان created_at موجود، parse وإلا استخدم سنة مضت
  //  $createdAt = isset($customer['created_at']) ? $this->safeParseDate($customer['created_at']) : now()->subYear();

    // دالة مساعدة لتحويل أي تاريخ إلى string Y-m-d
   //$toDate = fn($date, $default = null) => $this->safeParseDateString($date ?? $default ?? $createdAt);

    return [
        'customer_age' => (int) ($customer['age'] ?? 25),
       // 'customer_dob' => $toDate($customer['date_of_birth'], '1990-01-01'),
        'customer_gender' => $customer['gender'] ?? 'Male',
        'customer_nationality' => 'SA',
        'is_premium_customer' => false,
        'is_existing_customer' => ($customer['order_count'] ?? 0) > 0,
        'is_guest_user' => false,
       // 'account_creation_date' => $toDate($customer['created_at']),
       // 'platform_account_creation_date' => $toDate($customer['created_at']),
       // 'date_of_first_transaction' => $toDate($customer['first_order_date']),
        'is_card_on_file' => false,
        'is_COD_customer' => false,
        'has_delivered_order' => ($customer['completed_order_count'] ?? 0) > 0,
        'is_phone_verified' => true,
        'is_fraudulent_customer' => false,
        'total_ltv' => (float) ($customer['total_spent'] ?? 0),
        'total_order_count' => (int) ($customer['order_count'] ?? 0),
        'order_amount_last3months' => (float) ($customer['last_3months_spent'] ?? 0),
        'order_count_last3months' => (int) ($customer['last_3months_orders'] ?? 0),
      //  'last_order_date' => $toDate($customer['last_order_date']),
        'last_order_amount' => (float) ($customer['last_order_amount'] ?? 0),
        'reward_program_enrolled' => false,
        'reward_program_points' => 0,
    ];
}

/**
 * يحول أي قيمة تاريخية إلى نص YYYY-MM-DD
 */
private function safeParseDateString(mixed $date): string
{
    try {
        return \Carbon\Carbon::parse($date)->format('Y-m-d');
    } catch (\Exception $e) {
        return now()->format('Y-m-d');
    }
}



    private function formatPhoneNumber(string $phone): string
    {
        // إزالة أي أحرف غير رقمية
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // إذا كان الرقم يحتوي على 966 في البداية
        if (strlen($phone) === 12 && str_starts_with($phone, '966')) {
            return $phone;
        }
        
        // إذا كان الرقم 9 أرقام (بدون 966)
        if (strlen($phone) === 9) {
            return '966' . $phone;
        }
        
        // إذا كان الرقم 10 أرقام ويبدأ بـ 0
        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            return '966' . substr($phone, 1);
        }
        
        // الافتراضي
        return '966500000000';
    }

    public function verifyTransaction(array $data): array
    {
        return $this->verifyPayment($data);
    }

    public function verifyPayment(array $data): array
    {
        try {
            $orderId = $data['order_id'] ?? null;
            $paymentId = $data['payment_id'] ?? null;

            if (!$orderId && !$paymentId) {
                throw new \Exception('Order ID or Payment ID is required');
            }

            $authToken = $this->fetchAuthToken();

            // استخدام order_reference_id للبحث
            if ($orderId) {
                $response = $this->makeRequest(
                    'GET',
                    "/orders?order_reference_id={$orderId}",
                    [],
                    ['Authorization' => "Bearer {$authToken}"]
                );
            } else {
                $response = $this->makeRequest(
                    'GET',
                    "/orders/{$paymentId}",
                    [],
                    ['Authorization' => "Bearer {$authToken}"]
                );
            }

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to verify Tamara payment');
            }

            $orderData = $response['data'];
            
            // إذا كان الرد قائمة، خذ أول عنصر
            if (isset($orderData[0])) {
                $orderData = $orderData[0];
            }

            $status = $this->mapPaymentStatus($orderData['status'] ?? '');

            return [
                'success' => true,
                'gateway' => 'tamara',
                'order_id' => $orderData['order_reference_id'] ?? $orderId,
                'payment_id' => $orderData['order_id'] ?? $paymentId,
                'status' => $status,
                'amount' => $orderData['total_amount']['amount'] ?? 0,
                'currency' => $orderData['total_amount']['currency'] ?? $this->currency,
                'is_paid' => in_array($status, ['authorised', 'captured', 'approved', 'new']),
                'order_details' => $orderData,
            ];
        } catch (\Exception $e) {
            Log::channel('payment')->error('Tamara Payment Verification Failed', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'gateway' => 'tamara',
                'error' => $e->getMessage(),
                'error_code' => 'TAMARA_VERIFY_FAILED',
            ];
        }
    }

    public function refund(string $transactionId, float $amount, string $reason = ''): array
    {
        try {
            $authToken = $this->fetchAuthToken();

            $payload = [
                'order_id' => $transactionId,
                'refund_amount' => [
                    'amount' => (float) $amount,
                    'currency' => $this->currency,
                ],
                'comment' => $reason ?: 'Customer request',
                'refund_reason' => 'customer_request',
            ];

            Log::channel('payment')->debug('Tamara refund payload', [
                'transaction_id' => $transactionId,
                'payload' => $payload,
            ]);

            $response = $this->makeRequest(
                'POST',
                '/refunds',
                $payload,
                ['Authorization' => "Bearer {$authToken}"]
            );

            if (!$response['success']) {
                throw new \Exception($response['error'] ?? 'Failed to process Tamara refund');
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
            Log::channel('payment')->error('Tamara Refund Failed', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'gateway' => 'tamara',
                'error' => $e->getMessage(),
                'error_code' => 'TAMARA_REFUND_FAILED',
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
        // Tamara عادةً ترسل توقيع في header
        // يمكنك التحقق من وجود token في البيانات أو header
        $webhookToken = $this->config['webhook_token'] ?? config('services.tamara.webhook_token', '');
        $token = $data['token'] ?? $data['webhook_token'] ?? '';

        if (!$webhookToken || !$token) {
            Log::channel('payment')->warning('Missing Tamara webhook token', [
                'has_webhook_token' => !empty($webhookToken),
                'has_token' => !empty($token),
            ]);
            return false;
        }

        return hash_equals($webhookToken, $token);
    }

    public function handleWebhook(array $data): array
    {
        try {
            $eventType = $data['event_type'] ?? $data['event'] ?? '';
            $orderId = $data['order_reference_id'] ?? $data['order_id'] ?? null;
            $paymentId = $data['order_id'] ?? $data['payment_id'] ?? null;

            $result = [
                'success' => true,
                'gateway' => 'tamara',
                'event_type' => $eventType,
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'handled' => false,
                'status' => 'unknown',
            ];

            switch ($eventType) {
                case 'order_approved':
                case 'payment_authorised':
                    $result['status'] = 'authorised';
                    $result['handled'] = true;
                    break;

                case 'order_captured':
                case 'payment_captured':
                    $result['status'] = 'captured';
                    $result['handled'] = true;
                    break;

                case 'order_declined':
                case 'payment_declined':
                    $result['status'] = 'declined';
                    $result['handled'] = true;
                    break;

                case 'order_cancelled':
                case 'payment_cancelled':
                    $result['status'] = 'cancelled';
                    $result['handled'] = true;
                    break;

                case 'order_refunded':
                case 'payment_refunded':
                    $result['status'] = 'refunded';
                    $result['handled'] = true;
                    break;

                case 'order_expired':
                    $result['status'] = 'expired';
                    $result['handled'] = true;
                    break;

                case 'order_new':
                    $result['status'] = 'new';
                    $result['handled'] = true;
                    break;

                default:
                    $result['status'] = 'unknown';
                    Log::channel('payment')->warning('Unknown Tamara webhook event', [
                        'event_type' => $eventType,
                        'data' => $data,
                    ]);
            }

            Log::channel('payment')->info('Tamara webhook processed', $result);

            return $result;
        } catch (\Exception $e) {
            Log::channel('payment')->error('Tamara Webhook Processing Failed', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'gateway' => 'tamara',
                'error' => $e->getMessage(),
                'error_code' => 'TAMARA_WEBHOOK_FAILED',
            ];
        }
    }

    private function mapPaymentStatus(string $status): string
    {
        $statusMap = [
            'approved' => 'approved',
            'authorised' => 'authorised',
            'captured' => 'captured',
            'declined' => 'declined',
            'cancelled' => 'cancelled',
            'expired' => 'expired',
            'refunded' => 'refunded',
            'partially_refunded' => 'partially_refunded',
            'new' => 'new',
            'pending' => 'pending',
        ];

        return $statusMap[strtolower($status)] ?? 'unknown';
    }
}