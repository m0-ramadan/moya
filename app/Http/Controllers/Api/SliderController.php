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
    public function index()
    {
        $slider = Slider::where('is_active', 1)->get();
        return $this->successResponse(
            SliderResource::collection($slider),
            'تم جلب الصور المتحركة بنجاح'
        );
    }

    public function banners()
    {
        $banners = Slider::where('is_active', 1)
            ->where('type', 'banner')
            ->get();

        return $this->successResponse(
            SliderResource::collection($banners),
            'تم جلب البانرات بنجاح'
        );
    }
}
