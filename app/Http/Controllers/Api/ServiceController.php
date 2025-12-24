<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppUser\ServiceResource;

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
}
