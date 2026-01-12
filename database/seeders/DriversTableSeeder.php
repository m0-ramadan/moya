<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Database\Seeder;
use App\Models\Wallet\DriverWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DriversTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // حذف بترتيب معين
        DB::table('ledger_entries')->where('owner_type', 'driver')->delete();
        DB::table('idempotency_keys')->where('wallet_type', 'driver')->delete();
        DriverWallet::truncate();
        DB::table('driver_locations')->truncate();
        Driver::truncate();
        User::where('type', 'driver')->delete();

        // إعادة ضبط AUTO_INCREMENT
        DB::statement('ALTER TABLE driver_wallets AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE drivers AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'name' => 'سائق ' . $i,
                'password' => Hash::make('password123'),
                'type' => 'driver',
                'status' => 'active',
                'phone_number' => '05' . rand(10000000, 99999999), // بدون +
                'full_phone' => '+9665' . rand(10000000, 99999999), // مع +
            ]);

            $driver = Driver::create([
                'user_id' => $user->id,
                'full_name' => 'سائق ' . $i,
                // 'phone' => '+9665' . rand(10000000, 99999999),
                'id_number' => rand(1000000000, 1999999999),
                'license_number' => 'LIC-' . rand(100000, 999999),
                'is_active' => true,
                'national_id' => rand(100000000000, 1999999999999),
                'average_rating' => rand(35, 50) / 10,
                'total_ratings' => rand(10, 200),
                'total_orders' => rand(20, 500),
                'date_of_birth' => '1990-01-01'
            ]);

            // المحفظة ستُنشأ تلقائياً بواسطة حدث created في نموذج Driver
        }

        $this->command->info('✅ تم إنشاء 30 سائق مع محافظهم');
    }
}
