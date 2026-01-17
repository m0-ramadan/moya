<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Driver;
use App\Models\Order;
use App\Models\OrderOffer;
use App\Models\DriverRating;
use App\Models\Service;
use App\Models\WaterType;
use App\Models\SavedLocation;
use App\Models\OrderStatus;
use Faker\Factory as Faker;

class OrdersAndRatingsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $users = User::all();
        $drivers = Driver::all();
        $services = Service::all();
        $waterTypes = WaterType::all();
        $locations = SavedLocation::all();
        $statuses = OrderStatus::all();

        foreach ($users as $user) {

            // اختيار سائق عشوائي
            $driver = $drivers->random();

            // إنشاء طلب
            $order = Order::create([
                'user_id' => $user->id,
                'driver_id' => $driver->id,
                'service_id' => $services->random()->id,
                'water_type_id' => $waterTypes->random()->id,
                'saved_location_id' => $locations->random()->id,
                'order_status_id' => $statuses->random()->id,
                'order_date' => $faker->dateTimeThisYear(),
                'contract_id' => null,
            ]);

            // إنشاء عرض للسائق
            OrderOffer::create([
                'order_id' => $order->id,
                'driver_id' => $driver->id,
                'price' =>  $faker->numberBetween(20, 200),
                'status' => 'accepted',
                'delivery_duration_minutes' => $faker->numberBetween(30, 120),
            ]);

            // إنشاء تقييم للسائق
            DriverRating::create([
                'driver_id' => $driver->id,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'rating' => $faker->randomFloat(2, 3, 5), // تقييم بين 3 و 5
                'comment' => $faker->sentence(),
            ]);
        }
    }
}
