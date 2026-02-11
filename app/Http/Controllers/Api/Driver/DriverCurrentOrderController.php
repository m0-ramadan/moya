<?php

namespace App\Http\Controllers\Api\Driver;

use App\Models\Order;
use App\Models\OrderOffer;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Models\DriverLocation;
use App\Traits\ApiResponseTrait;
use App\Services\GoogleMapsService;
use App\Http\Controllers\Controller;

class DriverCurrentOrderController extends Controller
{
    use ApiResponseTrait;

    protected $googleMapsService;

    public function __construct(GoogleMapsService $googleMapsService)
    {
        $this->googleMapsService = $googleMapsService;
        $this->middleware('auth:sanctum');
        $this->middleware('driver');
    }
    public function getActiveOrder(Request $request)
    {
        $driver = auth()->user()->driver;

        if (!$driver) {
            return $this->errorResponse('يجب أن تكون سائقاً', 403);
        }

        // حالات الطلب النشطة (عدل الأسماء حسب جدولك)
        $activeStatusIds = OrderStatus::whereIn('name', ['pendding', 'in-road'])
            ->pluck('id')
            ->toArray();

        /*
    |--------------------------------------------------------------------------
    | أولاً: نبحث عن Order نشط
    |--------------------------------------------------------------------------
    */
        $activeOrder = Order::with([
            'service:id,name',
            'waterType:id,name',
            'status:id,name',
            'user:id,name,phone',
            'location:id,address_details,latitude,longitude',
            'acceptedOffer:id,order_id,price,delivery_duration_minutes,status'
        ])
            ->where('driver_id', $driver->id)
            ->whereIn('order_status_id', $activeStatusIds)
            ->latest()
            ->first();

        if ($activeOrder) {
            return $this->successResponse([
                'type' => 'order',
                'order' => [
                    'id' => $activeOrder->id,
                    'service' => $activeOrder->service?->name,
                    'water_type' => $activeOrder->waterType?->name,
                    'status' => $activeOrder->status?->name,
                    'price' => $activeOrder->getPaymentAmount(),
                    'payment_status' => $activeOrder->payment_status,
                    'user' => [
                        'name' => $activeOrder->user?->name,
                        'phone' => $activeOrder->user?->phone,
                    ],
                    'location' => [
                        'address' => $activeOrder->location?->address_details,
                        'latitude' => $activeOrder->location?->latitude,
                        'longitude' => $activeOrder->location?->longitude,
                    ],
                    'offer' => $activeOrder->acceptedOffer ? [
                        'price' => $activeOrder->acceptedOffer->price,
                        'delivery_duration_minutes' => $activeOrder->acceptedOffer->delivery_duration_minutes,
                    ] : null,
                    'created_at' => $activeOrder->created_at->format('Y-m-d H:i:s'),
                ]
            ], 'تم جلب الطلب النشط');
        }

        /*
    |--------------------------------------------------------------------------
    | ثانياً: نبحث عن Offer نشط
    |--------------------------------------------------------------------------
    */

        $activeOffer = OrderOffer::with([
            'order.service:id,name',
            'order.waterType:id,name',
            'order.status:id,name',
        ])
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->whereHas('order', function ($q) use ($activeStatusIds) {
                $q->whereIn('order_status_id', $activeStatusIds);
            })
            ->latest()
            ->first();
        if ($activeOffer) {
            return $this->successResponse([
                'type' => 'offer',
                'offer' => [
                    'id' => $activeOffer->id,
                    'price' => $activeOffer->price,
                    'status' => $activeOffer->status,
                    'delivery_duration_minutes' => $activeOffer->delivery_duration_minutes,
                    'created_at' => $activeOffer->created_at->format('Y-m-d H:i:s'),
                ],
                'order' => [
                    'id' => $activeOffer->order?->id,
                    'service' => $activeOffer->order?->service?->name,
                    'water_type' => $activeOffer->order?->waterType?->name,
                    'status' => $activeOffer->order?->status?->name,
                    'user' => [
                        'name' => $activeOffer->order?->user?->name,
                        'phone' => $activeOffer->order?->user?->phone,
                    ],
                    'location' => [
                        'address' => $activeOffer->order?->location?->address_details,
                        'latitude' => $activeOffer->order?->location?->latitude,
                        'longitude' => $activeOffer->order?->location?->longitude,
                    ],
                ]
            ], 'يوجد عرض نشط حالياً');
        }

        /*
    |--------------------------------------------------------------------------
    | لا يوجد أي شيء
    |--------------------------------------------------------------------------
    */
        return $this->successResponse(
            null,
            'لا يوجد طلب أو عرض نشط حالياً'
        );
    }


