<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

abstract class BaseGateway implements PaymentGatewayInterface
{
    protected array $config;
    protected string $currency;
    protected bool $isSandbox;

    public function __construct()
    {
        $this->initializeConfig();
        $this->currency = config('app.currency', 'SAR');
        $this->isSandbox = config("services.{$this->getGatewayName()}.sandbox", true);
    }

    abstract protected function initializeConfig(): void;
    abstract protected function getGatewayName(): string;
    abstract protected function getBaseUrl(): string;

    protected function getAuthToken(): ?string
    {
        $cacheKey = "payment_gateway_auth_{$this->getGatewayName()}";

        return Cache::remember($cacheKey, 3500, function () {
            return $this->fetchAuthToken();
        });
    }

    abstract protected function fetchAuthToken(): string;

    protected function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = $this->getBaseUrl() . $endpoint;
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
                'error' => 'Gateway connection failed: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    protected function validateRequired(array $data, array $required): bool
    {
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

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
}
