<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    use ApiResponseTrait;

    /**
     * 🔹 عرض بيانات الصفحة الرئيسية
     */
    public function index(Request $request, $locale = 'ar')
    {
        try {
            // 🟢 جلب الأقسام النشطة فقط
            $categories = Category::where('status_id', 1)
                ->get();

            // 🟢 جلب 10 منتجات نشطة للعرض في الصفحة الرئيسية
            $products = Product::where('status_id', 1)
                ->take(10)
                ->get();

            // 📦 البيانات النهائية
            $data = [
                'categories' => $categories,
                'products'   => $products,
            ];

            return $this->success($data, 'تم جلب بيانات الصفحة الرئيسية بنجاح');
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء تحميل بيانات الصفحة الرئيسية', 500, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
