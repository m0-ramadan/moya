<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Carbon\Carbon;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        // الحصول على أول مستخدم ككاتب
        $author = User::first();

        if (!$author) {
            $author = User::factory()->create([
                'name' => 'مدير النظام',
                'email' => 'admin@waterdelivery.com'
            ]);
        }

        // الحصول على الأقسام
        $categories = ArticleCategory::all();

        $articles = [
            [
                'title' => 'كيفية اختيار أفضل سائق لتوصيل المياه بمركبات 5 طن',
                'slug' => 'how-to-choose-best-driver-for-5-ton-water-delivery',
                'excerpt' => 'دليل شامل لاختيار السائق المناسب لتوصيل المياه بمركبات 5 طن مع ضمان الجودة والأمان',
                'content' => '<h2>مقدمة</h2>
                <p>توصيل المياه بمركبات 5 طن يتطلب سائقين محترفين يمتلكون الخبرة والمهارات اللازمة...</p>
                <h2>المعايير الأساسية لاختيار السائق</h2>
                <ul>
                    <li>الخبرة في قيادة مركبات 5 طن</li>
                    <li>المعرفة بأنظمة السلامة</li>
                    <li>القدرة على التعامل مع العملاء</li>
                </ul>',
                'summary' => 'مقال يقدم نصائح عملية لاختيار السائق المناسب لتوصيل المياه',
                'reading_time' => '5 دقائق',
                'status' => 'published',
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(2),
                'author_id' => $author->id,
                'category_id' => $categories->where('slug', 'tips-and-guidance')->first()->id,
                'meta_description' => 'دليل شامل لاختيار السائق المناسب لتوصيل المياه بمركبات 5 طن',
                'tags' => ['سائقين', 'توصيل مياه', '5 طن', 'نصائح'],
                'featured_image' => 'articles/drivers-guide.jpg'
            ],
            [
                'title' => 'أسعار توصيل المياه: كيفية تحديد السعر المناسب',
                'slug' => 'water-delivery-prices-how-to-set-right-price',
                'excerpt' => 'دليل لتحديد الأسعار المنافسة لتوصيل المياه بناءً على التكلفة والمسافة والعوامل الأخرى',
                'content' => '<h2>العوامل المؤثرة على السعر</h2>
                <p>تحديد سعر توصيل المياه يتطلب النظر في عدة عوامل...</p>',
                'summary' => 'تحليل للعوامل المؤثرة على أسعار توصيل المياه',
                'reading_time' => '7 دقائق',
                'status' => 'published',
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(5),
                'author_id' => $author->id,
                'category_id' => $categories->where('slug', 'business-management')->first()->id,
                'meta_description' => 'كيفية تحديد الأسعار المناسبة لتوصيل المياه بناءً على العوامل المختلفة',
                'tags' => ['أسعار', 'تكاليف', 'تسعير', 'منافسة'],
                'featured_image' => 'articles/pricing-guide.jpg'
            ],
            [
                'title' => 'صيانة مركبات توصيل المياه: دليل المبتدئين',
                'slug' => 'water-delivery-vehicles-maintenance-beginners-guide',
                'excerpt' => 'كل ما تحتاج معرفته عن صيانة مركبات 5 طن الخاصة بتوصيل المياه',
                'content' => '<h2>الصيانة الدورية</h2>
                <p>صيانة المركبات ضرورية لضمان استمرارية العمل...</p>',
                'reading_time' => '8 دقائق',
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(10),
                'author_id' => $author->id,
                'category_id' => $categories->where('slug', 'vehicle-safety')->first()->id,
                'tags' => ['صيانة', 'مركبات', '5 طن', 'سلامة'],
                'featured_image' => 'articles/maintenance.jpg'
            ]
        ];

        foreach ($articles as $articleData) {
            Article::create($articleData);
        }

        $this->command->info('تم إنشاء المقالات التجريبية بنجاح!');
    }
}
