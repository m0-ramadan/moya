<?php

namespace App\Services\Wallet;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ExchangeRateService
{
    private string $provider;
    private string $apiKey;
    private int $cacheTtl;
    private array $fallbackRates;
    private bool $isLive;

    const PROVIDER_OPENEXCHANGERATES = 'openexchangerates';
    const PROVIDER_FIXER = 'fixer';
    const PROVIDER_EXCHANGERATE_API = 'exchangerate_api';
    const PROVIDER_CURRENCY_LAYER = 'currency_layer';

    const CURRENCY_SAR = 'SAR';  // الريال السعودي
    const CURRENCY_EGP = 'EGP';  // الجنيه المصري
    const CURRENCY_USD = 'USD';  // الدولار الأمريكي
    const CURRENCY_EUR = 'EUR';  // اليورو
    const CURRENCY_AED = 'AED';  // الدرهم الإماراتي
    const CURRENCY_QAR = 'QAR';  // الريال القطري
    const CURRENCY_OMR = 'OMR';  // الريال العماني
    const CURRENCY_KWD = 'KWD';  // الدينار الكويتي
    const CURRENCY_BHD = 'BHD';  // الدينار البحريني
    const CURRENCY_JOD = 'JOD';  // الدينار الأردني

    public function __construct()
    {
        $this->provider = config('wallet.exchange_rate.provider', self::PROVIDER_OPENEXCHANGERATES);
        $this->apiKey = config('wallet.exchange_rate.api_key', 'd91b3f8e6f1b4e7597edd192e54d3cfe');
        $this->cacheTtl = config('wallet.exchange_rate.cache_ttl', 3600);
        $this->isLive = config('wallet.exchange_rate.live_mode', false);
        $this->fallbackRates = $this->loadFallbackRates();
    }

    /**
     * تحميل أسعار الصرف الافتراضية
     */
    private function loadFallbackRates(): array
    {
        return [
            'SAR' => [
                'USD' => 0.266667,
                'EUR' => 0.246154,
                'EGP' => 8.500000,
                'AED' => 0.980392,
                'QAR' => 0.970874,
                'OMR' => 0.102564,
                'KWD' => 0.081081,
                'BHD' => 0.100000,
                'JOD' => 0.188679,
            ],
            'USD' => [
                'SAR' => 3.750000,
                'EUR' => 0.923077,
                'EGP' => 31.500000,
                'AED' => 3.672500,
                'QAR' => 3.640000,
                'OMR' => 0.384615,
                'KWD' => 0.303030,
                'BHD' => 0.375000,
                'JOD' => 0.709677,
            ],
            'EGP' => [
                'SAR' => 0.117647,
                'USD' => 0.031746,
                'EUR' => 0.029412,
                'AED' => 0.117647,
                'QAR' => 0.116279,
                'OMR' => 0.012195,
                'KWD' => 0.009709,
                'BHD' => 0.011765,
                'JOD' => 0.022222,
            ],
            'EUR' => [
                'SAR' => 4.062500,
                'USD' => 1.083333,
                'EGP' => 34.000000,
                'AED' => 3.980392,
                'QAR' => 3.941176,
                'OMR' => 0.416667,
                'KWD' => 0.328125,
                'BHD' => 0.406250,
                'JOD' => 0.767442,
            ],
        ];
    }

    /**
     * تحويل مبلغ بين عملتين
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): array
    {
        try {
            // إذا كانت العملتان متشابهتان
            if ($fromCurrency === $toCurrency) {
                return $this->createConversionResult($amount, $amount, 1.0, $fromCurrency, $toCurrency);
            }

            // الحصول على سعر الصرف
            $rate = $this->getExchangeRate($fromCurrency, $toCurrency);

            // التحقق من صحة سعر الصرف
            if ($rate <= 0) {
                throw new \Exception("Invalid exchange rate: {$rate}");
            }

            // حساب المبلغ المحول
            $convertedAmount = $amount * $rate;

            // تسجيل عملية التحويل
            $this->logConversion($amount, $convertedAmount, $fromCurrency, $toCurrency, $rate);

            return $this->createConversionResult($amount, $convertedAmount, $rate, $fromCurrency, $toCurrency);
        } catch (\Exception $e) {
            Log::error('Currency conversion failed', [
                'amount' => $amount,
                'from' => $fromCurrency,
                'to' => $toCurrency,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // استخدام السعر الافتراضي في حالة الفشل
            return $this->convertWithFallback($amount, $fromCurrency, $toCurrency);
        }
    }

    /**
     * الحصول على سعر الصرف
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        // التحقق من الصحة
        $this->validateCurrency($fromCurrency);
        $this->validateCurrency($toCurrency);

        // إذا كانت العملتان متشابهتان
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        // مفتاح التخزين المؤقت
        $cacheKey = "exchange_rate_{$fromCurrency}_{$toCurrency}_" . date('YmdH');

        // محاولة الحصول من التخزين المؤقت
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($fromCurrency, $toCurrency) {
            return $this->fetchExchangeRate($fromCurrency, $toCurrency);
        });
    }

    /**
     * جلب سعر الصرف من مزود الخدمة
     */
    private function fetchExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        try {
            // إذا كان النظام غير متصل بالإنترنت
            if (!$this->isLive) {
                return $this->getFallbackRate($fromCurrency, $toCurrency);
            }

            switch ($this->provider) {
                case self::PROVIDER_OPENEXCHANGERATES:
                    return $this->fetchFromOpenExchangeRates($fromCurrency, $toCurrency);

                case self::PROVIDER_FIXER:
                    return $this->fetchFromFixer($fromCurrency, $toCurrency);

                case self::PROVIDER_EXCHANGERATE_API:
                    return $this->fetchFromExchangeRateApi($fromCurrency, $toCurrency);

                case self::PROVIDER_CURRENCY_LAYER:
                    return $this->fetchFromCurrencyLayer($fromCurrency, $toCurrency);

                default:
                    return $this->getFallbackRate($fromCurrency, $toCurrency);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch exchange rate from provider', [
                'provider' => $this->provider,
                'from' => $fromCurrency,
                'to' => $toCurrency,
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackRate($fromCurrency, $toCurrency);
        }
    }

