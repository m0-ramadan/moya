<?php

namespace App\Http\Controllers\Api\Orders;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Driver;
use App\Models\OrderOffer;
use App\Models\OrderStatus;
use App\Jobs\ExpireOrderJob;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\Order\OfferCancelled;
use App\Http\Controllers\Controller;
use App\Events\Order\DriverAcceptOrder;
use App\Events\Order\NewOrderAvailable;
use App\Events\Order\UserConfirmedDriver;
use App\Services\FirebaseNotificationService;
use App\Http\Resources\WebsiteUser\OrderResource;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected $firebaseService;

    /**
     * إنشاء طلب جديد
     */
    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * إنشاء طلب جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'water_type_id' => 'nullable|exists:water_types,id',
            'order_date' => 'nullable|date',
            'saved_location_id' => 'required|exists:saved_locations,id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Get saved location details
            //    $savedLocation = \App\Models\SavedLocation::findOrFail($validated['saved_location_id']);
            $statusOrder = OrderStatus::where('name', 'pendding')->first();
            $order = Order::create([
                'user_id' => auth()->id(),
                'service_id' => $validated['service_id'],
                'water_type_id' => $validated['water_type_id'] ?? null,
                'saved_location_id' => $validated['saved_location_id'],
                'order_status_id' => $statusOrder->id, // pending
                'order_date' => $validated['order_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(env('ORDER_EXPIRATION_MINUTES', 5)),
                // Add price if needed
                'price' => null, // Will be set when driver accepts
            ]);

            DB::commit();

            // Load relationships for the event
            $order->load(['user', 'service', 'waterType', 'location']);

            // إذا كان الطلب غير مجدول (فوري)
            if (! $order->order_date) {
                $this->notifyAvailableDrivers($order);
            }

            return $this->successResponse(
                new OrderResource($order),
                'تم إنشاء الطلب بنجاح',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * إشعار السائقين المتاحين
     */
    private function notifyAvailableDrivers(Order $order)
    {
        // جلب السائقين المتاحين
        $availableDrivers = Driver::where('is_active', true)
            ->where('status', 'active')
            ->whereDoesntHave('orders', function ($query) {
                $query->whereIn('order_status_id', [1, 2, 3, 4]); // الطلبات النشطة
            })
            ->with(relations: 'user.activeDeviceTokens')
            ->get();

        // إرسال إشعار Firebase لكل سائق
        foreach ($availableDrivers as $driver) {
            $tokens = $driver->user->activeDeviceTokens->pluck('token')->toArray();

            if (! empty($tokens)) {
                $this->firebaseService->sendToMultipleDevices($tokens, [
                    'title' => 'طلب توصيل جديد',
                    'body' => 'طلب توصيل مياه جديد متاح! اضغط للموافقة.',
                    'image' => null,
                ], [
                    'order_id' => $order->id,
                    'type' => 'new_order_available',
                    'click_action' => 'NEW_ORDER_ACTION',
                ]);
            }
        }

        // Broadcast Event لكل السائقين المتصلين
        event(new NewOrderAvailable($order));

        // جدولة إلغاء الطلب إذا لم يتم الرد
        $this->scheduleOrderExpiration($order);
    }

    /**
     * قبول السائق للطلب
     */
    public function acceptOrder(Request $request, $orderId)
    {
        try {

            $validated = $request->validate([
                'price' => 'required|numeric|min:0',
                'delivery_duration_minutes' => 'required|integer|min:1',
            ]);
            $user = auth()->user();

            $driver = Driver::where('user_id', $user->id)->first();

            if (! $driver) {
                return $this->errorResponse('يجب أن تكون سائقاً', 403);
            }
            // 1️⃣ نجيب IDs حالات الطلب النشطة
            $activeStatusIds = OrderStatus::whereIn('name', ['pendding', 'in-road'])
                ->pluck('id')
                ->toArray();


            // 2️⃣ هل عنده طلب نشط؟
            $hasActiveOrders = Order::where('driver_id', $driver->id)
                ->whereIn('order_status_id', $activeStatusIds)
                ->exists();


            // 3️⃣ هل عنده عرض نشط مرتبط بطلب مفتوح؟
            $hasActiveOffers = OrderOffer::where('driver_id', $driver->id)
                ->whereIn('status', ['pending', 'accepted'])
                ->whereHas('order', function ($query) use ($activeStatusIds) {
                    $query->whereIn('order_status_id', $activeStatusIds);
                })
                ->exists();

            if ($hasActiveOrders || $hasActiveOffers) {
                return $this->errorResponse('لديك طلب نشط بالفعل. انتظر حتى يتم إنهاء الطلب الحالي.', 400);
            }
            DB::beginTransaction();
            // التحقق من أن السائق لم يقدم بالفعل على هذا الطلب
            $existingOffer = OrderOffer::where('order_id', $orderId)
                ->where('driver_id', $driver->id)
                ->first();

            if ($existingOffer) {
                return $this->errorResponse('لقد قدمت بالفعل على هذا الطلب', 400);
            }


            $offer = OrderOffer::create([
                'order_id' => $orderId,
                'driver_id' => $driver->id,
                'price' => $validated['price'],
                'delivery_duration_minutes' => $validated['delivery_duration_minutes'],
                'status' => 'pending',
            ]);

            DB::commit();

            // إرسال إشعار للمستخدم
            $this->notifyUserAboutNewOffer($offer);
            Log::info('--' . $orderId . '--' . $driver->id . $offer);
            // Broadcast Event
            event(new DriverAcceptOrder($offer));

            return $this->successResponse(
                $offer,
                'تم تقديم العرض بنجاح',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * إشعار المستخدم بعرض جديد
     */
    private function notifyUserAboutNewOffer(OrderOffer $offer)
    {
        $user = $offer->order->user;
        $tokens = $user->activeDeviceTokens->pluck('token')->toArray();

        if (! empty($tokens)) {
            $this->firebaseService->sendToMultipleDevices($tokens, [
                'title' => 'عرض جديد للطلب',
                'body' => 'قام سائق بتقديم عرض لطلبك! اضغط لعرض العروض.',
                'image' => null,
            ], [
                'order_id' => $offer->order_id,
                'offer_id' => $offer->id,
                'type' => 'new_offer',
                'click_action' => 'NEW_OFFER_ACTION',
            ]);
        }
    }

    /**
     * تأكيد المستخدم على سائق
     */

    // public function confirmDriver(Request $request, $orderId)
    // {
    //     $validated = $request->validate([
    //         'driver_id' => 'required|exists:drivers,id',
    //         'offer_id' => 'required|exists:order_offers,id',
    //     ]);

    //     $order = Order::where('user_id', auth()->id())
    //         ->where('id', $orderId)
    //         ->firstOrFail();

    //     if ($order->order_status_id != 1) { // pending
    //         return $this->errorResponse('لا يمكن تأكيد سائق لهذا الطلب', 400);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         // تحديث حالة جميع العروض
    //         OrderOffer::where('order_id', $orderId)
    //             ->update(['status' => 'rejected']);

    //         // قبول العرض المختار
    //         $acceptedOffer = OrderOffer::where('id', $validated['offer_id'])
    //             ->where('driver_id', $validated['driver_id'])
    //             ->firstOrFail();

    //         $acceptedOffer->update(['status' => 'accepted']);

    //         // تحديث الطلب
    //         $order->update([
    //             'driver_id' => $validated['driver_id'],
    //             'order_status_id' => 2, // accepted
    //             'price' => $acceptedOffer->price,
    //         ]);

    //         DB::commit();

    //         // إرسال إشعارات
    //         $this->notifyConfirmedDriver($order);
    //         $this->notifyOtherDrivers($order, $validated['driver_id']);

    //         // Broadcast Event
    //         event(new UserConfirmedDriver($order));

    //         return $this->successResponse(
    //             new OrderResource($order),
    //             'تم تأكيد السائق بنجاح'
    //         );
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return $this->errorResponse($e->getMessage(), 500);
    //     }
    // }

    /**
     * إشعار السائق المؤكد
     */
    private function notifyConfirmedDriver(Order $order)
    {
        $driver = $order->driver;
        $tokens = $driver->user->activeDeviceTokens->pluck('token')->toArray();

        if (! empty($tokens)) {
            $this->firebaseService->sendToMultipleDevices($tokens, [
                'title' => 'تم تأكيدك للطلب',
                'body' => 'تم تأكيدك للطلب! ابدأ بالتوصيل الآن.',
                'image' => null,
            ], [
                'order_id' => $order->id,
                'user_phone' => $order->user->phone,
                'type' => 'driver_confirmed',
                'click_action' => 'START_DELIVERY_ACTION',
            ]);
        }
    }

    /**
     * إشعار السائقين الآخرين
     */
    private function notifyOtherDrivers(Order $order, $confirmedDriverId)
    {
        $otherDrivers = OrderOffer::where('order_id', $order->id)
            ->where('driver_id', '!=', $confirmedDriverId)
            ->with('driver.user.activeDeviceTokens')
            ->get();

        foreach ($otherDrivers as $offer) {
            $tokens = $offer->driver->user->activeDeviceTokens->pluck('token')->toArray();

            if (! empty($tokens)) {
                $this->firebaseService->sendToMultipleDevices($tokens, [
                    'title' => 'طلب تم تأكيده',
                    'body' => 'تم اختيار سائق آخر لهذا الطلب. يمكنك البحث عن طلبات أخرى.',
                    'image' => null,
                ], [
                    'order_id' => $order->id,
                    'type' => 'order_taken',
                    'click_action' => 'FIND_OTHER_ORDERS',
                ]);
            }
        }
    }

    /**
     * إلغاء عرض السائق
     */
    public function cancelOffer(Request $request, $offerId)
    {
        $offer = OrderOffer::with('order')->findOrFail($offerId);

        // التحقق من أن السائق هو صاحب العرض
        if ($offer->driver_id != auth()->user()->driver->id) {
            return $this->errorResponse('غير مصرح لك بإلغاء هذا العرض', 403);
        }

        if ($offer->status != 'pending') {
            return $this->errorResponse('لا يمكن إلغاء هذا العرض', 400);
        }

        try {
            DB::beginTransaction();

            $offer->update(['status' => 'cancelled']);

            DB::commit();

            // Broadcast Event
            event(new OfferCancelled($offer, 'driver_cancelled'));

            return $this->successResponse(
                $offer,
                'تم إلغاء العرض بنجاح'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * تحقق من صلاحية الطلب
     */
    public function checkOrderStatus($orderId)
    {
        $order = Order::where('user_id', auth()->id())
            ->with(['offers' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->findOrFail($orderId);

        $response = [
            'order_id' => $order->id,
            'status_id' => $order->order_status_id,
            'status_name' => $order->status?->name,
            'remaining_offers' => $order->offers->count(),
            'expires_in' => null,
        ];

        // إذا كان الطلب معلقاً، احسب الوقت المتبقي
        if ($order->order_status_id == 1) {
            $createdAt = $order->created_at;
            $expirationMinutes = config('orders.expiration_minutes', 5);
            $expiresAt = $createdAt->addMinutes($expirationMinutes);

            $response['expires_in'] = [
                'minutes' => now()->diffInMinutes($expiresAt, false),
                'expires_at' => $expiresAt->toDateTimeString(),
            ];
        }

        return $this->successResponse($response);
    }

    /**
     * جدولة انتهاء صلاحية الطلب (محدث)
     */
    private function scheduleOrderExpiration(Order $order)
    {
        // Ensure expires_at is set
        if (! $order->expires_at) {
            $order->update([
                'expires_at' => Carbon::now()->addMinutes(config('services.orders.expiration_minutes', 5)),
            ]);
        }

        // Calculate delay in seconds
        $delayInSeconds = Carbon::now()->diffInSeconds($order->expires_at, false);

        // Only dispatch if not already expired
        if ($delayInSeconds > 0) {
            ExpireOrderJob::dispatch($order)
                ->delay(now()->addSeconds($delayInSeconds))
                ->onQueue('orders');
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

        // 🔍 فلترة الحالات (أكثر من حالة)
        if ($request->filled('status_ids')) {
            $statusIds = is_array($request->status_ids)
                ? $request->status_ids
                : explode(',', $request->status_ids);

            $query->whereIn('order_status_id', $statusIds);
        }

        // ⏱️ فلترة الوقت
        if ($request->filled('period')) {
            match ($request->period) {
                'yesterday' => $query->whereDate(
                    'created_at',
                    Carbon::yesterday()
                ),

                '7_days' => $query->where(
                    'created_at',
                    '>=',
                    Carbon::now()->subDays(7)
                ),

                'week' => $query->whereBetween(
                    'created_at',
                    [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]
                ),

                'month' => $query->whereMonth(
                    'created_at',
                    Carbon::now()->month
                ),

                default => null,
            };
        }

        // 📄 Pagination
        $perPage = $request->get('per_page', 10);

        $orders = $query
            ->latest()
            ->paginate($perPage);

        // 🧼 Resource fix
        $orders->setCollection(
            OrderResource::collection($orders->getCollection())->collection
        );

        return $this->paginated(
            $orders,
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

    /**
     * تأكيد المستخدم على سائق
     */
    public function confirmDriver(Request $request, $orderId)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'offer_id' => 'required|exists:order_offers,id',
        ]);

        $order = Order::where('user_id', auth()->id())
            ->where('id', $orderId)
            ->firstOrFail();

        if ($order->order_status_id != 1) { // pending
            return $this->errorResponse('لا يمكن تأكيد سائق لهذا الطلب', 400);
        }

        // التحقق من حالة الدفع
        if (! $order->isPaid()) {
            return $this->errorResponse('يجب إتمام عملية الدفع أولاً', 402);
        }

        try {
            DB::beginTransaction();

            // تحديث حالة جميع العروض
            OrderOffer::where('order_id', $orderId)
                ->update(['status' => 'rejected']);

            // قبول العرض المختار
            $acceptedOffer = OrderOffer::where('id', $validated['offer_id'])
                ->where('driver_id', $validated['driver_id'])
                ->firstOrFail();

            $acceptedOffer->update(['status' => 'accepted']);

            // تحديث الطلب
            $order->update([
                'driver_id' => $validated['driver_id'],
                'order_status_id' => 2, // accepted
                'price' => $acceptedOffer->price,
            ]);

            DB::commit();

            // إرسال إشعارات
            $this->notifyConfirmedDriver($order);
            $this->notifyOtherDrivers($order, $validated['driver_id']);

            // Broadcast Event
            event(new UserConfirmedDriver($order));

            return $this->successResponse(
                new OrderResource($order),
                'تم تأكيد السائق بنجاح'
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
