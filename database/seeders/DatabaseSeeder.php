<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ColorSeeder;
use Database\Seeders\OfferSeeder;
use Database\Seeders\BranchSeeder;
use Database\Seeders\ReviewSeeder;
use Database\Seeders\SliderSeeder;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet\DriverWallet;
use Database\Seeders\ArticleSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\SocialMediaSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\ImportantLinksSeeder;
use Database\Seeders\ArticleCategorySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

            //   UserSeeder::class,

            // SocialMediaSeeder::class,
            // ServiceSeeder::class,
            //   SliderSeeder::class,

            // ArticleCategorySeeder::class,
            // ArticleSeeder::class,
        ]);
        $this->command->info('🧹 تنظيف بيانات المعاملات القديمة...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // حذف المعاملات القديمة فقط
        DB::table('ledger_entries')->truncate();
        DB::table('idempotency_keys')->truncate();

        // تأكد من عدم وجود محافظ مكررة
        $this->cleanDuplicateWallets();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // إعادة تعيين أرصدة المحافظ
        $this->resetWalletBalances();

        // تشغيل Seeder الأساسي أولاً
        $this->call([
            DriversTableSeeder::class,
        ]);

        // تشغيل Seeder الخاص بالمعاملات
        $this->call(WalletTransactionsSeeder::class);

        $this->command->info('🎉 تم الانتهاء من جميع عمليات الـ Seeding بنجاح!');
    }
    private function cleanDuplicateWallets(): void
    {
        // إيجاد وحذف المحافظ المكررة
        $duplicates = DB::table('driver_wallets')
            ->select('driver_id', DB::raw('COUNT(*) as count'))
            ->groupBy('driver_id')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $ids = DB::table('driver_wallets')
                ->where('driver_id', $duplicate->driver_id)
                ->orderBy('created_at', 'desc')
                ->skip(1) // احتفظ بأحدث محفظة فقط
                ->pluck('id');

            if ($ids->count() > 0) {
                DB::table('driver_wallets')->whereIn('id', $ids)->delete();
            }
        }
    }

    protected function resetWalletBalances()
    {
        DriverWallet::query()->update([
            'balance' => 0,
            'held_balance' => 0,
            'total_earnings_today' => 0,
            'total_withdrawals_today' => 0,
            'total_cashouts_today' => 0,
            'last_transaction_at' => null
        ]);
    }
}
