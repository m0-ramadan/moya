<?php

namespace App\Http\Controllers\Api\Driver;

use App\Events\DriverLocationUpdated;
use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Resources\Driver\OrderResource;
use App\Http\Resources\Driver\OrderResource as DriverOrderResource;
use App\Jobs\RequestRatingJob;
use App\Models\DriverLocation;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\GoogleMapsService;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DriverOrderController extends Controller
{
    use ApiResponseTrait;

    protected $googleMapsService;

    public function __construct(GoogleMapsService $googleMapsService)
    {
        $this->googleMapsService = $googleMapsService;
        $this->middleware('auth:sanctum');
        $this->middleware('driver');
    }

    /**
     * جلب الطلبات المنتظرة المتاحة للسائق
     */
    public function getPendingOrders(Request $request)
    {
        $driver = auth()->user()->driver;

        if (! $driver) {
            return $this->errorResponse('يجب أن تكون سائقاً', 403);
        }

        // التحقق من أن السائق ليس لديه طلبات نشطة
        $activeOrders = Order::where('driver_id', $driver->id)
            ->whereIn('order_status_id', [1, 2])
            ->exists();

        if ($activeOrders) {
            return $this->errorResponse('لديك طلب نشط بالفعل. يجب إنهاء الطلب الحالي أولاً.', 400);
        }

        // فلترة الطلبات المنتظرة المتاحة
        $query = Order::with([
            'service',
            'waterType',
            'location',
            'status',
            'user',
            'offers' => function ($query) use ($driver) {
                $query->where('driver_id', $driver->id);
            },
        ])
            ->where('order_status_id', 1)->whereNull('order_date')
            ->whereNull('driver_id') // ليس له سائق بعد
            ->whereDoesntHave('offers', function ($query) use ($driver) {
                $query->where('driver_id', $driver->id)
                    ->whereIn('status', ['pending', 'accepted']);
            });
        // فلترة حسب الموقع (اختياري)
        if ($request->has(['latitude', 'longitude'])) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            // الطلبات في نطاق 50 كيلومتر (يمكن تعديل المسافة)
            $query->whereHas('location', function ($q) use ($latitude, $longitude) {
                $q->whereRaw('
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * 
                        cos(radians(longitude) - radians(?)) + 
                        sin(radians(?)) * sin(radians(latitude))
                    )) <= ?
                ', [$latitude, $longitude, $latitude, 50]); // 50 كم
            });
        }

        // فلترة حسب نوع الخدمة
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // فلترة حسب نوع المياه
        if ($request->filled('water_type_id')) {
            $query->where('water_type_id', $request->water_type_id);
        }

        // استبعاد الطلبات المنتهية الصلاحية
        $expirationMinutes = config('services.orders.expiration_minutes', 5);
        $expiryTime = Carbon::now()->subMinutes($expirationMinutes);

        $query->where('created_at', '>=', $expiryTime);

        // إضافة معلومات المسافة إذا كانت هناك إحداثيات
        if ($request->has(['latitude', 'longitude'])) {
            $orders = $query->get()->map(function ($order) use ($request) {
                // حساب المسافة
                $distanceInfo = $this->googleMapsService->calculateDistanceAndTime(
                    $request->latitude,
                    $request->longitude,
                    $order->location->latitude,
                    $order->location->longitude
                );

                $order->distance_info = $distanceInfo;
                $order->estimated_distance_km = $distanceInfo ?
                    round($distanceInfo['distance']['value'] / 1000, 2) : null;
                $order->estimated_duration_minutes = $distanceInfo ?
                    round($distanceInfo['duration']['value'] / 60, 0) : null;

                return $order;
            });

            // ترتيب حسب المسافة
            $orders = $orders->sortBy('estimated_distance_km');
        } else {
            $orders = $query->get();
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);

        $paginatedOrders = $this->paginateCollection($orders, $perPage, $page);

        return $this->successResponse([
            'orders' => OrderResource::collection($paginatedOrders),
            'pagination' => [
                'current_page' => $paginatedOrders->currentPage(),
                'per_page' => $paginatedOrders->perPage(),
                'total' => $paginatedOrders->total(),
                'last_page' => $paginatedOrders->lastPage(),
            ],
            'filters' => [
                'service_id' => $request->service_id,
                'water_type_id' => $request->water_type_id,
                'has_location_filter' => $request->has(['latitude', 'longitude']),
                'total_available' => $orders->count(),
            ],
        ], 'تم جلب الطلبات المنتظرة بنجاح');
    }

    private function paginateCollection($collection, $perPage, $page)
    {
        $offset = ($page - 1) * $perPage;
        $paginated = $collection->slice($offset, $perPage);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginated,
            $collection->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }

    /**
     * عد الطلبات المتاحة للسائق
     */
    public function countPendingOrders(Request $request)
    {
        $driver = auth()->user()->driver;

        if (! $driver) {
            return $this->errorResponse('يجب أن تكون سائقاً', 403);
        }

        // التحقق من وجود طلبات نشطة
        $hasActiveOrder = Order::where('driver_id', $driver->id)
            ->whereIn('order_status_id', [1, 2, 3, 4])
            ->exists();

        if ($hasActiveOrder) {
            return $this->successResponse([
                'count' => 0,
                'has_active_order' => true,
                'message' => 'لديك طلب نشط بالفعل',
            ]);
        }

        // عد الطلبات المتاحة
        $expirationMinutes = config('orders.expiration_minutes', 5);
        $expiryTime = Carbon::now()->subMinutes($expirationMinutes);

        $count = Order::where('order_status_id', 1)
            ->whereNull('driver_id')
            ->where('created_at', '>=', $expiryTime)
            ->whereDoesntHave('offers', function ($query) use ($driver) {
                $query->where('driver_id', $driver->id);
            })
            ->count();

        return $this->successResponse([
            'count' => $count,
            'has_active_order' => false,
            'last_updated' => now()->toDateTimeString(),
        ]);
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(Request $request, $orderId)
    {
        $validated = $request->validate([
            'status_id' => 'required', // الحالات المسموحة
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
            'code_confirmation' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        $driver = auth('sanctum')->user()->driver;

        if (! $driver) {
            return $this->errorResponse('يجب أن تكون سائقاً', 403);
        }

        $order = Order::where('driver_id', $driver->id)
            ->where('id', $orderId)
            //  ->with(['location', 'user'])
            ->first();
        if (! $order) {
            return $this->errorResponse('الطلب غير موجود أو لا يخصك', 404);
        }
        $status=OrderStatus::find($validated['status_id']);
        if($status->name=='delivered' && $order->code_confirmation){
            if($validated['code_confirmation'] != $order->code_confirmation){
                return $this->errorResponse('كود التأكيد غير صحيح', 400);
            }
            // else{
            //     return $this->errorResponse('كود التأكيد مطلوب لهذه الحالة', 400);
            // }
        }

        // التحقق من تسلسل الحالات
        $allowedStatuses = $this->getAllowedNextStatuses($order->order_status_id);
        if (! in_array($validated['status_id'], $allowedStatuses)) {
            return $this->errorResponse('تسلسل الحالات غير صحيح', 400);
        }

        try {
            DB::beginTransaction();

            $oldStatus = $order->order_status_id;
            $status = OrderStatus::find($validated['status_id']);
            $order->update([
                'order_status_id' => $validated['status_id'],
                'status_updated_at' => now(),
            ]);

            // تسجيل تاريخ كل حالة
            $this->logStatusChange($order, $oldStatus, $validated['status_id'], $validated['notes'] ?? null);

            // إذا كانت هناك إحداثيات، تسجيل الموقع
            // if (!empty($validated['location_lat']) && !empty($validated['location_lng'])) {
            //     $this->updateDriverLocation($driver, $order, $validated['location_lat'], $validated['location_lng']);
            // }

            // إذا كانت الحالة "جاري التوصيل"، حساب وقت الوصول المتوقع
            if ($status->name == 'in-road') { // جاري التوصيل
                $this->calculateAndUpdateETA($driver, $order);
            }

            // إذا كانت الحالة "تم التسليم"، انهاء الطلب
            if ($status->name == 'delivered') { // تم التسليم
                $this->completeOrder($order);
            }

            DB::commit();

            // إرسال إشعارات
            $this->sendStatusNotifications($order, $oldStatus, $validated['status_id']);

            // Broadcast Events
            event(new OrderStatusUpdated($order, $oldStatus));

            if (! empty($validated['location_lat']) && ! empty($validated['location_lng'])) {
                event(new DriverLocationUpdated($driver, $order, $validated['location_lat'], $validated['location_lng']));
            }

            return $this->successResponse(
                [
                    'order' => new DriverOrderResource($order),
                    'new_status' => $order->status,
                    'estimated_arrival_time' => $order->driverLocations()->latest()->first()->estimated_arrival_time ?? null,
                ],
                'تم تحديث حالة الطلب بنجاح'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update order status failed: '.$e->getMessage());

            return $this->errorResponse('فشل تحديث حالة الطلب', 500);
        }
    }

    /**
     * تحديث موقع السائق
     */
    public function updateLocation(Request $request, $orderId)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'altitude' => 'nullable|numeric',
            'battery_level' => 'nullable|numeric|between:0,100',
            'is_moving' => 'nullable|boolean',
            'device_timestamp' => 'nullable|date',
        ]);

        $driver = auth()->user()->driver;

        if (! $driver) {
            return $this->errorResponse('يجب أن تكون سائقاً', 403);
        }

        $order = Order::where('driver_id', $driver->id)
            ->where('id', $orderId)
            ->whereIn('order_status_id', [2, 3]) // مقبول أو جاري التوصيل
            ->firstOrFail();

        try {
            // الحصول على العنوان من الإحداثيات
            $address = $this->googleMapsService->reverseGeocode(
                $validated['latitude'],
                $validated['longitude']
            );

            // حساب المسافة والوقت للوجهة
            $destination = $order->location;
            $distanceInfo = null;
            $estimatedArrival = null;

            if ($destination) {
                $distanceInfo = $this->googleMapsService->calculateDistanceAndTime(
                    $validated['latitude'],
                    $validated['longitude'],
                    $destination->latitude,
                    $destination->longitude
                );

                if ($distanceInfo) {
                    $estimatedArrival = now()->addSeconds($distanceInfo['duration']['value']);
                }
            }

            // تسجيل الموقع
            $location = DriverLocation::create([
                'driver_id' => $driver->id,
                'order_id' => $order->id,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'accuracy' => $validated['accuracy'] ?? null,
                'speed' => $validated['speed'] ?? null,
                'heading' => $validated['heading'] ?? null,
                'altitude' => $validated['altitude'] ?? null,
                'address' => $address['formatted_address'] ?? null,
                'battery_level' => $validated['battery_level'] ?? null,
                'is_moving' => $validated['is_moving'] ?? true,
                'device_timestamp' => $validated['device_timestamp'] ?? now(),
                'estimated_arrival_time' => $estimatedArrival,
                'distance_to_destination' => $distanceInfo['distance']['value'] ?? null,
            ]);

            // تحديث الطلب بآخر موقع
            $order->update([
                'last_location_updated_at' => now(),
            ]);

            // Broadcast Event
            event(new DriverLocationUpdated($driver, $order, $location));

            // إرسال إشعارات إذا كان الوقت المتبقي أقل من 5 دقائق
            $this->checkAndSendETAAlert($order, $distanceInfo);

            return $this->successResponse([
                'location' => $location,
                'distance_info' => $distanceInfo,
                'estimated_arrival' => $estimatedArrival ? $estimatedArrival->format('H:i') : null,
                'order_status' => $order->status->name,
            ], 'تم تحديث الموقع بنجاح');
        } catch (\Exception $e) {
            Log::error('Update driver location failed: '.$e->getMessage());

            return $this->errorResponse('فشل تحديث الموقع', 500);
        }
    }

    public function getOrder($orderId)
    {
        $order = Order::findOrFail($orderId);

        return $this->successResponse(new OrderResource($order), 'تم جلب الطلب بنجاح');
    }

    /**
     * جلب مسار السائق للطلب
     */
    public function getDriverPath($orderId)
    {
        $driver = auth()->user()->driver;

        if (! $driver) {
            return $this->errorResponse('يجب أن تكون سائقاً', 403);
        }

        $order = Order::where('driver_id', $driver->id)
            ->orWhere('user_id', auth()->id()) // يسمح للمستخدم برؤية مسار السائق
            ->findOrFail($orderId);

        $locations = DriverLocation::where('order_id', $orderId)
            ->orderBy('created_at', 'asc')
            ->get(['latitude', 'longitude', 'created_at', 'speed', 'heading']);

        $destination = $order->location;

        $currentLocation = $locations->last();
        $distanceInfo = null;

        if ($currentLocation && $destination) {
            $distanceInfo = $this->googleMapsService->calculateDistanceAndTime(
                $currentLocation->latitude,
                $currentLocation->longitude,
                $destination->latitude,
                $destination->longitude
            );
        }

        return $this->successResponse([
            'order_id' => $order->id,
            'order_status' => $order->status->name,
            'driver_name' => $order->driver->user?->name,
            'driver_phone' => $order->driver->user->phone,
            'driver_photo' => $order->driver->photo,
            'path' => $locations,
            'destination' => [
                'address' => $destination->address_details,
                'latitude' => $destination->latitude,
                'longitude' => $destination->longitude,
            ],
            'current_location' => $currentLocation,
            'distance_info' => $distanceInfo,
            'estimated_arrival' => $currentLocation->estimated_arrival_time ?? null,
            'total_points' => $locations->count(),
            'start_time' => $locations->first()->created_at ?? null,
            'last_update' => $locations->last()->created_at ?? null,
        ], 'تم جلب مسار السائق بنجاح');
    }

    /**
     * الحصول على حالة التتبع الحية
     */
    public function getLiveTracking($orderId)
    {
        $user = auth()->user();
        $order = Order::findOrFail($orderId);

        // التحقق من الصلاحية
        if (
            $order->user_id != $user->id &&
            ($user->driver && $order->driver_id != $user->driver->id)
        ) {
            return $this->errorResponse('غير مصرح لك بمتابعة هذا الطلب', 403);
        }

        $latestLocation = DriverLocation::where('order_id', $orderId)
            ->latest()
            ->first();

        if (! $latestLocation) {
            return $this->errorResponse('لا توجد بيانات تتبع لهذا الطلب', 404);
        }

        $destination = $order->location;
        $distanceInfo = null;

        if ($destination) {
            $distanceInfo = $this->googleMapsService->calculateDistanceAndTime(
                $latestLocation->latitude,
                $latestLocation->longitude,
                $destination->latitude,
                $destination->longitude
            );
        }

        $driver = $order->driver;

        return $this->successResponse([
            'order_id' => $order->id,
            'order_status' => $order->status->name,
            'tracking_active' => $latestLocation->created_at->diffInMinutes(now()) < 5,
            'last_update' => $latestLocation->created_at,
            'driver' => [
                'id' => $driver->id,
                'name' => $driver->user?->name,
                'phone' => $driver->user->phone,
                'photo' => $driver->photo,
                'rating' => $driver->average_rating,
                'vehicle' => $driver->vehicle->type ?? null,
                'plate_number' => $driver->vehicle->plate_number ?? null,
            ],
            'current_location' => [
                'latitude' => $latestLocation->latitude,
                'longitude' => $latestLocation->longitude,
                'address' => $latestLocation->address,
                'speed' => $latestLocation->speed,
                'heading' => $latestLocation->heading,
                'is_moving' => $latestLocation->is_moving,
            ],
            'destination' => [
                'address' => $destination->address_details,
                'latitude' => $destination->latitude,
                'longitude' => $destination->longitude,
            ],
            'distance_info' => $distanceInfo,
            'estimated_arrival' => $latestLocation->estimated_arrival_time,
            'polyline' => $this->generatePolyline($orderId),
        ], 'بيانات التتبع الحية');
    }

    // ========== الدوال المساعدة ==========

    private function getAllowedNextStatuses($currentStatus)
    {
        $statusFlow = [
            1 => [2, 5], // معلق -> مقبول أو منتهي
            2 => [3, 5], // مقبول -> جاري التوصيل أو منتهي
            3 => [4, 6], // جاري التوصيل -> تم التسليم أو ملغي
            4 => [], // تم التسليم (لا يمكن تغييرها)
            5 => [], // منتهي
            6 => [], // ملغي
        ];

        return $statusFlow[$currentStatus] ?? [];
    }

    private function logStatusChange($order, $oldStatus, $newStatus, $notes = null)
    {
        DB::table('order_status_history')->insert([
            'order_id' => $order->id,
            'old_status_id' => $oldStatus,
            'new_status_id' => $newStatus,
            'changed_by' => auth()->id(),
            'changed_by_type' => 'driver',
            'notes' => $notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function updateDriverLocation($driver, $order, $lat, $lng)
    {
        $address = $this->googleMapsService->reverseGeocode($lat, $lng);

        return DriverLocation::create([
            'driver_id' => $driver->id,
            'order_id' => $order->id,
            'latitude' => $lat,
            'longitude' => $lng,
            'address' => $address['formatted_address'] ?? null,
            'device_timestamp' => now(),
        ]);
    }

    private function calculateAndUpdateETA($driver, $order)
    {
        $latestLocation = DriverLocation::where('driver_id', $driver->id)
            ->where('order_id', $order->id)
            ->latest()
            ->first();

        if ($latestLocation && $order->location) {
            $distanceInfo = $this->googleMapsService->calculateDistanceAndTime(
                $latestLocation->latitude,
                $latestLocation->longitude,
                $order->location->latitude,
                $order->location->longitude
            );

            if ($distanceInfo) {
                $estimatedArrival = now()->addSeconds($distanceInfo['duration']['value']);

                $latestLocation->update([
                    'estimated_arrival_time' => $estimatedArrival,
                    'distance_to_destination' => $distanceInfo['distance']['value'],
                ]);

                // إرسال إشعار للمستخدم بوقت الوصول المتوقع
                $this->sendETAAlertToUser($order, $estimatedArrival);
            }
        }
    }

    // private function completeOrder($order)
    // {
    //     // تحديث إحصائيات السائق
    //     $driver = $order->driver;
    //     $driver->increment('total_orders');
    //     $driver->update([
    //         'last_order_completed_at' => now(),
    //     ]);

    //     // إشعار المستخدم بأن الطلب اكتمل
    //     $this->sendOrderCompletedAlert($order);

    //     // جدولة طلب التقييم بعد 5 دقائق
    //     RequestRatingJob::dispatch($order)
    //         ->delay(now()->addMinutes(5));
    // }

    private function completeOrder($order)
    {
        try {
            // تحديث إحصائيات السائق
            $driver = $order->driverOrder;
            $driver->increment('total_orders');
            $driver->update([
                'last_order_completed_at' => now(),
                'is_available' => true, // جعل السائق متاحاً لطلبات جديدة
            ]);

            // تحديث إحصائيات المستخدم
            $user = $order->user;
            // $user->increment('total_orders');

            // حساب متوسط إنفاق المستخدم
            // $totalSpent = $user->orders()->where('order_status_id', 4)->sum('price');

            // $user->update([
            //     'total_spent' => $totalSpent,
            //     'average_order_value' => $user->total_orders > 0 ? round($totalSpent / $user->total_orders, 2) : 0,
            // ]);
            // إشعار المستخدم بأن الطلب اكتمل
            $this->sendOrderCompletedAlert($order);

            // جدولة طلب التقييم بعد فترة
            $this->scheduleRatingRequest($order);

            // تسجيل اكتمال الطلب في السجل
            $this->logOrderCompletion($order);

            Log::info('Order completed successfully', [
                'order_id' => $order->id,
                'driver_id' => $driver->id,
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to complete order process: '.$e->getMessage(), [
                'order_id' => $order->id,
            ]);
            throw $e;
        }
    }

    private function scheduleRatingRequest($order)
    {
        // وقت تأخير طلب التقييم (5 دقائق بعد التسليم)
        $delayMinutes = config('orders.rating_request_delay_minutes', 5);

        // Job لطلب التقييم
        \App\Jobs\RequestRatingJob::dispatch($order)
            ->delay(now()->addMinutes($delayMinutes))
            ->onQueue('rating-requests');

        Log::info('Rating request scheduled', [
            'order_id' => $order->id,
            'scheduled_for' => now()->addMinutes($delayMinutes)->toDateTimeString(),
        ]);
    }

    private function logOrderCompletion($order)
    {
        try {

            DB::table('order_completion_logs')->insert([
                'order_id' => $order->id,
                'driver_id' => $order->driver_id,
                'user_id' => $order->user_id,
                'completed_at' => now(),
                'delivery_duration_minutes' => $this->calculateDeliveryDuration($order),
                'total_distance_km' => $this->calculateTotalDistance($order),
                'final_price' => (float)$order->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Order completion logged successfully', [
                'order_id' => $order->id,
            ]);
        } catch (\Throwable $e) {

            Log::error('Failed to log order completion', [
                'order_id' => $order->id ?? null,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            // لو عايز ترجع error
            throw $e;
            // أو لو مش عايز يوقف التنفيذ احذف throw
        }
    }

    private function calculateDeliveryDuration($order)
    {
        $acceptedAt = $order->acceptedOffer->created_at ?? $order->updated_at;

        return $acceptedAt->diffInMinutes(now());
    }

    private function calculateTotalDistance($order)
    {
        $locations = DriverLocation::where('order_id', $order->id)
            ->orderBy('created_at')
            ->get(['latitude', 'longitude']);

        $totalDistance = 0;

        for ($i = 1; $i < count($locations); $i++) {
            $distance = $this->googleMapsService->calculateHaversineDistance(
                $locations[$i - 1]->latitude,
                $locations[$i - 1]->longitude,
                $locations[$i]->latitude,
                $locations[$i]->longitude
            );

            if ($distance) {
                $totalDistance += $distance['distance_km'];
            }
        }

        return round($totalDistance, 2);
    }

    private function sendStatusNotifications($order, $oldStatus, $newStatus)
    {
        $statusNames = [
            1 => 'معلق',
            2 => 'مقبول',
            3 => 'جاري التوصيل',
            4 => 'تم التسليم',
            5 => 'منتهي',
            6 => 'ملغي',
        ];

        $userTokens = $order->user->activeDeviceTokens->pluck('token')->toArray();

        if (! empty($userTokens)) {
            $notificationData = [
                'title' => 'تحديث حالة الطلب',
                'body' => "تم تغيير حالة طلبك من {$statusNames[$oldStatus]} إلى {$statusNames[$newStatus]}",
                'image' => null,
            ];

            app(\App\Services\FirebaseNotificationService::class)
                ->sendToMultipleDevices($userTokens, $notificationData, [
                    'order_id' => $order->id,
                    'status_id' => $newStatus,
                    'type' => 'order_status_update',
                    'click_action' => 'ORDER_STATUS_UPDATE',
                ]);
        }
    }

    private function checkAndSendETAAlert($order, $distanceInfo)
    {
        if ($distanceInfo && $distanceInfo['duration']['value'] <= 300) { // 5 دقائق
            $userTokens = $order->user->activeDeviceTokens->pluck('token')->toArray();

            if (! empty($userTokens)) {
                app(\App\Services\FirebaseNotificationService::class)
                    ->sendToMultipleDevices($userTokens, [
                        'title' => 'وصول السائق قريباً',
                        'body' => "سائقك على بعد {$distanceInfo['distance']['text']} وسيصل خلال {$distanceInfo['duration']['text']}",
                        'image' => null,
                    ], [
                        'order_id' => $order->id,
                        'type' => 'eta_alert',
                        'eta_seconds' => $distanceInfo['duration']['value'],
                    ]);
            }
        }
    }

    private function sendETAAlertToUser($order, $estimatedArrival)
    {
        $etaMinutes = now()->diffInMinutes($estimatedArrival);

        $userTokens = $order->user->activeDeviceTokens->pluck('token')->toArray();

        if (! empty($userTokens)) {
            app(\App\Services\FirebaseNotificationService::class)
                ->sendToMultipleDevices($userTokens, [
                    'title' => 'وقت الوصول المتوقع',
                    'body' => "سائقك في طريقه وسيصل خلال {$etaMinutes} دقيقة تقريباً",
                    'image' => null,
                ], [
                    'order_id' => $order->id,
                    'eta' => $estimatedArrival->toDateTimeString(),
                    'type' => 'estimated_arrival',
                ]);
        }
    }

    private function sendOrderCompletedAlert($order)
    {

        $userTokens = $order->user->activeDeviceTokens->pluck('token')->toArray();
        if (! empty($userTokens)) {
            app(\App\Services\FirebaseNotificationService::class)
                ->sendToMultipleDevices($userTokens, [
                    'title' => 'تم تسليم الطلب',
                    'body' => 'تم تسليم طلبك بنجاح. يرجى تقييم تجربتك',
                    'image' => null,
                ], [
                    'order_id' => $order->id,
                    'type' => 'order_completed',
                    'action' => 'rate_order',
                ]);
        }
    }

    private function generatePolyline($orderId)
    {
        $locations = DriverLocation::where('order_id', $orderId)
            ->orderBy('created_at')
            ->get(['latitude', 'longitude']);

        if ($locations->count() < 2) {
            return null;
        }

        // هنا يمكنك استخدام خدمة Polyline Encoding إذا احتجت
        // أو إرجاع النقاط كما هي للـ Frontend
        return $locations->map(function ($location) {
            return [$location->latitude, $location->longitude];
        })->toArray();
    }
}
