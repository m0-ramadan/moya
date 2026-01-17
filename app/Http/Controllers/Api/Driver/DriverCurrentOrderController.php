<?php

namespace App\Http\Controllers\Api\Driver;

use App\Models\Order;
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

        // جلب الطلب النشط للسائق
        $activeOrder = Order::with([
            'service',
            'waterType',
            'location',
            'status',
            'user',
            'acceptedOffer',
            'driverLocations' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'driver' => function ($query) {
                $query->select('id', 'user_id', 'full_name', 'photo', 'total_orders', 'average_rating');
            }
        ])
            ->where('driver_id', $driver->id)
            ->whereIn('order_status_id', [1, 2, 3, 4]) // الطلبات النشطة
            ->orderBy('created_at', 'desc')
            ->first();


        if (!$activeOrder) {
            return $this->successResponse(
                null,
                'لا يوجد طلب نشط حالياً',
                200
            );
        }

        // حساب معلومات إضافية
        $currentLocation = $activeOrder->driverLocations->first();
        $distanceInfo = null;
        $estimatedArrival = null;
        $nextSteps = [];

        if ($currentLocation && $activeOrder->location) {
            $distanceInfo = $this->googleMapsService->calculateDistanceAndTime(
                $currentLocation->latitude,
                $currentLocation->longitude,
                $activeOrder->location->latitude,
                $activeOrder->location->longitude
            );

            if ($distanceInfo) {
                $estimatedArrival = now()->addSeconds($distanceInfo['duration']['value']);
            }
        }

        // تحديد الخطوات التالية بناءً على حالة الطلب
        $nextSteps = $this->getNextStepsForOrder($activeOrder);

        // إضافة إحصائيات السائق
        $driverStats = [
            'total_orders' => $driver->total_orders,
            'average_rating' => $driver->average_rating,
            'total_earnings' => $driver->total_earnings ?? 0,
            'completed_today' => $driver->orders()
                ->where('order_status_id', 4)
                ->whereDate('updated_at', today())
                ->count(),
        ];

        // تجميع البيانات للاستجابة
        $orderData = [
            'order' => [
                'id' => $activeOrder->id,
                'order_number' => $activeOrder->order_number,
                'service' => $activeOrder->service?->name,
                'water_type' => $activeOrder->waterType?->name,
                'status' => [
                    'id' => $activeOrder->order_status_id,
                    'name' => $activeOrder->status->name,
                    'color' => $this->getStatusColor($activeOrder->order_status_id),
                ],
                'user' => [
                    'id' => $activeOrder->user->id,
                    'name' => $activeOrder->user->name,
                    'phone' => $activeOrder->user->phone,
                    'profile_picture' => $activeOrder->user->profile_picture,
                    'rating' => $activeOrder->user->rating ?? 0,
                    'total_orders' => $activeOrder->user->total_orders ?? 0,
                ],
                'location' => [
                    'address' => $activeOrder->location->address_details,
                    'latitude' => $activeOrder->location->latitude,
                    'longitude' => $activeOrder->location->longitude,
                    'building_number' => $activeOrder->location->building_number,
                    'apartment_number' => $activeOrder->location->apartment_number,
                    'floor' => $activeOrder->location->floor,
                    'notes' => $activeOrder->location->notes,
                ],
                'price' => [
                    'amount' => $activeOrder->price,
                    'formatted' => number_format($activeOrder->price) . ' ريال',
                    'is_paid' => $activeOrder->is_paid,
                    'payment_method' => $activeOrder->payment_method,
                ],
                'dates' => [
                    'created_at' => $activeOrder->created_at->format('Y-m-d H:i:s'),
                    'created_at_human' => $activeOrder->created_at->diffForHumans(),
                    'order_date' => $activeOrder->order_date ? $activeOrder->order_date->format('Y-m-d H:i:s') : null,
                    'order_date_human' => $activeOrder->order_date ? $activeOrder->order_date->diffForHumans() : null,
                ],
                'notes' => $activeOrder->notes,
                'offer' => $activeOrder->acceptedOffer ? [
                    'id' => $activeOrder->acceptedOffer->id,
                    'price' => $activeOrder->acceptedOffer->price,
                    'delivery_duration_minutes' => $activeOrder->acceptedOffer->delivery_duration_minutes,
                    'created_at' => $activeOrder->acceptedOffer->created_at->format('H:i'),
                ] : null,
                'tracking' => [
                    'has_live_tracking' => $activeOrder->order_status_id >= 2,
                    'last_location' => $currentLocation ? [
                        'latitude' => $currentLocation->latitude,
                        'longitude' => $currentLocation->longitude,
                        'address' => $currentLocation->address,
                        'speed' => $currentLocation->speed,
                        'heading' => $currentLocation->heading,
                        'is_moving' => $currentLocation->is_moving,
                        'updated_at' => $currentLocation->created_at->format('H:i:s'),
                        'updated_at_human' => $currentLocation->created_at->diffForHumans(),
                    ] : null,
                    'distance_info' => $distanceInfo ? [
                        'distance' => $distanceInfo['distance']['text'],
                        'distance_meters' => $distanceInfo['distance']['value'],
                        'duration' => $distanceInfo['duration']['text'],
                        'duration_seconds' => $distanceInfo['duration']['value'],
                    ] : null,
                    'estimated_arrival' => $estimatedArrival ? [
                        'time' => $estimatedArrival->format('H:i'),
                        'human' => $estimatedArrival->diffForHumans(),
                    ] : null,
                ],
                'actions' => $nextSteps,
                'can_update_location' => in_array($activeOrder->order_status_id, [2, 3]), // مقبول أو جاري التوصيل
                'can_update_status' => true,
                'can_contact_user' => true,
                'has_chat' => $activeOrder->order_status_id >= 2,
            ],
            'driver_stats' => $driverStats,
            'quick_actions' => $this->getQuickActions($activeOrder),
        ];

        return $this->successResponse(
            $orderData,
            'تم جلب الطلب الحالي بنجاح'
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
                    'phone_number' => $order->user->phone,
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