    /**
     * تحديد الخطوات التالية للطلب
     */
    private function getNextStepsForOrder(Order $order)
    {
        $steps = [];
        $currentStatus = $order->order_status_id;

        switch ($currentStatus) {
            case 1: // معلق (مقبول ولكن بانتظار المستخدم)
                $steps[] = [
                    'action' => 'wait_for_user_confirmation',
                    'title' => 'بانتظار تأكيد المستخدم',
                    'description' => 'الطلب مقبول ولكن بانتظار تأكيد المستخدم',
                    'icon' => 'clock',
                    'color' => 'warning',
                ];
                break;

            case 2: // مقبول
                $steps[] = [
                    'action' => 'start_delivery',
                    'title' => 'بدء التوصيل',
                    'description' => 'ابدأ توصيل الطلب إلى المستخدم',
                    'icon' => 'play',
                    'color' => 'primary',
                    'api_endpoint' => '/api/v1/driver/orders/' . $order->id . '/update-status',
                    'api_method' => 'POST',
                    'api_body' => ['status_id' => 3],
                ];
                $steps[] = [
                    'action' => 'contact_user',
                    'title' => 'الاتصال بالمستخدم',
                    'description' => 'الاتصال لتأكيد العنوان أو الاستفسار',
                    'icon' => 'phone',
                    'color' => 'info',
                    'phone_number' => $order->user->phone_number,
                ];
                break;

            case 3: // جاري التوصيل
                $steps[] = [
                    'action' => 'update_location',
                    'title' => 'تحديث الموقع',
                    'description' => 'يتم تحديث موقعك تلقائياً للمستخدم',
                    'icon' => 'map-marker',
                    'color' => 'success',
                ];
                $steps[] = [
                    'action' => 'mark_delivered',
                    'title' => 'تسليم الطلب',
                    'description' => 'اضغط عند وصولك وتسليم الطلب',
                    'icon' => 'check-circle',
                    'color' => 'primary',
                    'api_endpoint' => '/api/v1/driver/orders/' . $order->id . '/update-status',
                    'api_method' => 'POST',
                    'api_body' => ['status_id' => 4],
                ];
                break;

            case 4: // تم التسليم
                $steps[] = [
                    'action' => 'order_completed',
                    'title' => 'تم التسليم',
                    'description' => 'تم إتمام الطلب بنجاح',
                    'icon' => 'check',
                    'color' => 'success',
                ];
                $steps[] = [
                    'action' => 'rate_user',
                    'title' => 'تقييم المستخدم',
                    'description' => 'قم بتقييم تجربة التوصيل',
                    'icon' => 'star',
                    'color' => 'warning',
                    'api_endpoint' => '/api/v1/orders/' . $order->id . '/rate-user',
                    'api_method' => 'POST',
                ];
                break;
        }

        // إضافة خطوات عامة
        $steps[] = [
            'action' => 'view_route',
            'title' => 'عرض المسار',
            'description' => 'عرض المسار إلى وجهة المستخدم',
            'icon' => 'directions',
            'color' => 'info',
            'api_endpoint' => '/api/v1/driver/orders/' . $order->id . '/path',
        ];

        $steps[] = [
            'action' => 'open_chat',
            'title' => 'محادثة مع المستخدم',
            'description' => 'الدردشة مع المستخدم لتنسيق التسليم',
            'icon' => 'message',
            'color' => 'secondary',
            'api_endpoint' => '/api/v1/chats/create',
            'api_method' => 'POST',
            'api_body' => ['order_id' => $order->id, 'user_id' => $order->user_id],
        ];

        return $steps;
    }

    /**
     * الحصول على لون الحالة
     */
    private function getStatusColor($statusId)
    {
        $colors = [
            1 => '#FFC107', // معلق - أصفر
            2 => '#17A2B8', // مقبول - أزرق
            3 => '#007BFF', // جاري التوصيل - أزرق داكن
            4 => '#28A745', // تم التسليم - أخضر
            5 => '#6C757D', // منتهي - رمادي
            6 => '#DC3545', // ملغي - أحمر
        ];

        return $colors[$statusId] ?? '#6C757D';
    }

    /**
     * الحصول على الإجراءات السريعة
     */
    private function getQuickActions(Order $order)
    {
        $actions = [
            [
                'id' => 'call_user',
                'title' => 'اتصال',
                'icon' => 'phone',
                'color' => 'success',
                'type' => 'phone',
                'value' => $order->user->phone,
                'available' => $order->order_status_id >= 2,
            ],
            [
                'id' => 'navigate',
                'title' => 'ملاحة',
                'icon' => 'navigation',
                'color' => 'primary',
                'type' => 'navigation',
                'value' => [
                    'latitude' => $order->location->latitude,
                    'longitude' => $order->location->longitude,
                    'address' => $order->location->address_details,
                ],
                'available' => $order->order_status_id >= 2,
            ],
            [
                'id' => 'update_status',
                'title' => 'تحديث الحالة',
                'icon' => 'refresh',
                'color' => 'info',
                'type' => 'api',
                'available' => true,
            ],
            [
                'id' => 'view_details',
                'title' => 'تفاصيل',
                'icon' => 'info',
                'color' => 'secondary',
                'type' => 'screen',
                'value' => 'order_details',
                'available' => true,
            ],
        ];

        return array_filter($actions, function ($action) {
            return $action['available'];
        });
    }

    /**
     * جلب آخر موقع للطلب الحالي
     */
    public function getLastLocation(Request $request)
    {
        $driver = auth()->user()->driver;

        if (!$driver) {
            return $this->errorResponse('يجب أن تكون سائقاً', 403);
        }

        $activeOrder = Order::where('driver_id', $driver->id)
            ->whereIn('order_status_id', [1, 2, 3, 4])
            ->first();

        if (!$activeOrder) {
            return $this->errorResponse('لا يوجد طلب نشط', 404);
        }

        $lastLocation = DriverLocation::where('order_id', $activeOrder->id)
            ->latest()
            ->first(['latitude', 'longitude', 'address', 'created_at']);

        return $this->successResponse([
            'order_id' => $activeOrder->id,
            'last_location' => $lastLocation,
            'is_tracking_active' => $lastLocation && $lastLocation->created_at->diffInMinutes(now()) < 5,
        ]);
    }
}
