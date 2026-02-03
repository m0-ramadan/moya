<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Driver;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Wallet\UserWallet;
use App\Models\Wallet\LedgerEntry;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet\DriverWallet;

class WalletTransactionsSeeder extends Seeder
{
    private $faker;
    private $currencies = ['SAR', 'USD', 'EGP', 'AED'];

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 بدء إضافة معاملات المحافظ...');

        DB::transaction(function () {
            // 1. معاملات المستخدمين
            $this->seedUserTransactions();

            // 2. معاملات السائقين
            $this->seedDriverTransactions();

            // 3. معاملات التحويل بين المستخدمين
            $this->seedTransferTransactions();
        });

        $this->command->info('✅ تم إضافة معاملات المحافظ بنجاح!');
    }

    /**
     * إضافة معاملات للمستخدمين
     */
    private function seedUserTransactions(): void
    {
        $users = User::where('type', 'user')
            ->has('userWallet')
            ->limit(50)
            ->get();

        $progressBar = $this->command->getOutput()->createProgressBar($users->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

        foreach ($users as $user) {
            $progressBar->setMessage("معالجة المستخدم: {$user->id}");

            $wallet = $user->userWallet;
            $balance = (float) $wallet->balance;

            // إضافة 3-5 إيداعات لكل مستخدم
            $depositCount = rand(3, 5);
            $totalDeposits = 0;

            for ($i = 0; $i < $depositCount; $i++) {
                $depositAmount = $this->generateDepositAmount($user);
                $this->createUserDeposit($user, $wallet, $depositAmount);
                $totalDeposits += $depositAmount;
            }

            // إضافة 2-4 عمليات سحب لكل مستخدم
            $withdrawalCount = rand(2, 4);
            $totalWithdrawals = 0;

            for ($i = 0; $i < $withdrawalCount; $i++) {
                $withdrawalAmount = $this->generateWithdrawalAmount($user, $wallet, $totalDeposits, $totalWithdrawals);
                if ($withdrawalAmount > 0) {
                    $this->createUserWithdrawal($user, $wallet, $withdrawalAmount);
                    $totalWithdrawals += $withdrawalAmount;
                }
            }

            // إضافة معاملات متنوعة
            $this->createUserMiscTransactions($user, $wallet);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info("📊 تم إضافة معاملات لـ {$users->count()} مستخدم");
    }

    /**
     * إضافة معاملات للسائقين
     */
    private function seedDriverTransactions(): void
    {
        $drivers = Driver::has('driverWallet')
            ->limit(30)
            ->get();

        $progressBar = $this->command->getOutput()->createProgressBar($drivers->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

        foreach ($drivers as $driver) {
            $progressBar->setMessage("معالجة السائق: {$driver->id}");

            $wallet = $driver->driverWallet;

            // إضافة أرباح من الطلبات (5-10 طلبات)
            $orderCount = rand(5, 10);
            $totalEarnings = 0;

            for ($i = 0; $i < $orderCount; $i++) {
                $earningAmount = $this->generateEarningAmount($driver);
                $this->createDriverEarning($driver, $wallet, $earningAmount, $i + 1);
                $totalEarnings += $earningAmount;
            }

            // إضافة عمولات
            $commissionCount = rand(1, 3);
            for ($i = 0; $i < $commissionCount; $i++) {
                $commissionAmount = rand(50, 500);
                $this->createDriverCommission($driver, $wallet, $commissionAmount);
            }

            // إضافة عمليات سحب (Cashout) - 1-2 عملية
            $cashoutCount = rand(1, 2);
            $totalCashouts = 0;

            for ($i = 0; $i < $cashoutCount; $i++) {
                $cashoutAmount = $this->generateCashoutAmount($driver, $wallet, $totalEarnings, $totalCashouts);
                if ($cashoutAmount > 0) {
                    $this->createDriverCashout($driver, $wallet, $cashoutAmount);
                    $totalCashouts += $cashoutAmount;
                }
            }

            // إضافة رسوم وخصومات
            $this->createDriverFees($driver, $wallet);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info("📊 تم إضافة معاملات لـ {$drivers->count()} سائق");
    }

    /**
     * إضافة معاملات تحويل بين المستخدمين
     */
    private function seedTransferTransactions(): void
    {
        $users = User::where('type', 'user')
            ->has('userWallet')
            ->inRandomOrder()
            ->limit(20)
            ->get();

        if ($users->count() < 2) {
            return;
        }

        $transferCount = rand(10, 20);
        $this->command->info("💰 إنشاء {$transferCount} عملية تحويل...");

        for ($i = 0; $i < $transferCount; $i++) {
            // اختيار مرسل ومستلم عشوائي
            $fromUser = $users->random();
            $toUser = $users->where('id', '!=', $fromUser->id)->random();

            $fromWallet = $fromUser->userWallet;
            $toWallet = $toUser->userWallet;

            // تحديد مبلغ التحويل
            $transferAmount = rand(50, min(1000, (int)($fromWallet->balance * 0.3)));

            if ($transferAmount > 0 && $fromWallet->balance >= $transferAmount) {
                $this->createTransferTransaction($fromUser, $toUser, $fromWallet, $toWallet, $transferAmount);
            }
        }
    }

    /**
     * إنشاء إيداع للمستخدم
     */
    private function createUserDeposit(User $user, UserWallet $wallet, float $amount): void
    {
        $depositDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $paymentMethod = $this->getRandomPaymentMethod();

        $transaction = LedgerEntry::create([
            'wallet_type' => 'user',
            'wallet_id' => $wallet->id,
            'owner_type' => 'user',
            'owner_id' => $user->id,
            'type' => LedgerEntry::TYPE_DEPOSIT,
            'amount' => $amount,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance + $amount,
            'available_balance_before' => $wallet->available_balance,
            'available_balance_after' => ($wallet->available_balance + $amount),
            'payment_method' => $paymentMethod,
            'payment_transaction_id' => 'DEP-' . Str::random(10),
            'description' => $this->getDepositDescription($paymentMethod),
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => 'DEP-' . now()->format('Ymd') . '-' . Str::random(6),
            'metadata' => json_encode([
                'deposit_method' => $paymentMethod,
                'currency' => $wallet->currency,
                'exchange_rate' => $this->getExchangeRate($wallet->currency),
                'gateway_response' => [
                    'status' => 'success',
                    'transaction_id' => 'GW-' . Str::random(8),
                    'approved_at' => $depositDate->format('Y-m-d H:i:s')
                ]
            ]),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'processed_at' => $depositDate,
            'created_at' => $depositDate,
            'updated_at' => $depositDate
        ]);

        // تحديث الرصيد فقط (available_balance سيتم تحديثه تلقائياً كونه generated column)
        $wallet->update([
            'balance' => $wallet->balance + $amount,
            'last_transaction_at' => $depositDate
        ]);
    }

    /**
     * إنشاء سحب للمستخدم
     */
    private function createUserWithdrawal(User $user, UserWallet $wallet, float $amount): void
    {
        $withdrawalDate = $this->faker->dateTimeBetween('-5 months', 'now');

        $transaction = LedgerEntry::create([
            'wallet_type' => 'user',
            'wallet_id' => $wallet->id,
            'owner_type' => 'user',
            'owner_id' => $user->id,
            'type' => LedgerEntry::TYPE_WITHDRAWAL,
            'amount' => $amount,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance - $amount,
            'available_balance_before' => $wallet->available_balance,
            'available_balance_after' => ($wallet->available_balance - $amount),
            'payment_method' => 'bank_transfer',
            'description' => 'سحب رصيد إلى الحساب البنكي',
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => 'WTH-' . now()->format('Ymd') . '-' . Str::random(6),
            'metadata' => json_encode([
                'withdrawal_method' => 'bank_transfer',
                'bank_account' => [
                    'bank_name' => $this->getRandomBankName(),
                    'account_number' => '****' . rand(1000, 9999),
                    'iban' => 'SA' . rand(100000000000000000, 999999999999999999)
                ],
                'processing_time' => rand(1, 3) . ' أيام عمل',
                'fee' => $amount * 0.01,
                'net_amount' => $amount * 0.99
            ]),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'processed_at' => $withdrawalDate,
            'created_at' => $withdrawalDate,
            'updated_at' => $withdrawalDate
        ]);

        $wallet->update([
            'balance' => $wallet->balance - $amount,
            'last_transaction_at' => $withdrawalDate
        ]);
    }

    /**
     * إنشاء أرباح للسائق
     */
    private function createDriverEarning(Driver $driver, DriverWallet $wallet, float $amount, int $orderNumber): void
    {
        $earningDate = $this->faker->dateTimeBetween('-3 months', 'now');
        $orderId = 'ORDER-' . Str::random(8);

        $transaction = LedgerEntry::create([
            'wallet_type' => 'driver',
            'wallet_id' => $wallet->id,
            'owner_type' => 'driver',
            'owner_id' => $driver->id,
            'type' => LedgerEntry::TYPE_EARNING,
            'amount' => $amount,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance + $amount,
            'available_balance_before' => $wallet->available_balance,
            'available_balance_after' => ($wallet->available_balance + $amount),
            'description' => "أرباح من طلب #{$orderId}",
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => 'ERN-' . now()->format('Ymd') . '-' . Str::random(6),
            'metadata' => json_encode([
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'earning_type' => 'trip_completion',
                'trip_details' => [
                    'distance' => rand(5, 50) . ' كم',
                    'duration' => rand(15, 120) . ' دقيقة',
                    'pickup_location' => $this->faker->address,
                    'dropoff_location' => $this->faker->address,
                    'fare' => $amount,
                    'commission' => $amount * 0.20,
                    'driver_share' => $amount * 0.80
                ]
            ]),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'processed_at' => $earningDate,
            'created_at' => $earningDate,
            'updated_at' => $earningDate
        ]);

        $wallet->update([
            'balance' => $wallet->balance + $amount,
            'last_transaction_at' => $earningDate
        ]);
    }

    /**
     * إنشاء عمولة للسائق
     */
    private function createDriverCommission(Driver $driver, DriverWallet $wallet, float $amount): void
    {
        $commissionDate = $this->faker->dateTimeBetween('-2 months', 'now');

        $transaction = LedgerEntry::create([
            'wallet_type' => 'driver',
            'wallet_id' => $wallet->id,
            'owner_type' => 'driver',
            'owner_id' => $driver->id,
            'type' => LedgerEntry::TYPE_COMMISSION,
            'amount' => $amount,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance + $amount,
            'available_balance_before' => $wallet->available_balance,
            'available_balance_after' => ($wallet->available_balance + $amount),
            'description' => 'عمولة تشجيعية',
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => 'COM-' . now()->format('Ymd') . '-' . Str::random(6),
            'metadata' => json_encode([
                'commission_type' => 'incentive',
                'reason' => 'أداء ممتاز',
                'period' => 'شهري',
                'target_achieved' => '110%',
                'additional_bonus' => $amount * 0.10
            ]),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'processed_at' => $commissionDate,
            'created_at' => $commissionDate,
            'updated_at' => $commissionDate
        ]);

        $wallet->update([
            'balance' => $wallet->balance + $amount,
            'last_transaction_at' => $commissionDate
        ]);
    }

    /**
     * إنشاء سحب للسائق (Cashout)
     */
    /**
     * إنشاء سحب للسائق (Cashout)
     */
    private function createDriverCashout(Driver $driver, DriverWallet $wallet, float $amount): void
    {
        $cashoutDate = $this->faker->dateTimeBetween('-1 month', 'now');
        $status = $this->getRandomCashoutStatus();

        // Clone the date to avoid modifying the original object
        $estimatedCompletion = clone $cashoutDate;
        $estimatedCompletion->modify('+' . rand(1, 3) . ' days');

        $transaction = LedgerEntry::create([
            'wallet_type' => 'driver',
            'wallet_id' => $wallet->id,
            'owner_type' => 'driver',
            'owner_id' => $driver->id,
            'type' => LedgerEntry::TYPE_CASHOUT,
            'amount' => $amount,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance - $amount,
            'available_balance_before' => $wallet->available_balance,
            'available_balance_after' => ($wallet->available_balance - $amount),
            'payment_method' => 'bank_transfer',
            'description' => 'سحب أرباح',
            'status' => $status,
            'reference' => 'CASH-' . now()->format('Ymd') . '-' . Str::random(6),
            'metadata' => json_encode([
                'withdrawal_method' => 'bank_transfer',
                'bank_account' => [
                    'bank_name' => $this->getRandomBankName(),
                    'account_number' => '****' . rand(1000, 9999),
                    'account_holder' => $driver->user?->name ?? $driver->first_name . ' ' . $driver->family_name
                ],
                'processing_fee' => $amount * 0.02,
                'net_amount' => $amount * 0.98,
                'requested_at' => $cashoutDate->format('Y-m-d H:i:s'),
                'estimated_completion' => $estimatedCompletion->format('Y-m-d H:i:s')
            ]),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'processed_at' => $status === LedgerEntry::STATUS_COMPLETED ? $cashoutDate : null,
            'approved_at' => $status === LedgerEntry::STATUS_COMPLETED ? $cashoutDate : null,
            'approved_by' => $status === LedgerEntry::STATUS_COMPLETED ? 1 : null,
            'created_at' => $cashoutDate,
            'updated_at' => $cashoutDate
        ]);

        if ($status === LedgerEntry::STATUS_COMPLETED) {
            $wallet->update([
                'balance' => $wallet->balance - $amount,
                'last_transaction_at' => $cashoutDate
            ]);
        }
    }

    /**
     * إنشاء تحويل بين المستخدمين
     */
    private function createTransferTransaction(
        User $fromUser,
        User $toUser,
        UserWallet $fromWallet,
        UserWallet $toWallet,
        float $amount
    ): void {
        $transferDate = $this->faker->dateTimeBetween('-4 months', 'now');
        $transferId = 'TRF-' . Str::random(10);

        // 1. Debit transaction (من المرسل)
        $debitTransaction = LedgerEntry::create([
            'wallet_type' => 'user',
            'wallet_id' => $fromWallet->id,
            'owner_type' => 'user',
            'owner_id' => $fromUser->id,
            'type' => LedgerEntry::TYPE_TRANSFER_OUT,
            'amount' => $amount,
            'balance_before' => $fromWallet->balance,
            'balance_after' => $fromWallet->balance - $amount,
            'available_balance_before' => $fromWallet->available_balance,
            'available_balance_after' => ($fromWallet->available_balance - $amount),
            'description' => 'تحويل إلى ' . ($toUser->name ?? 'مستخدم آخر'),
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => $transferId,
            'related_owner_type' => 'user',
            'related_owner_id' => $toUser->id,
            'metadata' => json_encode([
                'transfer_type' => 'peer_to_peer',
                'to_user_id' => $toUser->id,
                'to_user_name' => $toUser->name,
                'fee' => $amount * 0.005,
                'transfer_speed' => 'instant'
            ]),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'processed_at' => $transferDate,
            'created_at' => $transferDate,
            'updated_at' => $transferDate
        ]);

        // 2. Credit transaction (إلى المستلم)
        $creditTransaction = LedgerEntry::create([
            'wallet_type' => 'user',
            'wallet_id' => $toWallet->id,
            'owner_type' => 'user',
            'owner_id' => $toUser->id,
            'type' => LedgerEntry::TYPE_TRANSFER_IN,
            'amount' => $amount,
            'balance_before' => $toWallet->balance,
            'balance_after' => $toWallet->balance + $amount,
            'available_balance_before' => $toWallet->available_balance,
            'available_balance_after' => ($toWallet->available_balance + $amount),
            'description' => 'استلام تحويل من ' . ($fromUser->name ?? 'مستخدم آخر'),
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => $transferId . '-CR',
            'related_owner_type' => 'user',
            'related_owner_id' => $fromUser->id,
            'related_entry_id' => $debitTransaction->id,
            'metadata' => json_encode([
                'transfer_type' => 'peer_to_peer',
                'from_user_id' => $fromUser->id,
                'from_user_name' => $fromUser->name,
                'transfer_speed' => 'instant'
            ]),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'processed_at' => $transferDate,
            'created_at' => $transferDate,
            'updated_at' => $transferDate
        ]);

        // 3. Update debit transaction with related transaction ID
        $debitTransaction->update(['related_entry_id' => $creditTransaction->id]);

        // 4. Update wallet balances (only balance, available_balance is generated)
        $fromWallet->update([
            'balance' => $fromWallet->balance - $amount,
            'last_transaction_at' => $transferDate
        ]);

        $toWallet->update([
            'balance' => $toWallet->balance + $amount,
            'last_transaction_at' => $transferDate
        ]);
    }

    /**
     * إضافة معاملات متنوعة للمستخدمين
     */
    private function createUserMiscTransactions(User $user, UserWallet $wallet): void
    {
        $transactionTypes = [
            LedgerEntry::TYPE_PAYMENT,
            LedgerEntry::TYPE_REFUND,
            LedgerEntry::TYPE_FEE
        ];

        foreach ($transactionTypes as $type) {
            $count = rand(1, 2);
            for ($i = 0; $i < $count; $i++) {
                $this->createMiscTransaction($user, $wallet, $type);
            }
        }
    }

    /**
     * إضافة رسوم للسائقين
     */
    private function createDriverFees(Driver $driver, DriverWallet $wallet): void
    {
        $feeCount = rand(1, 2);
        for ($i = 0; $i < $feeCount; $i++) {
            $feeDate = $this->faker->dateTimeBetween('-2 months', 'now');
            $feeAmount = rand(10, 100);

            if ($wallet->balance >= $feeAmount) {
                $transaction = LedgerEntry::create([
                    'wallet_type' => 'driver',
                    'wallet_id' => $wallet->id,
                    'owner_type' => 'driver',
                    'owner_id' => $driver->id,
                    'type' => LedgerEntry::TYPE_FEE,
                    'amount' => $feeAmount,
                    'balance_before' => $wallet->balance,
                    'balance_after' => $wallet->balance - $feeAmount,
                    'available_balance_before' => $wallet->available_balance,
                    'available_balance_after' => ($wallet->available_balance - $feeAmount),
                    'description' => 'رسوم خدمة شهري',
                    'status' => LedgerEntry::STATUS_COMPLETED,
                    'reference' => 'FEE-' . now()->format('Ymd') . '-' . Str::random(6),
                    'metadata' => json_encode([
                        'fee_type' => 'subscription',
                        'period' => 'شهر',
                        'service' => 'منصة السائقين',
                        'tax_amount' => $feeAmount * 0.15
                    ]),
                    'ip_address' => $this->faker->ipv4,
                    'user_agent' => $this->faker->userAgent,
                    'processed_at' => $feeDate,
                    'created_at' => $feeDate,
                    'updated_at' => $feeDate
                ]);

                $wallet->update([
                    'balance' => $wallet->balance - $feeAmount,
                    'last_transaction_at' => $feeDate
                ]);
            }
        }
    }

    /**
     * إنشاء معاملة متنوعة
     */
    private function createMiscTransaction(User $user, UserWallet $wallet, string $type): void
    {
        $date = $this->faker->dateTimeBetween('-3 months', 'now');
        $amount = rand(10, 500);

        $descriptions = [
            LedgerEntry::TYPE_PAYMENT => 'دفع فاتورة خدمة',
            LedgerEntry::TYPE_REFUND => 'استرداد مبلغ',
            LedgerEntry::TYPE_FEE => 'رسوم خدمة'
        ];

        $transaction = LedgerEntry::create([
            'wallet_type' => 'user',
            'wallet_id' => $wallet->id,
            'owner_type' => 'user',
            'owner_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $wallet->balance,
            'balance_after' => $type === LedgerEntry::TYPE_REFUND ?
                $wallet->balance + $amount : $wallet->balance - $amount,
            'available_balance_before' => $wallet->available_balance,
            'available_balance_after' => $type === LedgerEntry::TYPE_REFUND ?
                ($wallet->available_balance + $amount) : ($wallet->available_balance - $amount),
            'description' => $descriptions[$type] ?? 'معاملة متنوعة',
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => substr($type, 0, 3) . '-' . now()->format('Ymd') . '-' . Str::random(6),
            'metadata' => json_encode([
                'service_type' => $this->getRandomServiceType(),
                'invoice_number' => 'INV-' . Str::random(8),
                'processed_by' => 'system'
            ]),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'processed_at' => $date,
            'created_at' => $date,
            'updated_at' => $date
        ]);

        if ($type === LedgerEntry::TYPE_REFUND) {
            $wallet->update([
                'balance' => $wallet->balance + $amount,
                'last_transaction_at' => $date
            ]);
        } else {
            $wallet->update([
                'balance' => $wallet->balance - $amount,
                'last_transaction_at' => $date
            ]);
        }
    }

    /**
     * توليد مبلغ الإيداع
     */
    private function generateDepositAmount(User $user): float
    {
        $userAgeInMonths = $user->created_at->diffInMonths(now());
        $baseAmount = $userAgeInMonths > 6 ? rand(500, 5000) : rand(100, 2000);

        return (float) $baseAmount;
    }

    /**
     * توليد مبلغ السحب
     */
    private function generateWithdrawalAmount(User $user, UserWallet $wallet, float $totalDeposits, float $totalWithdrawals): float
    {
        $availableForWithdrawal = min(
            $wallet->balance,
            $totalDeposits - $totalWithdrawals
        );

        if ($availableForWithdrawal <= 0) {
            return 0;
        }

        $maxWithdrawal = min($availableForWithdrawal * 0.8, 2000);
        return (float) rand(50, $maxWithdrawal);
    }

    /**
     * توليد مبلغ الأرباح
     */
    private function generateEarningAmount(Driver $driver): float
    {
        $driverAgeInMonths = $driver->created_at->diffInMonths(now());
        $baseAmount = $driverAgeInMonths > 3 ? rand(100, 1000) : rand(50, 300);

        return (float) $baseAmount;
    }

    /**
     * توليد مبلغ السحب للسائق
     */
    private function generateCashoutAmount(Driver $driver, DriverWallet $wallet, float $totalEarnings, float $totalCashouts): float
    {
        $availableForCashout = min(
            $wallet->balance,
            $totalEarnings - $totalCashouts
        );

        if ($availableForCashout <= 0) {
            return 0;
        }

        $maxCashout = min($availableForCashout * 0.9, 5000);
        return (float) rand(200, $maxCashout);
    }

    /**
     * الحصول على طريقة دفع عشوائية
     */
    private function getRandomPaymentMethod(): string
    {
        $methods = ['paymob', 'credit_card', 'vodafone_cash', 'apple_pay', 'stc_pay'];
        return $methods[array_rand($methods)];
    }

    /**
     * الحصول على وصف الإيداع
     */
    private function getDepositDescription(string $paymentMethod): string
    {
        $descriptions = [
            'paymob' => 'إيداع عبر Paymob',
            'credit_card' => 'إيداع ببطاقة ائتمان',
            'vodafone_cash' => 'إيداع عبر فودافون كاش',
            'apple_pay' => 'إيداع عبر Apple Pay',
            'stc_pay' => 'إيداع عبر STC Pay'
        ];

        return $descriptions[$paymentMethod] ?? 'إيداع رصيد';
    }

    /**
     * الحصول على اسم بنك عشوائي
     */
    private function getRandomBankName(): string
    {
        $banks = [
            'مصرف الراجحي',
            'البنك الأهلي التجاري',
            'بنك الرياض',
            'البنك السعودي الفرنسي',
            'بنك البلاد',
            'البنك العربي الوطني',
            'بنك الإنماء',
            'بنك الجزيرة'
        ];

        return $banks[array_rand($banks)];
    }

    /**
     * الحصول على سعر صرف
     */
    private function getExchangeRate(string $currency): float
    {
        $rates = [
            'SAR' => 1.0,
            'USD' => 3.75,
            'EGP' => 0.12,
            'AED' => 1.02
        ];

        return $rates[$currency] ?? 1.0;
    }

    /**
     * الحصول على حالة سحب عشوائية
     */
    private function getRandomCashoutStatus(): string
    {
        $statuses = [
            LedgerEntry::STATUS_COMPLETED,
            LedgerEntry::STATUS_PENDING,
            LedgerEntry::STATUS_APPROVED
        ];

        $rand = rand(1, 100);

        if ($rand <= 70) return LedgerEntry::STATUS_COMPLETED;
        if ($rand <= 90) return LedgerEntry::STATUS_PENDING;
        return LedgerEntry::STATUS_APPROVED;
    }

    /**
     * الحصول على نوع خدمة عشوائي
     */
    private function getRandomServiceType(): string
    {
        $services = [
            'subscription',
            'transaction_fee',
            'monthly_maintenance',
            'service_charge',
            'processing_fee'
        ];

        return $services[array_rand($services)];
    }
}
