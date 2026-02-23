<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderOffer;
use App\Models\OrderRating;
use App\Models\OrderCancellation;
use App\Models\OrderCompletionLog;
use App\Models\Driver;
use App\Models\User;
use App\Models\DriverLocation;
use App\Models\OrderStatus;
use App\Models\Service;
use App\Models\WaterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with advanced filtering
     */
    public function index(Request $request)
    {
        $query = Order::with([
            'user',
            'driver.user',
            'service',
            'waterType',
            'location',
            'status',
            'acceptedOffer.driver.user'
        ])->latest('order_date');

        // Search by order number, user name, phone, etc.
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('driver.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by order status
        if ($request->filled('order_status_id')) {
            $query->where('order_status_id', $request->get('order_status_id'));
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->get('payment_status'));
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        // Filter by payment gateway
        if ($request->filled('payment_gateway')) {
            $query->where('payment_gateway', $request->get('payment_gateway'));
        }

        // Filter by driver
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->get('driver_id'));
        }

        // Filter by service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->get('service_id'));
        }

        // Filter by water type
        if ($request->filled('water_type_id')) {
            $query->where('water_type_id', $request->get('water_type_id'));
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->get('date_to'));
        }

        // Price range filters
        if ($request->filled('price_from')) {
            $query->whereHas('acceptedOffer', function ($q) use ($request) {
                $q->where('price', '>=', $request->get('price_from'));
            });
        }

        if ($request->filled('price_to')) {
            $query->whereHas('acceptedOffer', function ($q) use ($request) {
                $q->where('price', '<=', $request->get('price_to'));
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'order_date');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        if (in_array($sortBy, ['order_date', 'created_at', 'id', 'payment_status'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Get statistics
        $stats = $this->getOrderStatistics();

        // Get filter data
        $orderStatuses = OrderStatus::all();
        $services = Service::all();
        $waterTypes = WaterType::all();
        $drivers = Driver::with('user')->where('is_verified', true)->get();

        return view('Admin.orders.index', compact(
            'orders', 
            'stats', 
            'orderStatuses', 
            'services', 
            'waterTypes', 
            'drivers'
        ));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $users = User::where('status', 'active')->latest()->get();
        $services = Service::where('is_active', true)->get();
        $waterTypes = WaterType::where('is_active', true)->get();
        $orderStatuses = OrderStatus::all();

        return view('Admin.orders.create', compact('users', 'services', 'waterTypes', 'orderStatuses'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'water_type_id' => 'required|exists:water_types,id',
            'saved_location_id' => 'nullable|exists:saved_locations,id',
            'order_status_id' => 'required|exists:order_statuses,id',
            'payment_method' => 'required|string|max:255',
            'payment_gateway' => 'nullable|string|in:wallet,paymob,tamara,tabby',
            'order_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Generate order number (you might want to create a more sophisticated system)
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::max('id') + 1, 6, '0', STR_PAD_LEFT);

            $order = Order::create([
                'user_id' => $request->user_id,
                'service_id' => $request->service_id,
                'water_type_id' => $request->water_type_id,
                'saved_location_id' => $request->saved_location_id,
                'order_status_id' => $request->order_status_id,
                'payment_status' => Order::PAYMENT_STATUS_PENDING,
                'payment_method' => $request->payment_method,
                'payment_gateway' => $request->payment_gateway,
                'order_date' => $request->order_date,
                'expires_at' => now()->addHours(24), // Default expiration after 24 hours
            ]);

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'تم إنشاء الطلب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified order
     */
    public function show( $order)
    {
        $order = Order::findOrFail($order);

        $order->load([
            'user',
            'driver.user',
            'service',
            'waterType',
            'location',
            'status',
            'offers.driver.user',
            'acceptedOffer.driver.user',
            'driverLocations' => function ($query) {
                $query->latest()->limit(50);
            },
            'latestDriverLocation',
            'ratings.user',
            'ratings.driver.user',
            'cancellation',
            'completionLog'
        ]);

        // Get order timeline
        $timeline = $this->getOrderTimeline($order);

        // Get available drivers for assignment
        $availableDrivers = Driver::with('user')
            ->where('is_verified', true)
            ->where('is_active', true)
            ->whereDoesntHave('orders', function ($query) {
                $query->whereIn('order_status_id', [
                    OrderStatus::where('name', OrderStatus::PENDING)->first()->id ?? 0,
                    OrderStatus::where('name', OrderStatus::IN_ROAD)->first()->id ?? 0
                ]);
            })
            ->get();
        return view('Admin.orders.show', compact('order', 'timeline', 'availableDrivers'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'service', 'waterType', 'location']);
        
        $users = User::where('status', 'active')->latest()->get();
        $services = Service::where('is_active', true)->get();
        $waterTypes = WaterType::where('is_active', true)->get();
        $orderStatuses = OrderStatus::all();

        return view('Admin.orders.edit', compact('order', 'users', 'services', 'waterTypes', 'orderStatuses'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'water_type_id' => 'required|exists:water_types,id',
            'saved_location_id' => 'nullable|exists:saved_locations,id',
            'order_status_id' => 'required|exists:order_statuses,id',
            'payment_status' => 'required|in:' . implode(',', [
                Order::PAYMENT_STATUS_PENDING,
                Order::PAYMENT_STATUS_PROCESSING,
                Order::PAYMENT_STATUS_PAID,
                Order::PAYMENT_STATUS_FAILED,
                Order::PAYMENT_STATUS_REFUNDED,
                Order::PAYMENT_STATUS_PARTIALLY_REFUNDED
            ]),
            'payment_method' => 'required|string|max:255',
            'payment_gateway' => 'nullable|string|in:wallet,paymob,tamara,tabby',
            'payment_transaction_id' => 'nullable|string|max:255',
            'paid_at' => 'nullable|date',
            'order_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $oldStatus = $order->order_status_id;
            $newStatus = $request->order_status_id;

            // Update order
            $order->update($request->except(['_token', '_method']));

            // Handle payment status change
            if ($request->payment_status == Order::PAYMENT_STATUS_PAID && !$order->paid_at) {
                $order->paid_at = now();
                $order->save();
            }

            // Handle order completion
            if ($this->isOrderCompleted($newStatus) && !$this->isOrderCompleted($oldStatus)) {
                $this->completeOrder($order);
            }

            // Handle order cancellation
            if ($this->isOrderCancelled($newStatus) && !$this->isOrderCancelled($oldStatus)) {
                if (!$order->cancellation) {
                    // You might want to redirect to cancellation form instead
                    $this->cancelOrder($order, null, auth()->id(), null, 'تم الإلغاء بواسطة المسؤول');
                }
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'تم تحديث الطلب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الطلب: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order)
    {
        // Check if order can be deleted
        if (!in_array($order->payment_status, [Order::PAYMENT_STATUS_PENDING, Order::PAYMENT_STATUS_FAILED])) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الطلب في هذه الحالة');
        }

        DB::beginTransaction();

        try {
            // Delete related records
            $order->offers()->delete();
            $order->driverLocations()->delete();
            $order->ratings()->delete();
            
            if ($order->cancellation) {
                $order->cancellation()->delete();
            }
            
            if ($order->completionLog) {
                $order->completionLog()->delete();
            }

            $order->delete();

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'تم حذف الطلب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الطلب: ' . $e->getMessage());
        }
    }

    /**
     * Assign driver to order
     */
    public function assignDriver(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'price' => 'required|numeric|min:0',
            'delivery_duration_minutes' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Check if driver is available
            $driver = Driver::find($request->driver_id);
            if (!$driver->is_active || !$driver->is_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'السائق غير متاح'
                ], 400);
            }

            // Create order offer
            $offer = OrderOffer::create([
                'order_id' => $order->id,
                'driver_id' => $request->driver_id,
                'price' => $request->price,
                'delivery_duration_minutes' => $request->delivery_duration_minutes,
                'status' => 'accepted', // Direct assignment
            ]);

            // Update order
            $order->driver_id = $request->driver_id;
            $order->order_status_id = OrderStatus::where('name', OrderStatus::IN_ROAD)->first()->id;
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تعيين السائق بنجاح',
                'data' => $offer->load('driver.user')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order payment status
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:' . implode(',', [
                Order::PAYMENT_STATUS_PENDING,
                Order::PAYMENT_STATUS_PROCESSING,
                Order::PAYMENT_STATUS_PAID,
                Order::PAYMENT_STATUS_FAILED,
                Order::PAYMENT_STATUS_REFUNDED,
                Order::PAYMENT_STATUS_PARTIALLY_REFUNDED
            ]),
            'payment_transaction_id' => 'nullable|string|max:255',
            'payment_details' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldStatus = $order->payment_status;
        $newStatus = $request->payment_status;

        $order->payment_status = $newStatus;
        
        if ($request->filled('payment_transaction_id')) {
            $order->payment_transaction_id = $request->payment_transaction_id;
        }
        
        if ($request->filled('payment_details')) {
            $order->payment_details = $request->payment_details;
        }

        if ($newStatus == Order::PAYMENT_STATUS_PAID && $oldStatus != Order::PAYMENT_STATUS_PAID) {
            $order->paid_at = now();
        }

        if ($newStatus == Order::PAYMENT_STATUS_REFUNDED && $oldStatus != Order::PAYMENT_STATUS_REFUNDED) {
            // Handle refund logic
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الدفع بنجاح',
            'payment_status' => $order->payment_status
        ]);
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Check if order can be cancelled
            if (in_array($order->payment_status, [Order::PAYMENT_STATUS_PAID, Order::PAYMENT_STATUS_REFUNDED])) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إلغاء الطلب في هذه الحالة'
                ], 400);
            }

            // Create cancellation record
            $cancellation = OrderCancellation::create([
                'order_id' => $order->id,
                'cancelled_by_user_id' => auth()->user()->id,
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            // Update order status
            $order->order_status_id = OrderStatus::where('name', OrderStatus::CANCELLED)->first()->id;
            $order->save();

            // Handle refund if payment was made
            if ($order->isPaid()) {
                // Process refund logic here
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الطلب بنجاح',
                'data' => $cancellation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order tracking information
     */
    public function tracking(Order $order)
    {
        $order->load([
            'driver.user',
            'latestDriverLocation',
            'location',
            'status'
        ]);

        if (!$order->driver) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد سائق للطلب'
            ], 404);
        }

        // Get driver location history for this order
        $locations = DriverLocation::where('order_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'current_location' => $order->latestDriverLocation,
                'location_history' => $locations,
                'destination' => $order->location,
                'estimated_arrival' => $order->latestDriverLocation?->estimated_arrival_time,
            ]
        ]);
    }

    /**
     * Get order offers
     */
    public function offers(Order $order)
    {
        $offers = $order->offers()->with('driver.user')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $offers
        ]);
    }

    /**
     * Get order ratings
     */
    public function ratings(Order $order)
    {
        $ratings = $order->ratings()->with(['user', 'driver.user'])->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $ratings
        ]);
    }

    /**
     * Print order invoice
     */
    public function printInvoice(Order $order)
    {
        $order->load([
            'user',
            'driver.user',
            'service',
            'waterType',
            'location',
            'acceptedOffer',
           // 'completionLog'
        ]);

        return view('Admin.orders.invoice', compact('order'));
    }

    /**
     * Export orders to Excel/CSV
     */
    public function export(Request $request)
    {
        $query = Order::with(['user', 'driver.user', 'service', 'waterType', 'status']);

        // Apply filters similar to index method
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        if ($request->filled('order_status_id')) {
            $query->where('order_status_id', $request->order_status_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->get();

        // Generate CSV
        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            'رقم الطلب',
            'اسم العميل',
            'هاتف العميل',
            'اسم السائق',
            'الخدمة',
            'نوع المياه',
            'حالة الطلب',
            'حالة الدفع',
            'طريقة الدفع',
            'السعر',
            'تاريخ الطلب',
        ];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $row = [
                    $order->id,
                    $order->user?->name,
                    $order->user?->phone,
                    $order->driver?->user?->name,
                    $order->service?->name,
                    $order->waterType?->name,
                    $order->status?->label,
                    $order->payment_status,
                    $order->payment_method,
                    $order->acceptedOffer?->price ?? 0,
                    $order->order_date?->format('Y-m-d H:i'),
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get order statistics
     */
    public function statistics()
    {
        $stats = $this->getOrderStatistics();

        // Get charts data
        $ordersByDay = Order::select(
                DB::raw('DATE(order_date) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN payment_status = "paid" THEN 1 ELSE 0 END) as paid_count')
            )
            ->where('order_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $revenueByDay = Order::join('order_offers', 'orders.id', '=', 'order_offers.order_id')
            ->select(
                DB::raw('DATE(orders.order_date) as date'),
                DB::raw('SUM(order_offers.price) as revenue')
            )
            ->where('orders.payment_status', Order::PAYMENT_STATUS_PAID)
            ->where('orders.order_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topDrivers = Driver::with('user')
            ->select('drivers.*', DB::raw('COUNT(orders.id) as orders_count'))
            ->join('orders', 'drivers.id', '=', 'orders.driver_id')
            ->where('orders.payment_status', Order::PAYMENT_STATUS_PAID)
            ->groupBy('drivers.id')
            ->orderBy('orders_count', 'desc')
            ->limit(10)
            ->get();

        $topUsers = User::select('users.*', DB::raw('COUNT(orders.id) as orders_count'))
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.payment_status', Order::PAYMENT_STATUS_PAID)
            ->groupBy('users.id')
            ->orderBy('orders_count', 'desc')
            ->limit(10)
            ->get();

        return view('Admin.orders.statistics', compact(
            'stats',
            'ordersByDay',
            'revenueByDay',
            'topDrivers',
            'topUsers'
        ));
    }

    /**
     * Bulk update orders
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'action' => 'required|in:update_status,update_payment_status,assign_driver,delete',
            'order_status_id' => 'required_if:action,update_status|exists:order_statuses,id',
            'payment_status' => 'required_if:action,update_payment_status|in:' . implode(',', [
                Order::PAYMENT_STATUS_PENDING,
                Order::PAYMENT_STATUS_PROCESSING,
                Order::PAYMENT_STATUS_PAID,
                Order::PAYMENT_STATUS_FAILED,
                Order::PAYMENT_STATUS_REFUNDED,
                Order::PAYMENT_STATUS_PARTIALLY_REFUNDED
            ]),
            'driver_id' => 'required_if:action,assign_driver|exists:drivers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $orders = Order::whereIn('id', $request->order_ids)->get();
            $updatedCount = 0;

            foreach ($orders as $order) {
                switch ($request->action) {
                    case 'update_status':
                        $order->order_status_id = $request->order_status_id;
                        $order->save();
                        $updatedCount++;
                        break;

                    case 'update_payment_status':
                        $order->payment_status = $request->payment_status;
                        if ($request->payment_status == Order::PAYMENT_STATUS_PAID && !$order->paid_at) {
                            $order->paid_at = now();
                        }
                        $order->save();
                        $updatedCount++;
                        break;

                    case 'assign_driver':
                        if (!$order->driver_id && !$order->acceptedOffer) {
                            $order->driver_id = $request->driver_id;
                            $order->save();
                            
                            // Create offer
                            OrderOffer::create([
                                'order_id' => $order->id,
                                'driver_id' => $request->driver_id,
                                'status' => 'accepted',
                                'price' => 0, // You might want to calculate this
                            ]);
                            
                            $updatedCount++;
                        }
                        break;

                    case 'delete':
                        if (in_array($order->payment_status, [Order::PAYMENT_STATUS_PENDING, Order::PAYMENT_STATUS_FAILED])) {
                            $order->offers()->delete();
                            $order->delete();
                            $updatedCount++;
                        }
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "تم تحديث {$updatedCount} طلب بنجاح"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete order and create completion log
     */
    private function completeOrder(Order $order)
    {
        if (!$order->driver_id) {
            return false;
        }

        // Get accepted offer
        $offer = $order->acceptedOffer;
        if (!$offer) {
            return false;
        }

        // Calculate delivery duration
        $firstLocation = DriverLocation::where('order_id', $order->id)
            ->where('driver_id', $order->driver_id)
            ->oldest()
            ->first();

        $deliveryDuration = null;
        if ($firstLocation) {
            $deliveryDuration = now()->diffInMinutes($firstLocation->created_at);
        }

        // Calculate total distance
        $totalDistance = DriverLocation::where('order_id', $order->id)
            ->where('driver_id', $order->driver_id)
            ->sum('distance_to_destination') ?? 0;

        // Create completion log
        OrderCompletionLog::create([
            'order_id' => $order->id,
            'driver_id' => $order->driver_id,
            'user_id' => $order->user_id,
            'completed_at' => now(),
            'delivery_duration_minutes' => $deliveryDuration,
            'total_distance_km' => $totalDistance,
            'final_price' => $offer->price,
        ]);

        return true;
    }

    /**
     * Cancel order
     */
    // private function cancelOrder(Order $order, ?int $userId, ?int $driverId, ?string $reason, ?string $notes)
    // {
    //     if ($order->cancellation) {
    //         return false;
    //     }

    //     $cancellation = OrderCancellation::create([
    //         'order_id' => $order->id,
    //         'cancelled_by_user_id' => $userId,
    //         'cancelled_by_driver_id' => $driverId,
    //         'reason' => $reason,
    //         'notes' => $notes,
    //     ]);

    //     $order->order_status_id = OrderStatus::where('name', OrderStatus::CANCELLED)->first()->id;
    //     $order->save();

    //     return $cancellation;
    // }

    /**
     * Check if order is completed
     */
    private function isOrderCompleted($statusId): bool
    {
        $status = OrderStatus::find($statusId);
        return $status && $status->name === OrderStatus::DELIVERED;
    }

    /**
     * Check if order is cancelled
     */
    private function isOrderCancelled($statusId): bool
    {
        $status = OrderStatus::find($statusId);
        return $status && $status->name === OrderStatus::CANCELLED;
    }

    /**
     * Get order timeline
     */
    private function getOrderTimeline(Order $order): array
    {
        $timeline = [];

        // Order created
        $timeline[] = [
            'event' => 'order_created',
            'label' => 'تم إنشاء الطلب',
            'time' => $order->created_at,
            'icon' => 'fa-plus-circle',
            'color' => 'primary',
        ];

        // First offer
        $firstOffer = $order->offers()->oldest()->first();
        if ($firstOffer) {
            $timeline[] = [
                'event' => 'offer_received',
                'label' => 'تم استلام أول عرض',
                'time' => $firstOffer->created_at,
                'icon' => 'fa-tag',
                'color' => 'info',
            ];
        }

        // Driver assigned
        if ($order->driver_id) {
            $timeline[] = [
                'event' => 'driver_assigned',
                'label' => 'تم تعيين السائق',
                'time' => $order->updated_at, // You might want to track this separately
                'icon' => 'fa-user-check',
                'color' => 'warning',
            ];
        }

        // Payment completed
        if ($order->paid_at) {
            $timeline[] = [
                'event' => 'payment_completed',
                'label' => 'تم إتمام الدفع',
                'time' => $order->paid_at,
                'icon' => 'fa-credit-card',
                'color' => 'success',
            ];
        }

        // Order delivered
        if ($order->completionLog) {
            $timeline[] = [
                'event' => 'order_delivered',
                'label' => 'تم توصيل الطلب',
                'time' => $order->completionLog->completed_at,
                'icon' => 'fa-check-circle',
                'color' => 'success',
            ];
        }

        // Order cancelled
        if ($order->cancellation) {
            $timeline[] = [
                'event' => 'order_cancelled',
                'label' => 'تم إلغاء الطلب',
                'time' => $order->cancellation->created_at,
                'icon' => 'fa-times-circle',
                'color' => 'danger',
            ];
        }

        // Sort by time
        usort($timeline, function ($a, $b) {
            return $a['time']->timestamp <=> $b['time']->timestamp;
        });

        return $timeline;
    }

    /**
     * Get order statistics
     */
    private function getOrderStatistics(): array
    {
        $statuses = OrderStatus::all();
        $statusCounts = [];

        foreach ($statuses as $status) {
            $statusCounts[$status->name] = Order::where('order_status_id', $status->id)->count();
        }

        $paymentStatuses = [
            Order::PAYMENT_STATUS_PENDING,
            Order::PAYMENT_STATUS_PROCESSING,
            Order::PAYMENT_STATUS_PAID,
            Order::PAYMENT_STATUS_FAILED,
            Order::PAYMENT_STATUS_REFUNDED,
            Order::PAYMENT_STATUS_PARTIALLY_REFUNDED,
        ];

        $paymentStatusCounts = [];
        foreach ($paymentStatuses as $status) {
            $paymentStatusCounts[$status] = Order::where('payment_status', $status)->count();
        }

        $totalRevenue = Order::join('order_offers', 'orders.id', '=', 'order_offers.order_id')
            ->where('orders.payment_status', Order::PAYMENT_STATUS_PAID)
            ->sum('order_offers.price');

        $todayRevenue = Order::join('order_offers', 'orders.id', '=', 'order_offers.order_id')
            ->where('orders.payment_status', Order::PAYMENT_STATUS_PAID)
            ->whereDate('orders.paid_at', today())
            ->sum('order_offers.price');

        $averageOrderValue = Order::join('order_offers', 'orders.id', '=', 'order_offers.order_id')
            ->where('orders.payment_status', Order::PAYMENT_STATUS_PAID)
            ->avg('order_offers.price') ?? 0;

        return [
            'total_orders' => Order::count(),
            'total_revenue' => $totalRevenue,
            'average_order_value' => $averageOrderValue,
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => $todayRevenue,
            'weekly_orders' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'weekly_revenue' => Order::join('order_offers', 'orders.id', '=', 'order_offers.order_id')
                ->where('orders.payment_status', Order::PAYMENT_STATUS_PAID)
                ->whereBetween('orders.paid_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('order_offers.price'),
            'monthly_orders' => Order::whereMonth('created_at', now()->month)->count(),
            'monthly_revenue' => Order::join('order_offers', 'orders.id', '=', 'order_offers.order_id')
                ->where('orders.payment_status', Order::PAYMENT_STATUS_PAID)
                ->whereMonth('orders.paid_at', now()->month)
                ->sum('order_offers.price'),
            'status_counts' => $statusCounts,
            'payment_status_counts' => $paymentStatusCounts,
        ];
    }

}