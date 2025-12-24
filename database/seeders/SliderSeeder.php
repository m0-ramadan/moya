<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'title' => 'خدمة توصيل',
                'image' => '',
                'is_active' => 1,
            ],
            [
                'title' => 'خدمة صيانة',
                'image' => '',
                'is_active' => 1,
            ]
        ];

        foreach ($services as $service) {
            Slider::create($service);
        }
    }
}
