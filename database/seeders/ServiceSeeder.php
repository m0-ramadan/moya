<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'خدمة توصيل',
                'image' => '',
                'is_active' => 1,
            ],
            [
                'name' => 'خدمة صيانة',
                'image' => '',
                'is_active' => 1,
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
