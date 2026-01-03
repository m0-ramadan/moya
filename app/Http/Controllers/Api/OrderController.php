<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderStatus;
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
            'water_type_id'     => 'nullable',
            'order_date'        => 'nullable',
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
                'order_date'        => $validated['order_date'] ?? now(),
            ]);
            DB::commit();

            return $this->successResponse(
                new OrderResource($order),
                'تم إنشاء الطلب بنجاح',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * كل طلبات المستخدم
     */
    public function index(Request $request)
    {
        $query = Order::with([
            'service',
            'waterType',
            'location',
            'status',
            'driver',
            'acceptedOffer',
        ])->where('user_id', auth()->id());

        // 🔍 فلترة حسب الحالة
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // 📄 Pagination
        $perPage = $request->get('per_page', 10);

        $orders = $query
            ->latest()
            ->paginate($perPage);

        return $this->paginated(
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

    /**
     * جلب حالات الطلبات
     */
    public function statuses()
    {
        $statuses = OrderStatus::all();

        return $this->successResponse(
            $statuses,
            'تم جلب حالات الطلبات بنجاح'
        );
    }
}
