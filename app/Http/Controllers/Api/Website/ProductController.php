<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Resources\Website\ColorResource;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Website\MaterialResource;
use App\Http\Resources\Website\ProductResource;
use App\Models\Color;

class ProductController extends Controller
{
    use ApiResponseTrait;

    /**
     * 🔹 عرض جميع المنتجات
     */
public function index(Request $request)
    {
        try {
            $query = Product::with([
                'category',
                'discount',
                'colors',
                'deliveryTime',
                'warranty',
                'features',
                'reviews',
                'sizes',
                'offers',
                'materials'
            ]);

            $products = $query
                ->filtered($request)
                ->searched($request->get('search'))
                ->sorted($request)
                ->paginate($request->get('per_page', 10));

            return $this->paginated(
                ProductResource::collection($products),
                'تم جلب المنتجات بنجاح'
            );
        } catch (\Throwable $e) {
            return $this->error('حدث خطأ أثناء جلب المنتجات', 500, [
                'exception' => $e->getMessage(),
            ]);
        }
    }


    /**
     * 🔹 عرض منتج واحد بالتفصيل
     */
    public function show($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->error('المنتج غير موجود', 404);
            }

            return $this->success(new ProductResource($product), 'تم جلب بيانات المنتج بنجاح');
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء جلب بيانات المنتج', 500, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
    /**
     * 🔹 جلب جميع الألوان المتاحة للمنتجات
     */
    public function getColor()
    {
        try {
            $colors = Color::get();
            return $this->success(ColorResource::collection($colors), 'تم جلب الألوان بنجاح');
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء جلب الألوان', 500, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
    /**
     * 🔹 جلب جميع المواد المتاحة للمنتجات
     */
    public function getMaterial()
    {
        try {
            $materials = Material::get();
            return $this->success(MaterialResource::collection($materials), 'تم جلب المواد بنجاح');
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء جلب المواد', 500, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
