<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'name_urdu' => 'سعودی عرب', 'code' => 'SA', 'dial_code' => '+966', 'flag_emoji' => '🇸🇦', 'sort_order' => 1],
            ['name_ar' => 'مصر', 'name_en' => 'Egypt', 'name_urdu' => 'مصر', 'code' => 'EG', 'dial_code' => '+20', 'flag_emoji' => '🇪🇬', 'sort_order' => 2],
            ['name_ar' => 'الأردن', 'name_en' => 'Jordan', 'name_urdu' => 'اردن', 'code' => 'JO', 'dial_code' => '+962', 'flag_emoji' => '🇯🇴', 'sort_order' => 3],
            ['name_ar' => 'فلسطين', 'name_en' => 'Palestine', 'name_urdu' => 'فلسطین', 'code' => 'PS', 'dial_code' => '+970', 'flag_emoji' => '🇵🇸', 'sort_order' => 4],
            ['name_ar' => 'سوريا', 'name_en' => 'Syria', 'name_urdu' => 'شام', 'code' => 'SY', 'dial_code' => '+963', 'flag_emoji' => '🇸🇾', 'sort_order' => 5],
            ['name_ar' => 'لبنان', 'name_en' => 'Lebanon', 'name_urdu' => 'لبنان', 'code' => 'LB', 'dial_code' => '+961', 'flag_emoji' => '🇱🇧', 'sort_order' => 6],
            ['name_ar' => 'اليمن', 'name_en' => 'Yemen', 'name_urdu' => 'یمن', 'code' => 'YE', 'dial_code' => '+967', 'flag_emoji' => '🇾🇪', 'sort_order' => 7],
            ['name_ar' => 'العراق', 'name_en' => 'Iraq', 'name_urdu' => 'عراق', 'code' => 'IQ', 'dial_code' => '+964', 'flag_emoji' => '🇮🇶', 'sort_order' => 8],
            ['name_ar' => 'السودان', 'name_en' => 'Sudan', 'name_urdu' => 'سوڈان', 'code' => 'SD', 'dial_code' => '+249', 'flag_emoji' => '🇸🇩', 'sort_order' => 9],
            ['name_ar' => 'المغرب', 'name_en' => 'Morocco', 'name_urdu' => 'مراکش', 'code' => 'MA', 'dial_code' => '+212', 'flag_emoji' => '🇲🇦', 'sort_order' => 10],
            ['name_ar' => 'تونس', 'name_en' => 'Tunisia', 'name_urdu' => 'تونس', 'code' => 'TN', 'dial_code' => '+216', 'flag_emoji' => '🇹🇳', 'sort_order' => 11],
            ['name_ar' => 'الجزائر', 'name_en' => 'Algeria', 'name_urdu' => 'الجزائر', 'code' => 'DZ', 'dial_code' => '+213', 'flag_emoji' => '🇩🇿', 'sort_order' => 12],
            ['name_ar' => 'ليبيا', 'name_en' => 'Libya', 'name_urdu' => 'لیبیا', 'code' => 'LY', 'dial_code' => '+218', 'flag_emoji' => '🇱🇾', 'sort_order' => 13],
            ['name_ar' => 'قطر', 'name_en' => 'Qatar', 'name_urdu' => 'قطر', 'code' => 'QA', 'dial_code' => '+974', 'flag_emoji' => '🇶🇦', 'sort_order' => 14],
            ['name_ar' => 'الإمارات', 'name_en' => 'UAE', 'name_urdu' => 'متحدہ عرب امارات', 'code' => 'AE', 'dial_code' => '+971', 'flag_emoji' => '🇦🇪', 'sort_order' => 15],
            ['name_ar' => 'الكويت', 'name_en' => 'Kuwait', 'name_urdu' => 'کویت', 'code' => 'KW', 'dial_code' => '+965', 'flag_emoji' => '🇰🇼', 'sort_order' => 16],
            ['name_ar' => 'البحرين', 'name_en' => 'Bahrain', 'name_urdu' => 'بحرین', 'code' => 'BH', 'dial_code' => '+973', 'flag_emoji' => '🇧🇭', 'sort_order' => 17],
            ['name_ar' => 'عمان', 'name_en' => 'Oman', 'name_urdu' => 'عمان', 'code' => 'OM', 'dial_code' => '+968', 'flag_emoji' => '🇴🇲', 'sort_order' => 18],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
