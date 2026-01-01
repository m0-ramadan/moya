<?php

namespace App\Http\Controllers\Api;

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
     * إنشاء طلب جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id'        => 'required|exists:services,id',
            'water_type_id'     => 'nullable|exists:water_types,id',
            'saved_location_id' => 'required|exists:saved_locations,id',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id'           => auth()->id(),
                'service_id'        => $validated['service_id'],
                'water_type_id'     => $validated['water_type_id'] ?? null,
                'saved_location_id' => $validated['saved_location_id'],
                'order_status_id'   => 1, // pending
            ]);

            DB::commit();

            return $this->successResponse(
                new OrderResource(
                    $order->load([
                        'service',
                        'waterType',
                        'location',
                        'status',
                    ])
                ),
                'تم إنشاء الطلب بنجاح',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                'فشل إنشاء الطلب',
                500
            );
        }
    }

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
