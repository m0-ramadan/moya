<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ArticleCategory;

class ArticleCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'نصائح وتوجيهات',
                'slug' => 'tips-and-guidance',
                'description' => 'نصائح وتوجيهات لتوصيل المياه بشكل آمن وفعال',
                'icon' => 'fas fa-lightbulb',
                'color' => '#3498db',
                'order' => 1,
                'is_active' => true,
                'show_in_menu' => true
            ],
            [
                'name' => 'أخبار وتحديثات',
                'slug' => 'news-and-updates',
                'description' => 'آخر الأخبار والتحديثات في مجال توصيل المياه',
                'icon' => 'fas fa-newspaper',
                'color' => '#e74c3c',
                'order' => 2,
                'is_active' => true,
                'show_in_menu' => true
            ],
            [
                'name' => 'سلامة المركبات',
                'slug' => 'vehicle-safety',
                'description' => 'نصائح للحفاظ على سلامة مركبات التوصيل',
                'icon' => 'fas fa-car',
                'color' => '#2ecc71',
                'order' => 3,
                'is_active' => true,
                'show_in_menu' => true
            ],
            [
                'name' => 'إدارة الأعمال',
                'slug' => 'business-management',
                'description' => 'نصائح لإدارة أعمال توصيل المياه',
                'icon' => 'fas fa-chart-line',
                'color' => '#f39c12',
                'order' => 4,
                'is_active' => true,
                'show_in_menu' => true
            ],
            [
                'name' => 'القوانين واللوائح',
                'slug' => 'laws-and-regulations',
                'description' => 'القوانين واللوائح المنظمة لتوصيل المياه',
                'icon' => 'fas fa-gavel',
                'color' => '#9b59b6',
                'order' => 5,
                'is_active' => true,
                'show_in_menu' => true
            ]
        ];

        foreach ($categories as $category) {
            ArticleCategory::create($category);
        }

        $this->command->info('تم إنشاء الأقسام الرئيسية بنجاح!');
    }
}
