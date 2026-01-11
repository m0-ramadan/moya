<?php

namespace App\Services\Security;

use App\Models\Wallet\AbstractWallet;
use App\Models\Wallet\LedgerEntry;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FraudDetector
{
    private array $config;
    private array $riskPatterns;

    public function __construct()
    {
        $this->config = config('fraud', []);
        $this->riskPatterns = $this->loadRiskPatterns();
    }

    /**
     * تحميل أنماط المخاطر من قاعدة البيانات أو الملفات
     */
    private function loadRiskPatterns(): array
    {
        return [
            'velocity' => [
                'deposits_per_hour' => 10,
                'withdrawals_per_hour' => 5,
                'transfers_per_hour' => 20,
                'transactions_per_5min' => 10,
            ],
            'amount' => [
                'max_deposit' => 50000,
                'max_withdrawal' => 20000,
                'max_transfer' => 10000,
                'suspicious_round_amount' => true,
                'unusual_amount_multiplier' => 10,
            ],
            'time' => [
                'night_hours' => [0, 6], // من 12 منتصف الليل إلى 6 صباحاً
                'rapid_transactions_seconds' => 30,
            ],
            'location' => [
                'max_distance_km' => 100,
                'country_change_limit_hours' => 24,
            ],
            'device' => [
                'max_devices_per_day' => 3,
                'suspicious_user_agents' => ['curl', 'wget', 'postman'],
            ]
        ];
    }

    /**
     * التحقق من عملية الإيداع
     */
    public function validateDeposit(AbstractWallet $wallet, float $amount, string $paymentMethod, array $details = []): bool
    {
        $riskScore = 0;
        $reasons = [];

        // 1. التحقق من السرعة (Velocity Check)
        $depositCount = $this->getRecentDepositCount($wallet, 60);
        if ($depositCount >= $this->riskPatterns['velocity']['deposits_per_hour']) {
            $riskScore += 30;
            $reasons[] = 'تجاوز عدد عمليات الإيداع المسموح بها في الساعة';
        }

        // 2. التحقق من المبلغ
        if ($amount > $this->riskPatterns['amount']['max_deposit']) {
            $riskScore += 25;
            $reasons[] = 'المبلغ يتجاوز الحد الأقصى للإيداع';
        }

        // 3. التحقق من الوقت (عمليات ليلية)
        if ($this->isSuspiciousTime()) {
            $riskScore += 15;
            $reasons[] = 'عملية في وقت متأخر من الليل';
        }

        // 4. التحقق من IP
        $ip = $details['ip_address'] ?? request()->ip();
        if ($this->isIpBlacklisted($ip)) {
            $riskScore += 50;
            $reasons[] = 'IP في القائمة السوداء';
        }

        // 5. التحقق من بصمة الجهاز
        if (isset($details['device_fingerprint'])) {
            if ($this->isDeviceSuspicious($details['device_fingerprint'])) {
                $riskScore += 40;
                $reasons[] = 'جهاز مشبوه';
            }
        }

        // 6. التحقق من أنماط المبالغ المستديرة (Round Amounts)
        if ($this->riskPatterns['amount']['suspicious_round_amount'] && $this->isRoundAmount($amount)) {
            $riskScore += 10;
            $reasons[] = 'مبلغ مستدير (مشبوه في عمليات الاحتيال)';
        }

        // 7. التحقق من التغير المفاجئ في نمط الإيداع
        if ($this->isUnusualDepositPattern($wallet, $amount)) {
            $riskScore += 20;
            $reasons[] = 'نمط إيداع غير معتاد';
        }

        // 8. التحقق من المصدر (دفعات متكررة من نفس المصدر)
        if (isset($details['payment_source'])) {
            if ($this->isSuspiciousPaymentSource($details['payment_source'], $wallet->id)) {
                $riskScore += 35;
                $reasons[] = 'مصدر دفع مشبوه';
            }
        }

        Log::info('Deposit fraud check', [
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'risk_score' => $riskScore,
            'reasons' => $reasons,
            'threshold' => $this->config['risk_threshold'] ?? 60
        ]);

        if ($riskScore >= ($this->config['risk_threshold'] ?? 60)) {
            $this->flagSuspiciousActivity($wallet, 'deposit', [
                'amount' => $amount,
                'risk_score' => $riskScore,
                'reasons' => $reasons,
                'details' => $details
            ]);
            return false;
        }

        return true;
    }

    /**
     * التحقق من عملية التحويل
     */
    public function validateTransfer(AbstractWallet $fromWallet, AbstractWallet $toWallet, float $amount): bool
    {
        $riskScore = 0;
        $reasons = [];

        // 1. التحقق من السرعة
        $transferCount = $this->getRecentTransferCount($fromWallet, 60);
        if ($transferCount >= $this->riskPatterns['velocity']['transfers_per_hour']) {
            $riskScore += 30;
            $reasons[] = 'تجاوز عدد عمليات التحويل المسموح بها في الساعة';
        }

        // 2. التحقق من المبلغ
        if ($amount > $this->riskPatterns['amount']['max_transfer']) {
            $riskScore += 25;
            $reasons[] = 'المبلغ يتجاوز الحد الأقصى للتحويل';
        }

        // 3. التحقق من حساب المستلم
        if ($this->isRecipientSuspicious($toWallet)) {
            $riskScore += 40;
            $reasons[] = 'حساب المستلم مشبوه';
        }

        // 4. التحقق من نمط التحويلات (P2P إلى نفس الشخص)
        if ($this->hasFrequentTransfersToSameRecipient($fromWallet, $toWallet)) {
            $riskScore += 35;
            $reasons[] = 'تحويلات متكررة لنفس المستلم';
        }

        // 5. التحقق من سلسلة التحويلات (Layering)
        if ($this->isLayeringPattern($fromWallet, $amount)) {
            $riskScore += 45;
            $reasons[] = 'نمط تحويلات يشبه عملية غسيل الأموال';
        }

        // 6. التحقق من الفجوة بين الإيداع والتحويل
        if ($this->hasRecentLargeDeposit($fromWallet, $amount)) {
            $riskScore += 20;
            $reasons[] = 'تحويل بعد إيداع كبير مؤخراً';
        }

        // 7. التحقق من الـ Round Amounts
        if ($this->riskPatterns['amount']['suspicious_round_amount'] && $this->isRoundAmount($amount)) {
            $riskScore += 15;
            $reasons[] = 'مبلغ مستدير مشبوه';
        }

        Log::info('Transfer fraud check', [
            'from_wallet' => $fromWallet->id,
            'to_wallet' => $toWallet->id,
            'amount' => $amount,
            'risk_score' => $riskScore,
            'reasons' => $reasons
        ]);

        if ($riskScore >= ($this->config['risk_threshold'] ?? 60)) {
            $this->flagSuspiciousActivity($fromWallet, 'transfer', [
                'to_wallet_id' => $toWallet->id,
                'amount' => $amount,
                'risk_score' => $riskScore,
                'reasons' => $reasons
            ]);
            return false;
        }

        return true;
    }

    /**
     * التحقق من عملية السحب
     */
    public function validateWithdrawal(LedgerEntry $entry, array $data = []): bool
    {
        $riskScore = 0;
        $reasons = [];

        $wallet = $this->getWalletForEntry($entry);

        // 1. التحقق من السرعة
        $withdrawalCount = $this->getRecentWithdrawalCount($wallet, 60);
        if ($withdrawalCount >= $this->riskPatterns['velocity']['withdrawals_per_hour']) {
            $riskScore += 30;
            $reasons[] = 'تجاوز عدد عمليات السحب المسموح بها في الساعة';
        }

        // 2. التحقق من المبلغ
        if ($entry->amount > $this->riskPatterns['amount']['max_withdrawal']) {
            $riskScore += 25;
            $reasons[] = 'المبلغ يتجاوز الحد الأقصى للسحب';
        }

        // 3. التحقق من الحساب البنكي
        if (isset($data['bank_account_id'])) {
            if ($this->isSuspiciousBankAccount($data['bank_account_id'], $wallet)) {
                $riskScore += 40;
                $reasons[] = 'حساب بنكي مشبوه';
            }
        }

        // 4. التحقق من تغير الحساب البنكي مؤخراً
        if ($this->hasRecentBankAccountChange($wallet)) {
            $riskScore += 20;
            $reasons[] = 'تم تغيير الحساب البنكي مؤخراً';
        }

        // 5. التحقق من نمط السحب (حجم/وقت)
        if ($this->isUnusualWithdrawalPattern($wallet, $entry->amount)) {
            $riskScore += 35;
            $reasons[] = 'نمط سحب غير معتاد';
        }

        // 6. التحقق من Rapid Succession Transactions
        if ($this->hasRapidSuccessionTransactions($wallet)) {
            $riskScore += 30;
            $reasons[] = 'معاملات متتالية بسرعة كبيرة';
        }

        // 7. التحقق من Geographic Anomalies
        if (isset($data['ip_address'])) {
            if ($this->hasGeographicAnomaly($wallet, $data['ip_address'])) {
                $riskScore += 25;
                $reasons[] = 'موقع جغرافي غير معتاد';
            }
        }

        Log::info('Withdrawal fraud check', [
            'entry_id' => $entry->id,
            'wallet_id' => $wallet->id,
            'amount' => $entry->amount,
            'risk_score' => $riskScore,
            'reasons' => $reasons
        ]);

        if ($riskScore >= ($this->config['risk_threshold'] ?? 60)) {
            $this->flagSuspiciousActivity($wallet, 'withdrawal', [
                'entry_id' => $entry->id,
                'amount' => $entry->amount,
                'risk_score' => $riskScore,
                'reasons' => $reasons,
                'data' => $data
            ]);
            return false;
        }

        return true;
    }

    /**
     * كشف الأنماط المشبوهة
     */
    public function detectSuspiciousPattern(LedgerEntry $entry): array
    {
        $patterns = [];
        $wallet = $this->getWalletForEntry($entry);

        // 1. Cascade Pattern (إيداعات صغيرة متبوعة بتحويل كبير)
        $patterns['cascade_pattern'] = $this->detectCascadePattern($wallet);

        // 2. Rapid Transaction Pattern
        $patterns['rapid_transactions'] = $this->detectRapidTransactionPattern($wallet);

        // 3. Structured Transactions (Structuring)
        $patterns['structuring'] = $this->detectStructuringPattern($wallet);

        // 4. Micro-transactions Pattern
        $patterns['micro_transactions'] = $this->detectMicroTransactionPattern($wallet);

        // 5. Circular Transactions
        $patterns['circular_transactions'] = $this->detectCircularTransactionPattern($wallet);

        // 6. High-risk Country Pattern
        $patterns['high_risk_country'] = $this->detectHighRiskCountryPattern($wallet);

        return array_filter($patterns);
    }

    /**
     * التحقق من المستخدم الجديد
     */
    public function validateNewUser(User $user, array $data = []): bool
    {
        $riskScore = 0;
        $reasons = [];

        // 1. التحقق من سرعة التسجيل
        $recentRegistrations = $this->getRecentRegistrationsFromIp($data['ip_address'] ?? request()->ip());
        if ($recentRegistrations > 3) {
            $riskScore += 30;
            $reasons[] = 'تسجيلات متعددة من نفس الـ IP';
        }

        // 2. التحقق من معلومات المستخدم
        if ($this->hasFakeUserInfo($user, $data)) {
            $riskScore += 40;
            $reasons[] = 'معلومات مستخدم مشبوهة';
        }

        // 3. التحقق من البريد الإلكتروني
        if ($this->isDisposableEmail($user->email)) {
            $riskScore += 35;
            $reasons[] = 'بريد إلكتروني مؤقت';
        }

        // 4. التحقق من رقم الهاتف
        if ($this->isVirtualPhoneNumber($user->phone)) {
            $riskScore += 25;
            $reasons[] = 'رقم هاتف افتراضي';
        }

        // 5. التحقق من الجهاز
        if (isset($data['device_fingerprint'])) {
            if ($this->isDeviceAssociatedWithFraud($data['device_fingerprint'])) {
                $riskScore += 50;
                $reasons[] = 'جهاز مرتبط بأنشطة احتيالية';
            }
        }

        if ($riskScore >= ($this->config['new_user_threshold'] ?? 50)) {
            $this->flagSuspiciousUser($user, [
                'risk_score' => $riskScore,
                'reasons' => $reasons,
                'data' => $data
            ]);
            return false;
        }

        return true;
    }

    /**
     * التحقق من السائق الجديد
     */
    public function validateNewDriver(Driver $driver, array $data = []): bool
    {
        $riskScore = 0;

        // 1. التحقق من رخصة القيادة
        if ($this->isExpiredLicense($driver->license_number, $driver->expiry_date)) {
            $riskScore += 30;
        }

        // 2. التحقق من الهوية الوطنية
        if ($this->isFakeIdNumber($driver->national_id)) {
            $riskScore += 40;
        }

        // 3. التحقق من الصورة
        if (isset($data['photo_url']) && $this->isStockPhoto($data['photo_url'])) {
            $riskScore += 35;
        }

        // 4. التحقق من العمر
        if ($driver->date_of_birth && $this->isUnderageDriver($driver->date_of_birth)) {
            $riskScore += 25;
        }

        return $riskScore < ($this->config['driver_threshold'] ?? 60);
    }

    /**
     * التحقق من عملية الدفع عبر البوابة
     */
    public function validatePaymentGatewayTransaction(array $transactionData): bool
    {
        $riskScore = 0;

        // 1. التحقق من البطاقة
        if (isset($transactionData['card_bin'])) {
            if ($this->isStolenCard($transactionData['card_bin'], $transactionData['card_last4'])) {
                $riskScore += 50;
            }
        }

        // 2. التحقق من الـ AVS
        if (isset($transactionData['avs_result']) && $transactionData['avs_result'] !== 'Y') {
            $riskScore += 20;
        }

        // 3. التحقق من الـ CVV
        if (isset($transactionData['cvv_result']) && $transactionData['cvv_result'] !== 'M') {
            $riskScore += 25;
        }

        // 4. التحقق من 3D Secure
        if (isset($transactionData['three_d_secure']) && !$transactionData['three_d_secure']) {
            $riskScore += 15;
        }

        // 5. التحقق من البلد
        if (isset($transactionData['country']) && $this->isHighRiskCountry($transactionData['country'])) {
            $riskScore += 30;
        }

        return $riskScore < ($this->config['payment_threshold'] ?? 50);
    }

    /**
     * =========================================
     * Helper Methods
     * =========================================
     */

    private function getRecentDepositCount(AbstractWallet $wallet, int $minutes = 60): int
    {
        $cacheKey = "deposit_count_{$wallet->getWalletType()}_{$wallet->id}_" . now()->format('YmdHi');

        return Cache::remember($cacheKey, 60, function () use ($wallet, $minutes) {
            return $wallet->ledgerEntries()
                ->where('type', LedgerEntry::TYPE_DEPOSIT)
                ->where('status', LedgerEntry::STATUS_COMPLETED)
                ->where('created_at', '>=', now()->subMinutes($minutes))
                ->count();
        });
    }

    private function getRecentTransferCount(AbstractWallet $wallet, int $minutes = 60): int
    {
        $cacheKey = "transfer_count_{$wallet->getWalletType()}_{$wallet->id}_" . now()->format('YmdHi');

        return Cache::remember($cacheKey, 60, function () use ($wallet, $minutes) {
            return $wallet->ledgerEntries()
                ->whereIn('type', [LedgerEntry::TYPE_TRANSFER_OUT, LedgerEntry::TYPE_TRANSFER_IN])
                ->where('status', LedgerEntry::STATUS_COMPLETED)
                ->where('created_at', '>=', now()->subMinutes($minutes))
                ->count();
        });
    }

    private function getRecentWithdrawalCount(AbstractWallet $wallet, int $minutes = 60): int
    {
        $cacheKey = "withdrawal_count_{$wallet->getWalletType()}_{$wallet->id}_" . now()->format('YmdHi');

        return Cache::remember($cacheKey, 60, function () use ($wallet, $minutes) {
            $types = $wallet->getWalletType() === 'user'
                ? [LedgerEntry::TYPE_WITHDRAWAL]
                : [LedgerEntry::TYPE_CASHOUT];

            return $wallet->ledgerEntries()
                ->whereIn('type', $types)
                ->where('status', LedgerEntry::STATUS_COMPLETED)
                ->where('created_at', '>=', now()->subMinutes($minutes))
                ->count();
        });
    }

    private function isSuspiciousTime(): bool
    {
        $hour = now()->hour;
        $nightStart = $this->riskPatterns['time']['night_hours'][0];
        $nightEnd = $this->riskPatterns['time']['night_hours'][1];

        return $hour >= $nightStart || $hour < $nightEnd;
    }

    private function isIpBlacklisted(string $ip): bool
    {
        $blacklist = Cache::remember('ip_blacklist', 3600, function () {
            return DB::table('fraud_blacklists')
                ->where('type', 'ip')
                ->pluck('value')
                ->toArray();
        });

        // Check exact IP
        if (in_array($ip, $blacklist)) {
            return true;
        }

        // Check IP range
        foreach ($blacklist as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    private function ipInRange($ip, $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);

        return ($ip & $mask) === ($subnet & $mask);
    }

    private function isDeviceSuspicious(string $fingerprint): bool
    {
        $suspiciousDevices = Cache::remember('suspicious_devices', 3600, function () {
            return DB::table('fraud_blacklists')
                ->where('type', 'device')
                ->pluck('value')
                ->toArray();
        });

        return in_array($fingerprint, $suspiciousDevices);
    }

    private function isRoundAmount(float $amount): bool
    {
        $rounded = round($amount, 2);
        return abs($amount - $rounded) < 0.001 &&
            fmod($rounded * 100, 100) === 0.0;
    }

    private function isUnusualDepositPattern(AbstractWallet $wallet, float $amount): bool
    {
        $avgDeposit = $wallet->ledgerEntries()
            ->where('type', LedgerEntry::TYPE_DEPOSIT)
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subDays(30))
            ->avg('amount');

        if (!$avgDeposit) {
            return false;
        }

        $multiplier = $this->riskPatterns['amount']['unusual_amount_multiplier'];
        return $amount > ($avgDeposit * $multiplier);
    }

    private function isSuspiciousPaymentSource(string $source, int $walletId): bool
    {
        $recentUses = DB::table('ledger_entries')
            ->where('metadata->payment_source', $source)
            ->where('wallet_id', '!=', $walletId)
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        return $recentUses > 2;
    }

    private function isRecipientSuspicious(AbstractWallet $wallet): bool
    {
        $suspiciousWallets = Cache::remember('suspicious_wallets', 1800, function () {
            return DB::table('fraud_alerts')
                ->where('status', 'confirmed')
                ->pluck('wallet_id')
                ->toArray();
        });

        return in_array($wallet->id, $suspiciousWallets);
    }

    private function hasFrequentTransfersToSameRecipient(AbstractWallet $fromWallet, AbstractWallet $toWallet): bool
    {
        $count = $fromWallet->ledgerEntries()
            ->where('type', LedgerEntry::TYPE_TRANSFER_OUT)
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('related_owner_type', $toWallet->getWalletType())
            ->where('related_owner_id', $toWallet->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        return $count > 5;
    }

    private function isLayeringPattern(AbstractWallet $wallet, float $amount): bool
    {
        // تحقق من نمط الإيداع والتحويل السريع
        $recentDeposit = $wallet->ledgerEntries()
            ->where('type', LedgerEntry::TYPE_DEPOSIT)
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('amount', '>=', $amount * 0.8)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->exists();

        return $recentDeposit;
    }

    private function hasRecentLargeDeposit(AbstractWallet $wallet, float $transferAmount): bool
    {
        $deposit = $wallet->ledgerEntries()
            ->where('type', LedgerEntry::TYPE_DEPOSIT)
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('amount', '>=', $transferAmount)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        return $deposit !== null;
    }

    private function isSuspiciousBankAccount(int $bankAccountId, AbstractWallet $wallet): bool
    {
        // تحقق من عدد الحسابات المرتبطة بهذا الرقم
        $accountUses = DB::table('bank_accounts_usage')
            ->where('bank_account_id', $bankAccountId)
            ->where('wallet_id', '!=', $wallet->id)
            ->count();

        return $accountUses > 2;
    }

    private function hasRecentBankAccountChange(AbstractWallet $wallet): bool
    {
        $recentChanges = DB::table('bank_account_changes')
            ->where('wallet_id', $wallet->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        return $recentChanges > 0;
    }

    private function isUnusualWithdrawalPattern(AbstractWallet $wallet, float $amount): bool
    {
        $avgWithdrawal = $wallet->ledgerEntries()
            ->whereIn('type', $wallet->getWalletType() === 'user'
                ? [LedgerEntry::TYPE_WITHDRAWAL]
                : [LedgerEntry::TYPE_CASHOUT])
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subDays(30))
            ->avg('amount');

        if (!$avgWithdrawal) {
            return false;
        }

        return $amount > ($avgWithdrawal * 3);
    }

    private function hasRapidSuccessionTransactions(AbstractWallet $wallet): bool
    {
        $rapidSeconds = $this->riskPatterns['time']['rapid_transactions_seconds'];

        $lastTransaction = $wallet->ledgerEntries()
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastTransaction) {
            return false;
        }

        return $lastTransaction->created_at->diffInSeconds(now()) < $rapidSeconds;
    }

    private function hasGeographicAnomaly(AbstractWallet $wallet, string $currentIp): bool
    {
        $previousLocation = Cache::get("wallet_location_{$wallet->id}");

        if (!$previousLocation) {
            Cache::put("wallet_location_{$wallet->id}", $currentIp, 3600);
            return false;
        }

        $currentCountry = $this->getCountryFromIp($currentIp);
        $previousCountry = $this->getCountryFromIp($previousLocation);

        if ($currentCountry && $previousCountry && $currentCountry !== $previousCountry) {
            // تحقق من الوقت بين تغيير البلد
            $lastChange = Cache::get("country_change_{$wallet->id}");

            if ($lastChange && now()->diffInHours($lastChange) < 24) {
                return true;
            }

            Cache::put("country_change_{$wallet->id}", now(), 86400);
        }

        Cache::put("wallet_location_{$wallet->id}", $currentIp, 3600);
        return false;
    }

    private function getCountryFromIp(string $ip): ?string
    {
        // استخدم خدمة مثل ipapi.co أو maxmind
        try {
            // هذه مثال - استبدل بخدمة حقيقية
            $response = @file_get_contents("http://ip-api.com/json/{$ip}");
            if ($response) {
                $data = json_decode($response, true);
                return $data['countryCode'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get country from IP', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Pattern Detection Methods
     */

    private function detectCascadePattern(AbstractWallet $wallet): bool
    {
        $recentDeposits = $wallet->ledgerEntries()
            ->where('type', LedgerEntry::TYPE_DEPOSIT)
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subHours(2))
            ->get();

        if ($recentDeposits->count() < 3) {
            return false;
        }

        $smallDeposits = $recentDeposits->filter(fn($entry) => $entry->amount < 100);
        $largeTransfers = $wallet->ledgerEntries()
            ->where('type', LedgerEntry::TYPE_TRANSFER_OUT)
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subHours(2))
            ->where('amount', '>', 1000)
            ->count();

        return $smallDeposits->count() >= 3 && $largeTransfers > 0;
    }

    private function detectRapidTransactionPattern(AbstractWallet $wallet): bool
    {
        $recentTransactions = $wallet->ledgerEntries()
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        return $recentTransactions > $this->riskPatterns['velocity']['transactions_per_5min'];
    }

    private function detectStructuringPattern(AbstractWallet $wallet): bool
    {
        // Smurfing/Structuring: تقسيم مبلغ كبير إلى معاملات صغيرة لتجنب التقارير
        $smallTransactions = $wallet->ledgerEntries()
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('amount', '<', 1000) // أقل من حد التقرير
            ->where('created_at', '>=', now()->subHours(24))
            ->get();

        if ($smallTransactions->count() < 10) {
            return false;
        }

        $totalAmount = $smallTransactions->sum('amount');
        return $totalAmount > 5000; // مجموع المعاملات الصغيرة يتجاوز حداً معيناً
    }

    private function detectMicroTransactionPattern(AbstractWallet $wallet): bool
    {
        // معاملات صغيرة جداً متكررة (لاختبار البطاقات المسروقة)
        $microTransactions = $wallet->ledgerEntries()
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('amount', '<', 1) // أقل من 1 ريال
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        return $microTransactions > 5;
    }

    private function detectCircularTransactionPattern(AbstractWallet $wallet): bool
    {
        // تحويلات دائرية بين حسابات
        $circularTransfers = DB::select("
            SELECT COUNT(*) as count
            FROM ledger_entries le1
            JOIN ledger_entries le2 ON le1.related_entry_id = le2.id
            WHERE le1.wallet_id = ?
            AND le1.type = 'transfer_out'
            AND le2.type = 'transfer_in'
            AND le1.created_at >= NOW() - INTERVAL 24 HOUR
            AND le2.created_at >= NOW() - INTERVAL 24 HOUR
        ", [$wallet->id]);

        return $circularTransfers[0]->count > 2;
    }

    private function detectHighRiskCountryPattern(AbstractWallet $wallet): bool
    {
        $highRiskCountries = [
            'AF', // أفغانستان
            'IR', // إيران
            'KP', // كوريا الشمالية
            'SY', // سوريا
            'YE'  // اليمن
        ];

        $transactions = $wallet->ledgerEntries()
            ->whereNotNull('metadata->country_code')
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        foreach ($transactions as $transaction) {
            $countryCode = $transaction->metadata['country_code'] ?? null;
            if ($countryCode && in_array($countryCode, $highRiskCountries)) {
                return true;
            }
        }

        return false;
    }

    /**
     * User Validation Methods
     */

    private function getRecentRegistrationsFromIp(string $ip): int
    {
        $cacheKey = "registrations_ip_{$ip}_" . now()->format('YmdH');

        return Cache::remember($cacheKey, 3600, function () use ($ip) {
            return DB::table('users')
                ->where('registration_ip', $ip)
                ->where('created_at', '>=', now()->subHours(24))
                ->count();
        });
    }

    private function hasFakeUserInfo(User $user, array $data): bool
    {
        // تحقق من أسماء وهمية
        $fakeNames = [
            'test',
            'user',
            'admin',
            'guest',
            'demo',
            'محمد',
            'أحمد',
            'علي',
            'محمود' // أسماء شائعة جداً
        ];

        $nameLower = strtolower($user->name);
        foreach ($fakeNames as $fakeName) {
            if (strpos($nameLower, $fakeName) !== false) {
                return true;
            }
        }

        // تحقق من البريد الإلكتروني
        if (
            preg_match('/test\d*@/', $user->email) ||
            preg_match('/user\d*@/', $user->email)
        ) {
            return true;
        }

        return false;
    }

    private function isDisposableEmail(string $email): bool
    {
        $disposableDomains = [
            'tempmail.com',
            '10minutemail.com',
            'guerrillamail.com',
            'mailinator.com',
            'yopmail.com',
            'trashmail.com'
        ];

        $domain = substr(strrchr($email, "@"), 1);
        return in_array($domain, $disposableDomains);
    }

    private function isVirtualPhoneNumber(string $phone): bool
    {
        // أرقام وهمية أو افتراضية
        $virtualPatterns = [
            '/^\+555/', // أرقام اختبار
            '/^\+123/', // أرقام نموذجية
            '/^000000/', // أرقام صفرية
        ];

        foreach ($virtualPatterns as $pattern) {
            if (preg_match($pattern, $phone)) {
                return true;
            }
        }

        return false;
    }

    private function isDeviceAssociatedWithFraud(string $fingerprint): bool
    {
        return DB::table('fraud_devices')
            ->where('device_fingerprint', $fingerprint)
            ->where('created_at', '>=', now()->subDays(30))
            ->exists();
    }

    /**
     * Driver Validation Methods
     */

    private function isExpiredLicense(string $licenseNumber, $expiryDate): bool
    {
        if (!$expiryDate) {
            return true;
        }

        return Carbon::parse($expiryDate)->isPast();
    }

    private function isFakeIdNumber(string $idNumber): bool
    {
        // تحقق من صحة الهوية الوطنية السعودية
        if (strlen($idNumber) !== 10) {
            return true;
        }

        // تحقق من أن جميع الأحرف أرقام
        if (!ctype_digit($idNumber)) {
            return true;
        }

        // تحقق من أول رقم (1 لمقيم، 2 لمواطن)
        $firstDigit = $idNumber[0];
        if (!in_array($firstDigit, ['1', '2'])) {
            return true;
        }

        return false;
    }

    private function isStockPhoto(string $photoUrl): bool
    {
        // تحقق من أن الصورة ليست من مواقع الصور المخزنة
        $stockDomains = [
            'shutterstock.com',
            'istockphoto.com',
            'gettyimages.com',
            'adobestock.com',
            'alamy.com',
            'depositphotos.com'
        ];

        foreach ($stockDomains as $domain) {
            if (strpos($photoUrl, $domain) !== false) {
                return true;
            }
        }

        return false;
    }

    private function isUnderageDriver($dateOfBirth): bool
    {
        $age = Carbon::parse($dateOfBirth)->age;
        return $age < 18;
    }

    /**
     * Payment Gateway Validation Methods
     */

    private function isStolenCard(string $bin, string $last4): bool
    {
        return DB::table('stolen_cards')
            ->where('bin', $bin)
            ->where('last4', $last4)
            ->where('reported_at', '>=', now()->subDays(90))
            ->exists();
    }

    private function isHighRiskCountry(string $countryCode): bool
    {
        $highRiskCountries = Cache::remember('high_risk_countries', 86400, function () {
            return DB::table('high_risk_countries')->pluck('country_code')->toArray();
        });

        return in_array($countryCode, $highRiskCountries);
    }

    /**
     * Utility Methods
     */

    private function getWalletForEntry(LedgerEntry $entry): AbstractWallet
    {
        if ($entry->wallet_type === 'user') {
            return \App\Models\Wallet\UserWallet::findOrFail($entry->wallet_id);
        } elseif ($entry->wallet_type === 'driver') {
            return \App\Models\Wallet\DriverWallet::findOrFail($entry->wallet_id);
        }

        throw new \Exception('Unknown wallet type');
    }

    private function flagSuspiciousActivity(AbstractWallet $wallet, string $type, array $data): void
    {
        DB::table('fraud_alerts')->insert([
            'wallet_id' => $wallet->id,
            'wallet_type' => $wallet->getWalletType(),
            'alert_type' => $type,
            'risk_score' => $data['risk_score'],
            'details' => json_encode($data),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Log::warning('Suspicious activity flagged', [
            'wallet_id' => $wallet->id,
            'type' => $type,
            'risk_score' => $data['risk_score'],
            'reasons' => $data['reasons'] ?? []
        ]);

        // إضافة إلى القائمة السوداء إذا تجاوزت النسبة
        if ($data['risk_score'] >= 80) {
            $this->addToBlacklist($wallet, $type, $data);
        }
    }

    private function flagSuspiciousUser(User $user, array $data): void
    {
        DB::table('suspicious_users')->insert([
            'user_id' => $user->id,
            'risk_score' => $data['risk_score'],
            'reasons' => json_encode($data['reasons']),
            'status' => 'pending_review',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // تعليق الحساب مؤقتاً
        $user->update(['status' => 'suspended']);
    }

    private function addToBlacklist(AbstractWallet $wallet, string $type, array $data): void
    {
        DB::table('fraud_blacklists')->insert([
            'type' => 'wallet',
            'value' => (string) $wallet->id,
            'reason' => implode(', ', $data['reasons']),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Cache::forget('suspicious_wallets');
    }

    /**
     * التحليل الإحصائي
     */
    public function getRiskAnalytics(AbstractWallet $wallet): array
    {
        $thirtyDaysAgo = now()->subDays(30);

        return [
            'total_transactions' => $wallet->ledgerEntries()
                ->where('status', LedgerEntry::STATUS_COMPLETED)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count(),
            'total_amount' => $wallet->ledgerEntries()
                ->where('status', LedgerEntry::STATUS_COMPLETED)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->sum('amount'),
            'avg_transaction_amount' => $wallet->ledgerEntries()
                ->where('status', LedgerEntry::STATUS_COMPLETED)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->avg('amount'),
            'peak_hours' => $this->getPeakHours($wallet),
            'frequent_recipients' => $this->getFrequentRecipients($wallet),
            'unusual_patterns' => $this->detectSuspiciousPattern(new LedgerEntry(['wallet_id' => $wallet->id]))
        ];
    }

    private function getPeakHours(AbstractWallet $wallet): array
    {
        return DB::table('ledger_entries')
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->where('wallet_id', $wallet->id)
            ->where('wallet_type', $wallet->getWalletType())
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getFrequentRecipients(AbstractWallet $wallet): array
    {
        return DB::table('ledger_entries')
            ->select('related_owner_id', 'related_owner_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->where('wallet_id', $wallet->id)
            ->where('wallet_type', $wallet->getWalletType())
            ->where('type', 'transfer_out')
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('related_owner_id')
            ->groupBy('related_owner_id', 'related_owner_type')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * إعادة تقييم الحسابات
     */
    public function reassessWalletRisk(AbstractWallet $wallet): void
    {
        $analytics = $this->getRiskAnalytics($wallet);
        $riskScore = 0;

        // تقييم بناء على الإحصائيات
        if ($analytics['total_transactions'] > 1000) {
            $riskScore += 10;
        }

        if ($analytics['avg_transaction_amount'] > 5000) {
            $riskScore += 15;
        }

        // تحديث مستوى الخطورة
        DB::table('wallet_risk_scores')->updateOrInsert(
            ['wallet_id' => $wallet->id, 'wallet_type' => $wallet->getWalletType()],
            [
                'risk_score' => $riskScore,
                'analytics' => json_encode($analytics),
                'updated_at' => now()
            ]
        );
    }
}
