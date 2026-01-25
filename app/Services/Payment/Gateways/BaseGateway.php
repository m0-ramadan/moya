<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\Payment\PaymentGatewayInterface ;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseGateway implements PaymentGatewayInterface
{
    protected array $config;
    protected string $currency;
    protected bool $isSandbox;
    protected ?string $authToken = null;
    protected string $baseUrl;

    public function __construct()
    {
        $this->initializeConfig();
        $this->currency = config('app.currency', 'SAR');
        $this->isSandbox = config("services.{$this->getGatewayName()}.sandbox", true);
        $this->initializeAuthToken();
    }

    abstract protected function initializeConfig(): void;
    abstract protected function getGatewayName(): string;
    abstract protected function getBaseUrl(): string;

    /**
     * Initialize auth token - يمكن تخطيها في Gateways التي لا تحتاج token
     */
    protected function initializeAuthToken(): void
    {
        // Default implementation does nothing
        // يمكن للـ Child Classes أن تoverride هذه الدالة إذا احتاجت
    }

    /**
     * Get auth token - دالة افتراضية يمكن تخطيها
     */
    protected function getAuthToken(string $gateway = null): ?string
    {
        $gatewayName = $gateway ?? $this->getGatewayName();

        return match ($gatewayName) {
            'paymob' => $this->getPaymobAuthToken(),
            'tamara' => $this->getTamaraAuthToken(),
            'tabby' => $this->getTabbyAuthToken(),
            default => $this->fetchAuthToken(),
        };
    }

protected function getPaymobAuthToken(): ?string
{
    $apiKey = config('services.paymob.api_key');

    // تحقق من وجود API Key
    if (empty($apiKey)) {
        Log::channel('payment')->error('Paymob API Key not found');
        return null;
    }

    try {
        $response = Http::post('https://ksa.paymob.com/api/auth/tokens', [
            'api_key' => $apiKey,
        ]);

        if (! $response->successful()) {
            Log::channel('payment')->error('Paymob Auth Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $token = $response->json('token');

        if (empty($token)) {
            Log::channel('payment')->error('Paymob Auth Token not found in response', [
                'response' => $response->body(),
            ]);
            return null;
        }

        // يمكنك تخزين التوكن في الكاش إذا أردت
        // $this->cacheAuthToken($token);

        return $token;
    } catch (\Throwable $e) {
        Log::channel('payment')->error('Paymob Auth Exception', [
            'message' => $e->getMessage(),
        ]);
        return null;
    }
}


    protected function getTamaraAuthToken(): ?string
    {
        return config('services.tamara.token');
    }

    protected function getTabbyAuthToken(): ?string
    {
        return config('services.tabby.secret_key');
    }

    /**
     * Fetch auth token - أصبحت optional وليست abstract
     */
    protected function fetchAuthToken(): ?string
    {
        // Default implementation returns null
        return null;
    }

    /**
     * Cache auth token if needed
     */
    protected function cacheAuthToken(string $token, int $ttl = 3500): void
    {
        $cacheKey = "payment_gateway_auth_{$this->getGatewayName()}";
        Cache::put($cacheKey, $token, $ttl);
        $this->authToken = $token;
    }

    /**
     * Get cached auth token
     */
    protected function getCachedAuthToken(): ?string
    {
        $cacheKey = "payment_gateway_auth_{$this->getGatewayName()}";
        return Cache::get($cacheKey);
    }

    /**
     * Make HTTP request with proper error handling
     */
    protected function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = $this->getBaseUrl().$endpoint;
   
        // Add auth token if available
        $authToken = $this->getAuthToken();
        if ($authToken) {
            $headers['Authorization'] = "Bearer {$authToken}";
        }

        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $headers = array_merge($defaultHeaders, $headers);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->retry(3, 100)
                ->{$method}($url, $data);

            Log::channel('payment')->debug("{$this->getGatewayName()} API Request", [
                'url' => $url,
                'method' => $method,
                'data' => $data,
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Payment gateway error',
                'status' => $response->status(),
                'raw_response' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::channel('payment')->error("{$this->getGatewayName()} API Error", [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Gateway connection failed: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    /**
     * Validate required fields
     */
    protected function validateRequired(array $data, array $required): bool
    {
        foreach ($required as $field) {
            if (! isset($data[$field]) || empty($data[$field])) {
                Log::channel('payment')->warning("Missing required field: {$field}", $data);
                return false;
            }
        }

        return true;
    }

    /**
     * Create standardized payment data
     */
    protected function createPaymentData(array $orderData): array
    {
        return [
            'order_id' => $orderData['order_id'],
            'amount' => $orderData['amount'],
            'currency' => $this->currency,
            'customer' => $orderData['customer'] ?? [],
            'items' => $orderData['items'] ?? [],
            'billing_address' => $orderData['billing_address'] ?? [],
            'shipping_address' => $orderData['shipping_address'] ?? [],
            'callback_urls' => $orderData['callback_urls'] ?? [],
            'metadata' => $orderData['metadata'] ?? [],
        ];
    }

    /**
     * Generate unique reference ID
     */
    protected function generateReferenceId(string $prefix = 'ORD'): string
    {
        return $prefix.'-'.uniqid().'-'.time();
    }

    /**
     * Prepare customer data
     */
    protected function prepareCustomerData(array $customer): array
    {
        return [
            'first_name' => $customer['first_name'] ?? 'Customer',
            'last_name' => $customer['last_name'] ?? '',
            'email' => $customer['email'] ?? 'customer@example.com',
            'phone' => $customer['phone'] ?? '+966500000000',
        ];
    }

    /**
     * Prepare items data
     */
    protected function prepareItemsData(array $items): array
    {
        $preparedItems = [];
        foreach ($items as $item) {
            $preparedItems[] = [
                'name' => $item['name'] ?? 'Item',
                'description' => $item['description'] ?? '',
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'total_price' => $item['total_price'] ?? 0,
                'sku' => $item['sku'] ?? '',
            ];
        }

        return $preparedItems;
    }

    // Interface methods that need to be implemented by child classes
    abstract public function createPaymentOrder(array $data): array;
    abstract public function verifyTransaction(array $data): array;
    abstract public function refund(string $transactionId, float $amount, string $reason = ''): array;
    abstract public function getTransactionStatus(string $transactionId): array;
}