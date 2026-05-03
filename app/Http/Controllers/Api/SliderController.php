<?php

namespace App\Http\Controllers\Api;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppUser\SliderResource;

class SliderController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get sliders based on authenticated user type (driver/user).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $user = auth('sanctum')->user();
            
            if (!$user) {
                return $this->errorResponse('المستخدم غير مصرح له', 401);
            }

            // جلب السلايدرات حسب نوع المستخدم
            $sliders = Slider::where('type', $user->type)
                ->where('is_active', 1)
                ->orderBy('order', 'asc')
                ->get();

            // التحقق من وجود سلايدرات
            if ($sliders->isEmpty()) {
                return $this->successResponse(
                    [],
                    'لا توجد سلايدرات متاحة حالياً'
                );
            }

            return $this->successResponse(
                SliderResource::collection($sliders),
                'تم جلب السلايدرات بنجاح'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'حدث خطأ أثناء جلب السلايدرات: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get banners only (for marketing or special purposes).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function banners()
    {
        try {
            $banners = Slider::where('is_active', 1)
                ->whereIn('type', ['banner', 'user', 'driver'])
                ->orderBy('order', 'asc')
                ->get();

            if ($banners->isEmpty()) {
                return $this->successResponse(
                    [],
                    'لا توجد بانرات متاحة حالياً'
                );
            }

            return $this->successResponse(
                SliderResource::collection($banners),
                'تم جلب البانرات بنجاح'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'حدث خطأ أثناء جلب البانرات: ' . $e->getMessage(),
                500
            );
        }
    }
}