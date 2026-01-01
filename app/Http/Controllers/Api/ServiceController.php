<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use App\Models\WaterType;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteUser\ServiceResource;
use App\Http\Resources\WebsiteUser\WaterTypeResource;

class ServiceController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $services = Service::where('is_active', 1)->get();

        return $this->successResponse(
            ServiceResource::collection($services),
            'تم جلب الخدمات بنجاح'
        );
    }
    public function typeWater()
    {
        $types = WaterType::all();

        return $this->successResponse(
            WaterTypeResource::collection($types),
            'تم جلب أنواع المياه بنجاح'
        );
    }
}