    /**
     * جلب سعر الصرف من OpenExchangeRates
     */
    private function fetchFromOpenExchangeRates(string $fromCurrency, string $toCurrency): float
    {
        $response = Http::retry(3, 100)->get('https://openexchangerates.org/api/latest.json', [
            'app_id' => $this->apiKey,
            'base' => $fromCurrency,
            'symbols' => $toCurrency,
            'show_alternative' => false
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenExchangeRates API failed: ' . $response->status());
        }

        $data = $response->json();

        if (!isset($data['rates'][$toCurrency])) {
            throw new \Exception("Rate for {$toCurrency} not found");
        }

        $rate = $data['rates'][$toCurrency];

        // تخزين السعر في قاعدة البيانات للمراقبة
        $this->storeRateInDatabase($fromCurrency, $toCurrency, $rate, 'openexchangerates');

        return (float) $rate;
    }

    /**
     * جلب سعر الصرف من Fixer
     */
    private function fetchFromFixer(string $fromCurrency, string $toCurrency): float
    {
        $response = Http::retry(3, 100)->get('http://data.fixer.io/api/latest', [
            'access_key' => $this->apiKey,
            'base' => $fromCurrency,
            'symbols' => $toCurrency
        ]);

        if (!$response->successful()) {
            throw new \Exception('Fixer API failed: ' . $response->status());
        }

        $data = $response->json();

        if (!$data['success'] || !isset($data['rates'][$toCurrency])) {
            throw new \Exception("Fixer API error: " . ($data['error']['info'] ?? 'Unknown error'));
        }

        $rate = $data['rates'][$toCurrency];
        $this->storeRateInDatabase($fromCurrency, $toCurrency, $rate, 'fixer');

        return (float) $rate;
    }

    /**
     * جلب سعر الصرف من ExchangeRate-API
     */
    private function fetchFromExchangeRateApi(string $fromCurrency, string $toCurrency): float
    {
        $response = Http::retry(3, 100)->get("https://api.exchangerate-api.com/v4/latest/{$fromCurrency}");

        if (!$response->successful()) {
            throw new \Exception('ExchangeRate-API failed: ' . $response->status());
        }

        $data = $response->json();

        if (!isset($data['rates'][$toCurrency])) {
            throw new \Exception("Rate for {$toCurrency} not found");
        }

        $rate = $data['rates'][$toCurrency];
        $this->storeRateInDatabase($fromCurrency, $toCurrency, $rate, 'exchangerate_api');

        return (float) $rate;
    }

    /**
     * جلب سعر الصرف من Currency Layer
     */
    private function fetchFromCurrencyLayer(string $fromCurrency, string $toCurrency): float
    {
        $response = Http::retry(3, 100)->get('http://apilayer.net/api/live', [
            'access_key' => $this->apiKey,
            'currencies' => "{$fromCurrency},{$toCurrency}",
            'source' => $fromCurrency,
            'format' => 1
        ]);

        if (!$response->successful()) {
            throw new \Exception('Currency Layer API failed: ' . $response->status());
        }

        $data = $response->json();

        if (!$data['success'] || !isset($data['quotes']["{$fromCurrency}{$toCurrency}"])) {
            throw new \Exception("Currency Layer API error: " . ($data['error']['info'] ?? 'Unknown error'));
        }

        $rate = $data['quotes']["{$fromCurrency}{$toCurrency}"];
        $this->storeRateInDatabase($fromCurrency, $toCurrency, $rate, 'currency_layer');

        return (float) $rate;
    }

    /**
     * الحصول على السعر الافتراضي
     */
    private function getFallbackRate(string $fromCurrency, string $toCurrency): float
    {
        if (isset($this->fallbackRates[$fromCurrency][$toCurrency])) {
            return $this->fallbackRates[$fromCurrency][$toCurrency];
        }

        // حساب عكسي إذا كان متوفراً
        if (isset($this->fallbackRates[$toCurrency][$fromCurrency])) {
            return 1 / $this->fallbackRates[$toCurrency][$fromCurrency];
        }

        // إذا لم يكن هناك سعر افتراضي
        Log::critical('No fallback rate available', [
            'from' => $fromCurrency,
            'to' => $toCurrency
        ]);

        throw new \Exception("No exchange rate available for {$fromCurrency} to {$toCurrency}");
    }

    /**
     * التحويل باستخدام الأسعار الافتراضية
     */
    private function convertWithFallback(float $amount, string $fromCurrency, string $toCurrency): array
    {
        $rate = $this->getFallbackRate($fromCurrency, $toCurrency);
        $convertedAmount = $amount * $rate;

        Log::warning('Used fallback exchange rate', [
            'amount' => $amount,
            'from' => $fromCurrency,
            'to' => $toCurrency,
            'rate' => $rate,
            'converted' => $convertedAmount
        ]);

        return $this->createConversionResult(
            $amount,
            $convertedAmount,
            $rate,
            $fromCurrency,
            $toCurrency,
            true // علامة استخدام السعر الافتراضي
        );
    }

    /**
     * إنشاء نتيجة التحويل
     */
    private function createConversionResult(
        float $originalAmount,
        float $convertedAmount,
        float $exchangeRate,
        string $fromCurrency,
        string $toCurrency,
        bool $isFallback = false
    ): array {
        return [
            'success' => true,
            'original_amount' => $originalAmount,
            'converted_amount' => round($convertedAmount, 2),
            'exchange_rate' => round($exchangeRate, 6),
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'is_fallback_rate' => $isFallback,
            'converted_at' => now()->toIso8601String(),
            'rate_source' => $isFallback ? 'fallback' : $this->provider,
            'inverse_rate' => round(1 / $exchangeRate, 6),
            'fee_percentage' => $this->calculateConversionFee($originalAmount, $fromCurrency, $toCurrency),
            'net_amount' => $this->calculateNetAmount($originalAmount, $convertedAmount, $fromCurrency, $toCurrency)
        ];
    }

    /**
     * حساب رسوم التحويل
     */
    private function calculateConversionFee(float $amount, string $fromCurrency, string $toCurrency): float
    {
        // رسوم ثابتة أو نسبية بناءً على العملات
        $feeConfig = config('wallet.exchange_rate.fees', []);

        $feeKey = "{$fromCurrency}_{$toCurrency}";
        $feePercentage = $feeConfig[$feeKey] ?? $feeConfig['default'] ?? 0.02; // 2% افتراضي

        return round($feePercentage * 100, 2); // النسبة المئوية
    }

    /**
     * حساب المبلغ الصافي بعد الرسوم
     */
    private function calculateNetAmount(float $originalAmount, float $convertedAmount, string $fromCurrency, string $toCurrency): float
    {
        $feePercentage = $this->calculateConversionFee($originalAmount, $fromCurrency, $toCurrency) / 100;
        $feeAmount = $convertedAmount * $feePercentage;

        return round($convertedAmount - $feeAmount, 2);
    }

    /**
     * التحقق من صحة العملة
     */
    private function validateCurrency(string $currency): void
    {
        $supportedCurrencies = $this->getSupportedCurrencies();

        if (!isset($supportedCurrencies[$currency])) {
            throw new \Exception("Unsupported currency: {$currency}");
        }
    }

    /**
     * الحصول على العملات المدعومة
     */
    public function getSupportedCurrencies(): array
    {
        return [
            self::CURRENCY_SAR => [
                'name' => 'الريال السعودي',
                'symbol' => 'ر.س',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => true
            ],
            self::CURRENCY_EGP => [
                'name' => 'الجنيه المصري',
                'symbol' => 'ج.م',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false
            ],
            self::CURRENCY_USD => [
                'name' => 'الدولار الأمريكي',
                'symbol' => '$',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false
            ],
            self::CURRENCY_EUR => [
                'name' => 'اليورو',
                'symbol' => '€',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false
            ],
            self::CURRENCY_AED => [
                'name' => 'الدرهم الإماراتي',
                'symbol' => 'د.إ',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => false
            ],
            self::CURRENCY_QAR => [
                'name' => 'الريال القطري',
                'symbol' => 'ر.ق',
                'decimal_places' => 2,
                'is_active' => false,
                'is_default' => false
            ],
            self::CURRENCY_OMR => [
                'name' => 'الريال العماني',
                'symbol' => 'ر.ع.',
                'decimal_places' => 3,
                'is_active' => false,
                'is_default' => false
            ],
            self::CURRENCY_KWD => [
                'name' => 'الدينار الكويتي',
                'symbol' => 'د.ك',
                'decimal_places' => 3,
                'is_active' => false,
                'is_default' => false
            ],
            self::CURRENCY_BHD => [
                'name' => 'الدينار البحريني',
                'symbol' => 'د.ب',
                'decimal_places' => 3,
                'is_active' => false,
                'is_default' => false
            ],
            self::CURRENCY_JOD => [
                'name' => 'الدينار الأردني',
                'symbol' => 'د.أ',
                'decimal_places' => 3,
                'is_active' => false,
                'is_default' => false
            ],
        ];
    }

    /**
     * الحصول على سعر الصرف بين عدة عملات دفعة واحدة
     */
    public function getBulkExchangeRates(string $baseCurrency, array $targetCurrencies): array
    {
        $rates = [];

        foreach ($targetCurrencies as $targetCurrency) {
            if ($baseCurrency === $targetCurrency) {
                $rates[$targetCurrency] = 1.0;
                continue;
            }

            try {
                $rate = $this->getExchangeRate($baseCurrency, $targetCurrency);
                $rates[$targetCurrency] = round($rate, 6);
            } catch (\Exception $e) {
                Log::warning('Failed to get bulk exchange rate', [
                    'base' => $baseCurrency,
                    'target' => $targetCurrency,
                    'error' => $e->getMessage()
                ]);

                $rates[$targetCurrency] = null;
            }
        }

        return [
            'base_currency' => $baseCurrency,
            'rates' => $rates,
            'fetched_at' => now()->toIso8601String(),
            'provider' => $this->provider
        ];
    }

    /**
     * التحويل الجماعي
     */
    public function convertBulk(array $conversions): array
    {
        $results = [];

        foreach ($conversions as $index => $conversion) {
            try {
                $result = $this->convert(
                    $conversion['amount'],
                    $conversion['from_currency'],
                    $conversion['to_currency']
                );

                $results[] = [
                    'success' => true,
                    'conversion_id' => $conversion['id'] ?? $index,
                    'data' => $result
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'conversion_id' => $conversion['id'] ?? $index,
                    'error' => $e->getMessage(),
                    'amount' => $conversion['amount'],
                    'from_currency' => $conversion['from_currency'],
                    'to_currency' => $conversion['to_currency']
                ];
            }
        }

        return [
            'total_conversions' => count($conversions),
            'successful' => count(array_filter($results, fn($r) => $r['success'])),
            'failed' => count(array_filter($results, fn($r) => !$r['success'])),
            'results' => $results,
            'processed_at' => now()->toIso8601String()
        ];
    }

    /**
     * تسجيل عملية التحويل
     */
    private function logConversion(
        float $originalAmount,
        float $convertedAmount,
        string $fromCurrency,
        string $toCurrency,
        float $exchangeRate
    ): void {
        DB::table('exchange_rate_logs')->insert([
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'original_amount' => $originalAmount,
            'converted_amount' => $convertedAmount,
            'exchange_rate' => $exchangeRate,
            'provider' => $this->provider,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * تخزين السعر في قاعدة البيانات
     */
    private function storeRateInDatabase(string $fromCurrency, string $toCurrency, float $rate, string $provider): void
    {
        DB::table('exchange_rates')->insert([
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'rate' => $rate,
            'provider' => $provider,
            'source' => 'api',
            'is_live' => true,
            'valid_from' => now(),
            'valid_to' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * تحديث الأسعار الافتراضية
     */
    public function updateFallbackRates(array $newRates): bool
    {
        try {
            foreach ($newRates as $fromCurrency => $targetRates) {
                foreach ($targetRates as $toCurrency => $rate) {
                    $this->fallbackRates[$fromCurrency][$toCurrency] = (float) $rate;

                    // تخزين في قاعدة البيانات
                    DB::table('exchange_rates')->updateOrInsert(
                        [
                            'from_currency' => $fromCurrency,
                            'to_currency' => $toCurrency,
                            'provider' => 'fallback'
                        ],
                        [
                            'rate' => $rate,
                            'source' => 'manual',
                            'is_live' => false,
                            'valid_from' => now(),
                            'valid_to' => now()->addDays(30),
                            'updated_at' => now()
                        ]
                    );
                }
            }

            // مسح التخزين المؤقت
            Cache::tags(['exchange_rates'])->flush();

            Log::info('Fallback rates updated successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update fallback rates', [
                'error' => $e->getMessage(),
                'rates' => $newRates
            ]);

            return false;
        }
    }

    /**
     * الحصول على تاريخ أسعار الصرف
     */
    public function getHistoricalRate(string $fromCurrency, string $toCurrency, string $date): ?float
    {
        $cacheKey = "historical_rate_{$fromCurrency}_{$toCurrency}_{$date}";

        return Cache::remember($cacheKey, 86400, function () use ($fromCurrency, $toCurrency, $date) {
            // البحث في قاعدة البيانات أولاً
            $storedRate = DB::table('exchange_rates')
                ->where('from_currency', $fromCurrency)
                ->where('to_currency', $toCurrency)
                ->whereDate('valid_from', '<=', $date)
                ->whereDate('valid_to', '>=', $date)
                ->orderBy('created_at', 'desc')
                ->value('rate');

            if ($storedRate) {
                return (float) $storedRate;
            }

            // جلب من API إذا كان متاحاً
            if ($this->provider === self::PROVIDER_OPENEXCHANGERATES) {
                return $this->fetchHistoricalFromOpenExchangeRates($fromCurrency, $toCurrency, $date);
            }

            return null;
        });
    }

    /**
     * جلب سعر تاريخي من OpenExchangeRates
     */
    private function fetchHistoricalFromOpenExchangeRates(string $fromCurrency, string $toCurrency, string $date): ?float
    {
        try {
            $response = Http::get("https://openexchangerates.org/api/historical/{$date}.json", [
                'app_id' => $this->apiKey,
                'base' => $fromCurrency,
                'symbols' => $toCurrency
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['rates'][$toCurrency] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch historical rate', [
                'date' => $date,
                'from' => $fromCurrency,
                'to' => $toCurrency,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * مراقبة تقلبات أسعار الصرف
     */
    public function monitorRateVolatility(string $fromCurrency, string $toCurrency, int $days = 7): array
    {
        $rates = DB::table('exchange_rates')
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at')
            ->get(['rate', 'created_at']);

        if ($rates->isEmpty()) {
            return ['volatility' => 0, 'trend' => 'stable', 'data' => []];
        }

        $rateValues = $rates->pluck('rate')->toArray();
        $average = array_sum($rateValues) / count($rateValues);

        // حساب التقلب
        $variance = 0;
        foreach ($rateValues as $rate) {
            $variance += pow($rate - $average, 2);
        }
        $variance /= count($rateValues);
        $volatility = sqrt($variance) / $average * 100; // نسبة مئوية

        // تحديد الاتجاه
        $firstRate = $rateValues[0];
        $lastRate = $rateValues[count($rateValues) - 1];
        $trend = $lastRate > $firstRate ? 'up' : ($lastRate < $firstRate ? 'down' : 'stable');

        return [
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'period_days' => $days,
            'current_rate' => $lastRate,
            'average_rate' => round($average, 6),
            'volatility_percentage' => round($volatility, 2),
            'trend' => $trend,
            'min_rate' => min($rateValues),
            'max_rate' => max($rateValues),
            'rate_change' => round(($lastRate - $firstRate) / $firstRate * 100, 2),
            'data_points' => $rates->count(),
            'monitored_at' => now()->toIso8601String()
        ];
    }

    /**
     * التحقق من صحة مزود الخدمة
     */
    public function validateProvider(): array
    {
        $startTime = microtime(true);

        try {
            // محاولة الحصول على سعر معروف
            $rate = $this->fetchFromOpenExchangeRates('USD', 'EUR');

            $responseTime = round((microtime(true) - $startTime) * 1000, 2); // مللي ثانية

            return [
                'provider' => $this->provider,
                'status' => 'healthy',
                'response_time_ms' => $responseTime,
                'rate' => $rate,
                'checked_at' => now()->toIso8601String()
            ];
        } catch (\Exception $e) {
            return [
                'provider' => $this->provider,
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                'checked_at' => now()->toIso8601String()
            ];
        }
    }

    /**
     * تبديل مزود الخدمة
     */
    public function switchProvider(string $newProvider, string $apiKey = null): bool
    {
        $supportedProviders = [
            self::PROVIDER_OPENEXCHANGERATES,
            self::PROVIDER_FIXER,
            self::PROVIDER_EXCHANGERATE_API,
            self::PROVIDER_CURRENCY_LAYER
        ];

        if (!in_array($newProvider, $supportedProviders)) {
            throw new \Exception("Unsupported provider: {$newProvider}");
        }

        $oldProvider = $this->provider;
        $this->provider = $newProvider;

        if ($apiKey) {
            $this->apiKey = $apiKey;
        }

        // التحقق من صحة المزود الجديد
        $validation = $this->validateProvider();

        if ($validation['status'] !== 'healthy') {
            // العودة للمزود القديم إذا فشل
            $this->provider = $oldProvider;
            return false;
        }

        // تحديث الإعدادات
        config(['wallet.exchange_rate.provider' => $newProvider]);
        if ($apiKey) {
            config(['wallet.exchange_rate.api_key' => $apiKey]);
        }

        // مسح التخزين المؤقت
        Cache::tags(['exchange_rates'])->flush();

        Log::info('Exchange rate provider switched', [
            'from' => $oldProvider,
            'to' => $newProvider,
            'validation' => $validation
        ]);

        return true;
    }

    /**
     * الحصول على إحصائيات الاستخدام
     */
    public function getUsageStatistics(int $days = 30): array
    {
        $statistics = DB::table('exchange_rate_logs')
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_conversions'),
                DB::raw('SUM(original_amount) as total_amount'),
                DB::raw('AVG(exchange_rate) as avg_rate'),
                DB::raw('GROUP_CONCAT(DISTINCT from_currency) as from_currencies'),
                DB::raw('GROUP_CONCAT(DISTINCT to_currency) as to_currencies')
            ])
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $totalConversions = $statistics->sum('total_conversions');
        $totalAmount = $statistics->sum('total_amount');

        return [
            'period_days' => $days,
            'total_conversions' => $totalConversions,
            'total_amount' => round($totalAmount, 2),
            'daily_average_conversions' => round($totalConversions / $days, 1),
            'daily_average_amount' => round($totalAmount / $days, 2),
            'most_used_from_currency' => $this->getMostUsedCurrency($statistics, 'from_currencies'),
            'most_used_to_currency' => $this->getMostUsedCurrency($statistics, 'to_currencies'),
            'daily_statistics' => $statistics,
            'generated_at' => now()->toIso8601String()
        ];
    }

    /**
     * الحصول على العملة الأكثر استخداماً
     */
    private function getMostUsedCurrency($statistics, string $field): ?string
    {
        $currencyCounts = [];

        foreach ($statistics as $stat) {
            $currencies = explode(',', $stat->$field);
            foreach ($currencies as $currency) {
                $currencyCounts[$currency] = ($currencyCounts[$currency] ?? 0) + 1;
            }
        }

        if (empty($currencyCounts)) {
            return null;
        }

        arsort($currencyCounts);
        return array_key_first($currencyCounts);
    }

    /**
     * إنشاء تقرير شهري
     */
    public function generateMonthlyReport(int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $conversions = DB::table('exchange_rate_logs')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalConversions = $conversions->count();
        $totalAmount = $conversions->sum('original_amount');

        // تحليل حسب العملة
        $currencyAnalysis = [];
        foreach ($conversions as $conversion) {
            $key = "{$conversion->from_currency}_{$conversion->to_currency}";

            if (!isset($currencyAnalysis[$key])) {
                $currencyAnalysis[$key] = [
                    'from_currency' => $conversion->from_currency,
                    'to_currency' => $conversion->to_currency,
                    'count' => 0,
                    'total_amount' => 0,
                    'avg_rate' => 0,
                    'rates' => []
                ];
            }

            $currencyAnalysis[$key]['count']++;
            $currencyAnalysis[$key]['total_amount'] += $conversion->original_amount;
            $currencyAnalysis[$key]['rates'][] = $conversion->exchange_rate;
        }

        // حساب متوسط الأسعار
        foreach ($currencyAnalysis as &$analysis) {
            $analysis['avg_rate'] = array_sum($analysis['rates']) / count($analysis['rates']);
            unset($analysis['rates']);
        }

        return [
            'year' => $year,
            'month' => $month,
            'month_name' => $startDate->locale('ar')->monthName,
            'total_conversions' => $totalConversions,
            'total_amount' => round($totalAmount, 2),
            'average_daily_conversions' => round($totalConversions / $startDate->daysInMonth, 1),
            'most_active_day' => $this->getMostActiveDay($conversions),
            'currency_analysis' => array_values($currencyAnalysis),
            'top_conversion_pair' => $this->getTopConversionPair($currencyAnalysis),
            'generated_at' => now()->toIso8601String()
        ];
    }

    /**
     * الحصول على اليوم الأكثر نشاطاً في التحويلات
     */
    private function getMostActiveDay($conversions): ?array
    {
        $dailyConversions = [];

        foreach ($conversions as $conversion) {
            $date = $conversion->created_at->toDateString();

            if (!isset($dailyConversions[$date])) {
                $dailyConversions[$date] = [
                    'date' => $date,
                    'count' => 0,
                    'total_amount' => 0
                ];
            }

            $dailyConversions[$date]['count']++;
            $dailyConversions[$date]['total_amount'] += $conversion->original_amount;
        }

        if (empty($dailyConversions)) {
            return null;
        }

        usort($dailyConversions, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $dailyConversions[0];
    }

    /**
     * الحصول على زوج العملات الأكثر تحويلاً
     */
    private function getTopConversionPair($currencyAnalysis): ?array
    {
        if (empty($currencyAnalysis)) {
            return null;
        }

        $topPair = null;
        $maxCount = 0;

        foreach ($currencyAnalysis as $pair) {
            if ($pair['count'] > $maxCount) {
                $maxCount = $pair['count'];
                $topPair = $pair;
            }
        }

        return $topPair;
    }

    /**
     * تنظيف السجلات القديمة
     */
    public function cleanupOldLogs(int $days = 90): array
    {
        try {
            $cutoffDate = now()->subDays($days);

            // حذف سجلات التحويلات القديمة
            $logsDeleted = DB::table('exchange_rate_logs')
                ->where('created_at', '<', $cutoffDate)
                ->delete();

            // حذف أسعار الصرف القديمة
            $ratesDeleted = DB::table('exchange_rates')
                ->where('valid_to', '<', $cutoffDate)
                ->where('source', 'api')
                ->delete();

            // مسح التخزين المؤقت القديم
            $this->clearOldCache();

            Log::info('Old exchange rate data cleaned up', [
                'logs_deleted' => $logsDeleted,
                'rates_deleted' => $ratesDeleted,
                'cutoff_date' => $cutoffDate
            ]);

            return [
                'success' => true,
                'logs_deleted' => $logsDeleted,
                'rates_deleted' => $ratesDeleted,
                'message' => 'تم تنظيف البيانات القديمة بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old exchange rate logs', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * مسح التخزين المؤقت القديم
     */
    private function clearOldCache(): void
    {
        // مسح مفاتيح التخزين المؤقت القديمة (أقدم من 7 أيام)
        $oldKeys = [
            'exchange_rate_*_' . now()->subDays(7)->format('YmdH'),
            'historical_rate_*_' . now()->subDays(30)->format('Y-m-d')
        ];

        foreach ($oldKeys as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * تحسين الأداء - تحميل جميع الأسعار مرة واحدة
     */
    public function preloadExchangeRates(string $baseCurrency, array $targetCurrencies = []): array
    {
        if (empty($targetCurrencies)) {
            $supportedCurrencies = array_keys($this->getSupportedCurrencies());
            $targetCurrencies = array_diff($supportedCurrencies, [$baseCurrency]);
        }

        $cacheKey = "preloaded_rates_{$baseCurrency}_" . date('YmdH');

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($baseCurrency, $targetCurrencies) {
            $rates = [];

            try {
                // إذا كان متصل بالإنترنت، جلب جميع الأسعار دفعة واحدة
                if ($this->isLive) {
                    switch ($this->provider) {
                        case self::PROVIDER_OPENEXCHANGERATES:
                            $rates = $this->fetchAllRatesFromOpenExchangeRates($baseCurrency, $targetCurrencies);
                            break;

                        case self::PROVIDER_FIXER:
                            $rates = $this->fetchAllRatesFromFixer($baseCurrency, $targetCurrencies);
                            break;

                        case self::PROVIDER_EXCHANGERATE_API:
                            $rates = $this->fetchAllRatesFromExchangeRateApi($baseCurrency);
                            break;

                        case self::PROVIDER_CURRENCY_LAYER:
                            $rates = $this->fetchAllRatesFromCurrencyLayer($baseCurrency, $targetCurrencies);
                            break;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to preload exchange rates', [
                    'base_currency' => $baseCurrency,
                    'error' => $e->getMessage()
                ]);

                // استخدام الأسعار الافتراضية
                $rates = $this->getFallbackRatesForBase($baseCurrency, $targetCurrencies);
            }

            return [
                'base_currency' => $baseCurrency,
                'rates' => $rates,
                'preloaded_at' => now()->toIso8601String(),
                'provider' => $this->provider
            ];
        });
    }

    /**
     * جلب جميع الأسعار من OpenExchangeRates
     */
    private function fetchAllRatesFromOpenExchangeRates(string $baseCurrency, array $targetCurrencies): array
    {
        $response = Http::retry(3, 100)->get('https://openexchangerates.org/api/latest.json', [
            'app_id' => $this->apiKey,
            'base' => $baseCurrency,
            'show_alternative' => false
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenExchangeRates API failed');
        }

        $data = $response->json();
        $allRates = $data['rates'] ?? [];

        // تصفية الأسعار المطلوبة فقط
        $filteredRates = [];
        foreach ($targetCurrencies as $currency) {
            if (isset($allRates[$currency])) {
                $filteredRates[$currency] = (float) $allRates[$currency];

                // تخزين في قاعدة البيانات
                $this->storeRateInDatabase($baseCurrency, $currency, $allRates[$currency], 'openexchangerates');
            }
        }

        return $filteredRates;
    }

    /**
     * جلب جميع الأسعار من Fixer
     */
    private function fetchAllRatesFromFixer(string $baseCurrency, array $targetCurrencies): array
    {
        $response = Http::retry(3, 100)->get('http://data.fixer.io/api/latest', [
            'access_key' => $this->apiKey,
            'base' => $baseCurrency
        ]);

        if (!$response->successful()) {
            throw new \Exception('Fixer API failed');
        }

        $data = $response->json();

        if (!$data['success']) {
            throw new \Exception("Fixer API error: " . ($data['error']['info'] ?? 'Unknown error'));
        }

        $allRates = $data['rates'] ?? [];

        // تصفية الأسعار المطلوبة فقط
        $filteredRates = [];
        foreach ($targetCurrencies as $currency) {
            if (isset($allRates[$currency])) {
                $filteredRates[$currency] = (float) $allRates[$currency];

                // تخزين في قاعدة البيانات
                $this->storeRateInDatabase($baseCurrency, $currency, $allRates[$currency], 'fixer');
            }
        }

        return $filteredRates;
    }

    /**
     * جلب جميع الأسعار من ExchangeRate-API
     */
    private function fetchAllRatesFromExchangeRateApi(string $baseCurrency): array
    {
        $response = Http::retry(3, 100)->get("https://api.exchangerate-api.com/v4/latest/{$baseCurrency}");

        if (!$response->successful()) {
            throw new \Exception('ExchangeRate-API failed');
        }

        $data = $response->json();
        $rates = $data['rates'] ?? [];

        // تخزين جميع الأسعار في قاعدة البيانات
        foreach ($rates as $currency => $rate) {
            if ($currency !== $baseCurrency) {
                $this->storeRateInDatabase($baseCurrency, $currency, $rate, 'exchangerate_api');
            }
        }

        return $rates;
    }

    /**
     * جلب جميع الأسعار من Currency Layer
     */
    private function fetchAllRatesFromCurrencyLayer(string $baseCurrency, array $targetCurrencies): array
    {
        $currenciesString = implode(',', array_merge([$baseCurrency], $targetCurrencies));

        $response = Http::retry(3, 100)->get('http://apilayer.net/api/live', [
            'access_key' => $this->apiKey,
            'currencies' => $currenciesString,
            'source' => $baseCurrency,
            'format' => 1
        ]);

        if (!$response->successful()) {
            throw new \Exception('Currency Layer API failed');
        }

        $data = $response->json();

        if (!$data['success']) {
            throw new \Exception("Currency Layer API error");
        }

        $quotes = $data['quotes'] ?? [];
        $rates = [];

        foreach ($quotes as $pair => $rate) {
            // استخراج العملة المستهدفة من الزوج (مثال: USDEUR => EUR)
            $targetCurrency = substr($pair, 3);

            if (in_array($targetCurrency, $targetCurrencies)) {
                $rates[$targetCurrency] = (float) $rate;

                // تخزين في قاعدة البيانات
                $this->storeRateInDatabase($baseCurrency, $targetCurrency, $rate, 'currency_layer');
            }
        }

        return $rates;
    }

    /**
     * الحصول على الأسعار الافتراضية لعملة أساسية
     */
    private function getFallbackRatesForBase(string $baseCurrency, array $targetCurrencies): array
    {
        $rates = [];

        foreach ($targetCurrencies as $currency) {
            if (isset($this->fallbackRates[$baseCurrency][$currency])) {
                $rates[$currency] = $this->fallbackRates[$baseCurrency][$currency];
            } elseif (isset($this->fallbackRates[$currency][$baseCurrency])) {
                // حساب السعر العكسي
                $rates[$currency] = 1 / $this->fallbackRates[$currency][$baseCurrency];
            } else {
                // إذا لم يكن هناك سعر افتراضي، استخدم 1.0 (تحذير)
                $rates[$currency] = 1.0;
                Log::warning('No fallback rate available for pair', [
                    'from' => $baseCurrency,
                    'to' => $currency
                ]);
            }
        }

        return $rates;
    }

    /**
     * التحقق من صحة العملة وتحديث حالتها
     */
    public function validateAndUpdateCurrency(string $currency, array $data): bool
    {
        try {
            $supportedCurrencies = $this->getSupportedCurrencies();

            if (!isset($supportedCurrencies[$currency])) {
                throw new \Exception("Currency not supported: {$currency}");
            }

            // محاولة جلب سعر صرف لتأكيد صحة العملة
            $testRate = $this->fetchExchangeRate($currency, 'USD');

            if ($testRate <= 0) {
                throw new \Exception("Invalid exchange rate for currency: {$currency}");
            }

            // تحديث البيانات في التخزين المؤقت
            Cache::put("currency_valid_{$currency}", true, 86400); // 24 ساعة

            Log::info('Currency validated successfully', [
                'currency' => $currency,
                'test_rate_to_usd' => $testRate
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Currency validation failed', [
                'currency' => $currency,
                'error' => $e->getMessage()
            ]);

            Cache::put("currency_valid_{$currency}", false, 3600); // ساعة واحدة

            return false;
        }
    }

    /**
     * الحصول على حالة مزود الخدمة بالتفصيل
     */
    public function getProviderStatus(): array
    {
        $status = [
            'provider' => $this->provider,
            'api_key_configured' => !empty($this->apiKey),
            'live_mode' => $this->isLive,
            'cache_ttl' => $this->cacheTtl,
            'fallback_rates_loaded' => !empty($this->fallbackRates),
            'supported_currencies_count' => count($this->getSupportedCurrencies()),
            'last_validation' => null
        ];

        // إضافة آخر حالة تحقق
        $validation = $this->validateProvider();
        $status['last_validation'] = $validation;

        // إضافة إحصائيات الاستخدام
        try {
            $usageStats = $this->getUsageStatistics(7); // آخر 7 أيام
            $status['recent_usage'] = [
                'total_conversions' => $usageStats['total_conversions'],
                'daily_average' => $usageStats['daily_average_conversions']
            ];
        } catch (\Exception $e) {
            $status['recent_usage_error'] = $e->getMessage();
        }

        // إضافة حالة التخزين المؤقت
        $status['cache_status'] = [
            'enabled' => config('cache.default') !== 'null',
            'driver' => config('cache.default'),
            'preload_enabled' => config('wallet.exchange_rate.preload_enabled', false)
        ];

        return $status;
    }

    /**
     * إعادة تعيين خدمة أسعار الصرف
     */
    public function reset(): bool
    {
        try {
            // مسح التخزين المؤقت
            Cache::tags(['exchange_rates'])->flush();

            // إعادة تحميل الأسعار الافتراضية
            $this->fallbackRates = $this->loadFallbackRates();

            // إعادة قراءة الإعدادات
            $this->provider = config('wallet.exchange_rate.provider', self::PROVIDER_OPENEXCHANGERATES);
            $this->apiKey = config('wallet.exchange_rate.api_key');
            $this->cacheTtl = config('wallet.exchange_rate.cache_ttl', 3600);
            $this->isLive = config('wallet.exchange_rate.live_mode', true);

            Log::info('Exchange rate service reset successfully');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to reset exchange rate service', [
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * التحويل مع احتساب الضريبة
     */
    public function convertWithTax(
        float $amount,
        string $fromCurrency,
        string $toCurrency,
        float $taxRate = 0.0
    ): array {
        $conversionResult = $this->convert($amount, $fromCurrency, $toCurrency);

        if (!$conversionResult['success']) {
            return $conversionResult;
        }

        $taxAmount = $conversionResult['converted_amount'] * ($taxRate / 100);
        $totalAmount = $conversionResult['converted_amount'] + $taxAmount;

        return array_merge($conversionResult, [
            'tax_rate_percentage' => $taxRate,
            'tax_amount' => round($taxAmount, 2),
            'total_amount_with_tax' => round($totalAmount, 2),
            'breakdown' => [
                'converted_amount' => $conversionResult['converted_amount'],
                'tax_amount' => round($taxAmount, 2),
                'service_fee' => $conversionResult['net_amount'] - $conversionResult['converted_amount'],
                'total' => round($totalAmount, 2)
            ]
        ]);
    }

    /**
     * مقارنة أسعار الصرف بين مزودين
     */
    public function compareProviders(
        string $fromCurrency,
        string $toCurrency,
        array $providers = []
    ): array {
        if (empty($providers)) {
            $providers = [
                self::PROVIDER_OPENEXCHANGERATES,
                self::PROVIDER_FIXER,
                self::PROVIDER_EXCHANGERATE_API,
                self::PROVIDER_CURRENCY_LAYER
            ];
        }

        $comparison = [
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'compared_at' => now()->toIso8601String(),
            'providers' => []
        ];

        $originalProvider = $this->provider;
        $originalApiKey = $this->apiKey;

        foreach ($providers as $provider) {
            try {
                $this->provider = $provider;

                // استخدام API key مختلف لكل مزود إذا كان متوفراً
                $providerApiKey = config("wallet.exchange_rate.api_keys.{$provider}", $originalApiKey);
                $this->apiKey = $providerApiKey;

                $rate = $this->fetchExchangeRate($fromCurrency, $toCurrency);

                $comparison['providers'][$provider] = [
                    'rate' => $rate,
                    'status' => 'success',
                    'has_api_key' => !empty($providerApiKey)
                ];
            } catch (\Exception $e) {
                $comparison['providers'][$provider] = [
                    'rate' => null,
                    'status' => 'error',
                    'error' => $e->getMessage(),
                    'has_api_key' => !empty($providerApiKey)
                ];
            }
        }

        // العودة إلى الإعدادات الأصلية
        $this->provider = $originalProvider;
        $this->apiKey = $originalApiKey;

        // تحديد أفضل سعر
        $validRates = array_filter($comparison['providers'], function ($provider) {
            return $provider['status'] === 'success' && $provider['rate'] > 0;
        });

        if (!empty($validRates)) {
            $bestProvider = array_keys($validRates)[0];
            $bestRate = $validRates[$bestProvider]['rate'];

            foreach ($validRates as $provider => $data) {
                if ($data['rate'] > $bestRate) {
                    $bestRate = $data['rate'];
                    $bestProvider = $provider;
                }
            }

            $comparison['best_provider'] = $bestProvider;
            $comparison['best_rate'] = $bestRate;
        }

        return $comparison;
    }

    /**
     * التحويل الدفعي مع إدارة الذاكرة
     */
    public function bulkConvertWithMemoryManagement(array $conversions, int $batchSize = 50): array
    {
        $results = [];
        $totalConversions = count($conversions);

        Log::info('Starting bulk conversion with memory management', [
            'total_conversions' => $totalConversions,
            'batch_size' => $batchSize
        ]);

        // تقسيم إلى دفعات لتجنب استهلاك الذاكرة
        $batches = array_chunk($conversions, $batchSize);

        foreach ($batches as $batchIndex => $batch) {
            Log::debug('Processing batch', [
                'batch_index' => $batchIndex + 1,
                'batch_size' => count($batch),
                'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB'
            ]);

            foreach ($batch as $conversion) {
                try {
                    $result = $this->convert(
                        $conversion['amount'],
                        $conversion['from_currency'],
                        $conversion['to_currency']
                    );

                    $results[] = [
                        'success' => true,
                        'conversion_id' => $conversion['id'] ?? null,
                        'data' => $result
                    ];
                } catch (\Exception $e) {
                    $results[] = [
                        'success' => false,
                        'conversion_id' => $conversion['id'] ?? null,
                        'error' => $e->getMessage(),
                        'amount' => $conversion['amount'],
                        'from_currency' => $conversion['from_currency'],
                        'to_currency' => $conversion['to_currency']
                    ];
                }
            }

            // تنظيف الذاكرة بعد كل دفعة
            gc_collect_cycles();
        }

        Log::info('Bulk conversion completed', [
            'total_processed' => $totalConversions,
            'successful' => count(array_filter($results, fn($r) => $r['success'])),
            'failed' => count(array_filter($results, fn($r) => !$r['success'])),
            'final_memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB'
        ]);

        return [
            'total_conversions' => $totalConversions,
            'batches_processed' => count($batches),
            'successful' => count(array_filter($results, fn($r) => $r['success'])),
            'failed' => count(array_filter($results, fn($r) => !$r['success'])),
            'results' => $results,
            'processed_at' => now()->toIso8601String(),
            'memory_usage' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB'
        ];
    }
}
