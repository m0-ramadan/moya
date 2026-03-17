<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'عبدالله',
                'city' => 'الرياض',
                'rating' => 5,
                'review' => 'تجربة ممتازة جدًا، الطلب وصل بسرعة والتغليف كان مرتب، أكيد مو آخر تعامل.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'نورة',
                'city' => 'جدة',
                'rating' => 5,
                'review' => 'جودة المنتجات عالية جدًا، والموقع سهل في الاستخدام، أنصح فيه بقوة.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'فهد',
                'city' => 'الدمام',
                'rating' => 5,
                'review' => 'التوصيل سريع وخدمة العملاء ممتازة، تم الرد على استفساري خلال دقائق.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'سارة',
                'city' => 'الخبر',
                'rating' => 5,
                'review' => 'طلبت أكثر من مرة ونفس الجودة كل مرة، فعلاً متجر يعتمد عليه.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'تركي',
                'city' => 'مكة',
                'rating' => 4,
                'review' => 'التجربة بشكل عام ممتازة، لكن أتمنى خيارات توصيل أسرع.',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}