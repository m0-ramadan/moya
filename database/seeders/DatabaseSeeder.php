<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ColorSeeder;
use Database\Seeders\OfferSeeder;
use Database\Seeders\BranchSeeder;
use Database\Seeders\ReviewSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\SocialMediaSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\ImportantLinksSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
 $this->call([

           UserSeeder::class,

           SocialMediaSeeder::class,

        ]);
    }
}
