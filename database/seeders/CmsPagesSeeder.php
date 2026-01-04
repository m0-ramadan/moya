<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\SectionContent;

class CmsPagesSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | HOME PAGE
        |--------------------------------------------------------------------------
        */
        $home = Page::create([
            'key' => 'home',
            'title' => 'Home Page',
        ]);

        /* HERO */
        $hero = PageSection::create([
            'page_id' => $home->id,
            'type' => 'hero',
            'order' => 1,
        ]);

        SectionContent::insert([
            [
                'section_id' => $hero->id,
                'key' => 'title',
                'value' => 'اسرع خدمة توصيل في المملكة',
            ],
            [
                'section_id' => $hero->id,
                'key' => 'subtitle',
                'value' => 'حدد الكمية والموقع واستلم عروض الأسعار من السواقين فورًا',
            ],
            [
                'section_id' => $hero->id,
                'key' => 'image',
                'value' => 'hero-truck.png',
            ],
        ]);

        /* WHY US */
        $features = PageSection::create([
            'page_id' => $home->id,
            'type' => 'features',
            'order' => 2,
        ]);

        $featuresData = [
            ['icon' => 'price', 'title' => 'أسعار تنافسية', 'description' => 'أفضل الأسعار من السواقين'],
            ['icon' => 'fast', 'title' => 'توصيل سريع', 'description' => 'وصول خلال 30-60 دقيقة'],
            ['icon' => 'safe', 'title' => 'سواقين موثوقين', 'description' => 'تم التحقق من جميع السواقين'],
            ['icon' => 'support', 'title' => 'دعم فني', 'description' => 'دعم على مدار الساعة'],
        ];

        foreach ($featuresData as $i => $item) {
            SectionContent::create([
                'section_id' => $features->id,
                'key' => 'item',
                'value' => $item,
                'order' => $i + 1,
            ]);
        }

        /* PACKAGES */
        $packages = PageSection::create([
            'page_id' => $home->id,
            'type' => 'packages',
            'order' => 3,
        ]);

        $packagesData = [
            ['name' => '6 طن', 'price' => 240, 'is_featured' => false],
            ['name' => '6 طن', 'price' => 240, 'is_featured' => true],
            ['name' => '6 طن', 'price' => 240, 'is_featured' => false],
        ];

        foreach ($packagesData as $i => $pkg) {
            SectionContent::create([
                'section_id' => $packages->id,
                'key' => 'package',
                'value' => $pkg,
                'order' => $i + 1,
            ]);
        }

        /* HOW IT WORKS */
        $steps = PageSection::create([
            'page_id' => $home->id,
            'type' => 'steps',
            'order' => 4,
        ]);

        $stepsData = [
            ['icon' => 'location', 'title' => 'حدد موقعك', 'description' => 'اختر موقع التوصيل'],
            ['icon' => 'truck', 'title' => 'اختر السائق', 'description' => 'استلم عروض متعددة'],
            ['icon' => 'pay', 'title' => 'ادفع بأمان', 'description' => 'وسائل دفع متعددة'],
            ['icon' => 'done', 'title' => 'تم التوصيل', 'description' => 'استلم طلبك بسهولة'],
        ];

        foreach ($stepsData as $i => $step) {
            SectionContent::create([
                'section_id' => $steps->id,
                'key' => 'step',
                'value' => $step,
                'order' => $i + 1,
            ]);
        }

        /* TESTIMONIALS */
        $testimonials = PageSection::create([
            'page_id' => $home->id,
            'type' => 'testimonials',
            'order' => 5,
        ]);

        foreach (['أبو عبدالله', 'أبو عبدالله', 'أبو عبدالله'] as $i => $name) {
            SectionContent::create([
                'section_id' => $testimonials->id,
                'key' => 'review',
                'value' => [
                    'name' => $name,
                    'rating' => 5,
                    'comment' => 'خدمة ممتازة وسريعة',
                ],
                'order' => $i + 1,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | DRIVERS PAGE
        |--------------------------------------------------------------------------
        */
        $drivers = Page::create([
            'key' => 'drivers',
            'title' => 'Drivers Page',
        ]);

        /* HERO */
        $heroDrivers = PageSection::create([
            'page_id' => $drivers->id,
            'type' => 'hero',
            'order' => 1,
        ]);

        SectionContent::insert([
            [
                'section_id' => $heroDrivers->id,
                'key' => 'title',
                'value' => 'انضم إلى سواقينا',
            ],
            [
                'section_id' => $heroDrivers->id,
                'key' => 'description',
                'value' => 'اربح دخل إضافي مع أفضل منصة توصيل مياه',
            ],
        ]);

        /* STATISTICS */
        $stats = PageSection::create([
            'page_id' => $drivers->id,
            'type' => 'statistics',
            'order' => 2,
        ]);

        $statsData = [
            ['label' => 'رضا المستخدم', 'value' => '98%'],
            ['label' => 'طلبات شهرية', 'value' => '+15K'],
            ['label' => 'سائق نشط', 'value' => '500+'],
        ];

        foreach ($statsData as $i => $stat) {
            SectionContent::create([
                'section_id' => $stats->id,
                'key' => 'stat',
                'value' => $stat,
                'order' => $i + 1,
            ]);
        }

        /* DRIVER BENEFITS */
        $benefits = PageSection::create([
            'page_id' => $drivers->id,
            'type' => 'benefits',
            'order' => 3,
        ]);

        $benefitsData = [
            ['title' => 'أرباح يومية', 'description' => 'سحب أرباحك بسهولة'],
            ['title' => 'طلبات مستمرة', 'description' => 'طلبات على مدار اليوم'],
            ['title' => 'دعم فني', 'description' => 'دعم 24/7'],
        ];

        foreach ($benefitsData as $i => $benefit) {
            SectionContent::create([
                'section_id' => $benefits->id,
                'key' => 'benefit',
                'value' => $benefit,
                'order' => $i + 1,
            ]);
        }
    }
}
