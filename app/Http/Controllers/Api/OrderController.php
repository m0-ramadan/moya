<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteUser\OrderResource;


class OrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * كل طلبات المستخدم
     */
    public function index()
    {
        $orders = Order::with([
            'service',
            'waterType',
            'location',
            'status',
            'driver',
            'acceptedOffer',
        ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return $this->successResponse(
            OrderResource::collection($orders),
            'تم جلب الطلبات بنجاح'
        );
    }

    /**
     * تفاصيل طلب واحد
     */
    public function show($id)
    {
        $order = Order::with([
            'service',
            'waterType',
            'location',
            'status',
            'driver',
            'acceptedOffer',
        ])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return $this->successResponse(
            new OrderResource($order),
            'تم جلب تفاصيل الطلب'
        );
    }
}
