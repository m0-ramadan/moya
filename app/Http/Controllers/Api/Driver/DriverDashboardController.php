<?php

namespace App\Http\Controllers\Api\Driver;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Driver;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Driver\OrderResource;
use App\Http\Resources\Driver\EarningResource;

class DriverDashboardController extends Controller
{
    use ApiResponseTrait;

    /**
     * Dashboard الرئيسي للسائق
     */
    public function index(Request $request)
    {
        $driver = $request->user()->driver;

        if (!$driver) {
            return $this->errorResponse('لم يتم العثور على بيانات السائق', 404);
        }

        // الإحصائيات الأساسية
        $stats = $this->getBasicStats($driver);

        // الطلبات النشطة
        $activeOrders = $this->getActiveOrders($driver);

        // الأرباح اليومية والأسبوعية
        $earnings = $this->getEarningsSummary($driver);

        // تقييمات حديثة
        $recentRatings = $this->getRecentRatings($driver);

        // الطلبات المتاحة القريبة
        $availableOrders = $this->getAvailableOrders($driver);

        // التحذيرات والمستندات المنتهية
        $alerts = $this->getAlerts($driver);

        return $this->successResponse([
            'driver' => [
                'id' => $driver->id,
                'name' => $driver->full_name,
                'photo' => $driver->photo,
                'status' => $driver->status,
                'is_active' => $driver->is_active,
                'is_verified' => $driver->is_verified,
                'average_rating' => $driver->average_rating,
                'total_orders' => $driver->total_orders,
                'completion_rate' => $driver->completion_rate,
                'vehicle' => $driver->vehicle?->only(['type', 'plate_number', 'model', 'year']),
                'wallet_balance' => $driver->wallet?->balance ?? 0,
                'available_balance' => $driver->wallet?->available_balance ?? 0,
            ],
            'stats' => $stats,
            'active_orders' => $activeOrders,
            'earnings' => $earnings,
            'recent_ratings' => $recentRatings,
            'available_orders' => $availableOrders,
            'alerts' => $alerts,
            'summary' => $this->getDashboardSummary($driver),
        ], 'تم جلب بيانات Dashboard بنجاح');
    }

    /**
     * الإحصائيات المفصلة
     */
    public function stats(Request $request)
    {
        $driver = $request->user()->driver;

        if (!$driver) {
            return $this->errorResponse('لم يتم العثور على بيانات السائق', 404);
        }

        $period = $request->input('period', 'today'); // today, week, month, year

        return $this->successResponse([
            'period' => $period,
            'overview' => $this->getOverviewStats($driver, $period),
            'performance' => $this->getPerformanceStats($driver, $period),
            'earnings_breakdown' => $this->getEarningsBreakdown($driver, $period),
            'order_analysis' => $this->getOrderAnalysis($driver, $period),
            'comparison' => $this->getComparisonStats($driver, $period),
        ], 'تم جلب الإحصائيات بنجاح');
    }

    /**
     * الطلبات الأخيرة
     */
    public function recentOrders(Request $request)
    {
        $driver = $request->user()->driver;

        if (!$driver) {
            return $this->errorResponse('لم يتم العثور على بيانات السائق', 404);
        }

        $perPage = $request->input('per_page', 10);
        $status = $request->input('status'); // all, active, completed, cancelled

        $query = Order::where('driver_id', $driver->id)
            ->with(['user:id,name,phone', 'service:id,name', 'status:id,name'])
            ->latest();

        // فلترة حسب الحالة
        if ($status && $status !== 'all') {
            switch ($status) {
                case 'active':
                    $query->whereIn('order_status_id', [2, 3]); // مقبول وجاري التوصيل
                    break;
                case 'completed':
                    $query->where('order_status_id', 4); // تم التسليم
                    break;
                case 'cancelled':
                    $query->whereIn('order_status_id', [5, 6]); // منتهي وملغي
                    break;
            }
        }

        $orders = $query->paginate($perPage);

        return $this->successResponse([
            'orders' => OrderResource::collection($orders),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
            'summary' => [
                'total' => $orders->total(),
                'active' => Order::where('driver_id', $driver->id)->whereIn('order_status_id', [2, 3])->count(),
                'completed' => Order::where('driver_id', $driver->id)->where('order_status_id', 4)->count(),
                'cancelled' => Order::where('driver_id', $driver->id)->whereIn('order_status_id', [5, 6])->count(),
            ],
        ], 'تم جلب الطلبات بنجاح');
    }

    /**
     * الأرباح والمدفوعات
     */
    public function earnings(Request $request)
    {
        $driver = $request->user()->driver;

        if (!$driver) {
            return $this->errorResponse('لم يتم العثور على بيانات السائق', 404);
        }

        $period = $request->input('period', 'month'); // today, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($period === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            list($start, $end) = $this->getDateRange($period);
        }

        $earnings = $this->getDetailedEarnings($driver, $start, $end);

        return $this->successResponse([
            'period' => $period,
            'date_range' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ],
            'summary' => $earnings['summary'],
            'transactions' => EarningResource::collection($earnings['transactions']),
            'daily_breakdown' => $earnings['daily_breakdown'],
            'payment_methods' => $earnings['payment_methods'],
            'withdrawals' => $earnings['withdrawals'],
            'taxes_and_fees' => $earnings['taxes_and_fees'],
        ], 'تم جلب بيانات الأرباح بنجاح');
    }

    /**
     * تحديث حالة السائق (متاح/غير متاح)
     */
    public function updateAvailability(Request $request)
    {
        $request->validate([
            'is_available' => ['required', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $driver = $request->user()->driver;

        if (!$driver) {
            return $this->errorResponse('لم يتم العثور على بيانات السائق', 404);
        }

        $oldStatus = $driver->is_active;
        $driver->update([
            'is_active' => $request->is_available,
            'status_updated_at' => now(),
            'status_reason' => $request->reason,
        ]);

        // تسجيل تغيير الحالة
        DB::table('driver_status_history')->insert([
            'driver_id' => $driver->id,
            'old_status' => $oldStatus ? 'active' : 'inactive',
            'new_status' => $request->is_available ? 'active' : 'inactive',
            'reason' => $request->reason,
            'changed_by' => 'driver',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->successResponse([
            'driver' => [
                'id' => $driver->id,
                'is_active' => $driver->is_active,
                'status' => $driver->status,
                'status_updated_at' => $driver->status_updated_at,
            ],
        ], $request->is_available ? 'تم تحديث حالتك إلى متاح' : 'تم تحديث حالتك إلى غير متاح');
    }

    /**
     * تحديث معلومات الموقع والعمل
     */
    public function updateWorkingInfo(Request $request)
    {
        $request->validate([
            'radius_km' => ['nullable', 'integer', 'min:5', 'max:100'],
            'preferred_working_hours' => ['nullable', 'string', 'max:100'],
            'max_daily_orders' => ['nullable', 'integer', 'min:1', 'max:20'],
            'current_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'current_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'current_address' => ['nullable', 'string', 'max:500'],
        ]);

        $driver = $request->user()->driver;

        if (!$driver) {
            return $this->errorResponse('لم يتم العثور على بيانات السائق', 404);
        }

        $data = $request->only([
            'radius_km',
            'preferred_working_hours',
            'max_daily_orders',
            'current_latitude',
            'current_longitude',
            'current_address'
        ]);

        $driver->update($data);

        // تسجيل تحديث الموقع
        if ($request->has('current_latitude') && $request->has('current_longitude')) {
            DB::table('driver_location_logs')->insert([
                'driver_id' => $driver->id,
                'latitude' => $request->current_latitude,
                'longitude' => $request->current_longitude,
                'address' => $request->current_address,
                'type' => 'manual_update',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $this->successResponse([
            'driver' => [
                'id' => $driver->id,
                'radius_km' => $driver->radius_km,
                'preferred_working_hours' => $driver->preferred_working_hours,
                'max_daily_orders' => $driver->max_daily_orders,
                'current_location' => [
                    'latitude' => $driver->current_latitude,
                    'longitude' => $driver->current_longitude,
                    'address' => $driver->current_address,
                ],
            ],
        ], 'تم تحديث معلومات العمل بنجاح');
    }

    /**
     * التقارير والأداء
     */
    public function reports(Request $request)
    {
        $driver = $request->user()->driver;

        if (!$driver) {
            return $this->errorResponse('لم يتم العثور على بيانات السائق', 404);
        }

        $period = $request->input('period', 'month');
        list($start, $end) = $this->getDateRange($period);

        return $this->successResponse([
            'performance_report' => $this->getPerformanceReport($driver, $start, $end),
            'financial_report' => $this->getFinancialReport($driver, $start, $end),
            'customer_feedback' => $this->getCustomerFeedback($driver, $start, $end),
            'comparison_with_peers' => $this->getPeerComparison($driver, $start, $end),
            'insights' => $this->getInsights($driver, $start, $end),
        ], 'تم جلب التقارير بنجاح');
    }

    // ========== الدوال المساعدة ==========

    private function getBasicStats(Driver $driver)
    {
        $today = Carbon::today();

        return [
            'today' => [
                'orders' => Order::where('driver_id', $driver->id)
                    ->whereDate('created_at', $today)
                    ->count(),
                'completed_orders' => Order::where('driver_id', $driver->id)
                    ->where('order_status_id', 4)
                    ->whereDate('created_at', $today)
                    ->count(),
                'earnings' => Order::where('driver_id', $driver->id)
                    ->where('order_status_id', 4)
                    ->whereDate('created_at', $today)
                    ->sum('price'),
                'rating' => $driver->average_rating,
                'acceptance_rate' => $this->calculateAcceptanceRate($driver, $today, $today),
            ],
            'week' => [
                'orders' => Order::where('driver_id', $driver->id)
                    ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->count(),
                'earnings' => Order::where('driver_id', $driver->id)
                    ->where('order_status_id', 4)
                    ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->sum('price'),
            ],
            'month' => [
                'orders' => Order::where('driver_id', $driver->id)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'earnings' => Order::where('driver_id', $driver->id)
                    ->where('order_status_id', 4)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('price'),
            ],
        ];
    }

    private function getActiveOrders(Driver $driver)
    {
        return Order::where('driver_id', $driver->id)
            ->whereIn('order_status_id', [2, 3]) // مقبول وجاري التوصيل
            ->with(['user:id,name,phone', 'service:id,name', 'location'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'customer' => [
                        'name' => $order->user->name,
                        'phone' => $order->user->phone,
                    ],
                    'service' => $order->service->name,
                    'status' => $order->status->name,
                    'price' => $order->price,
                    'created_at' => $order->created_at->format('H:i'),
                    'destination' => $order->location->address_details ?? null,
                    'estimated_time' => $order->acceptedOffer->delivery_duration_minutes ?? null,
                ];
            });
    }

    private function getEarningsSummary(Driver $driver)
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        return [
            'today' => [
                'total' => Order::where('driver_id', $driver->id)
                    ->where('order_status_id', 4)
                    ->whereDate('created_at', $today)
                    ->sum('price'),
                'tips' => $this->getTipsAmount($driver, $today, $today),
                'bonuses' => $this->getBonusesAmount($driver, $today, $today),
            ],
            'week' => [
                'total' => Order::where('driver_id', $driver->id)
                    ->where('order_status_id', 4)
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->sum('price'),
                'average_per_day' => $this->getAverageDailyEarnings($driver, $weekStart, $weekEnd),
                'projected_weekly' => $this->projectWeeklyEarnings($driver),
            ],
            'pending_withdrawal' => $driver->wallet->held_balance ?? 0,
            'available_for_withdrawal' => $driver->wallet->available_balance ?? 0,
            'total_earned' => $driver->wallet->total_earned ?? 0,
        ];
    }

    private function getRecentRatings(Driver $driver)
    {
        return DB::table('order_ratings')
            ->where('driver_id', $driver->id)
            ->where('rated_by', 'user')
            ->join('users', 'order_ratings.user_id', '=', 'users.id')
            ->join('orders', 'order_ratings.order_id', '=', 'orders.id')
            ->select(
                'order_ratings.id',
                'order_ratings.rating',
                'order_ratings.comment',
                'order_ratings.created_at',
                'users.name as customer_name',
                'orders.id as order_id'
            )
            ->orderBy('order_ratings.created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'customer_name' => $rating->customer_name,
                    'order_id' => $rating->order_id,
                    'date' => Carbon::parse($rating->created_at)->format('Y-m-d H:i'),
                    'time_ago' => Carbon::parse($rating->created_at)->diffForHumans(),
                ];
            });
    }

    private function getAvailableOrders(Driver $driver)
    {
        // جلب الطلبات المتاحة بالقرب من السائق
        if (!$driver->current_latitude || !$driver->current_longitude) {
            return [];
        }

        // الطلبات المعلقة بدون سائق
        $orders = Order::whereNull('driver_id')
            ->where('order_status_id', 1) // معلق
            ->whereDoesntHave('offers', function ($query) use ($driver) {
                $query->where('driver_id', $driver->id);
            })
            ->with(['user:id,name', 'service:id,name', 'location'])
            ->latest()
            ->take(3)
            ->get();

        // حساب المسافة لكل طلب
        return $orders->map(function ($order) use ($driver) {
            $distance = null;
            if ($order->location && $driver->current_latitude && $driver->current_longitude) {
                $distance = $this->calculateDistance(
                    $driver->current_latitude,
                    $driver->current_longitude,
                    $order->location->latitude,
                    $order->location->longitude
                );
            }

            return [
                'id' => $order->id,
                'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'service' => $order->service->name,
                'customer' => $order->user->name,
                'price_range' => $order->service->price_range,
                'distance_km' => $distance ? round($distance['distance_km'], 1) : null,
                'estimated_time_minutes' => $distance ? ceil($distance['estimated_time_minutes']) : null,
                'created_at' => $order->created_at->format('H:i'),
                'time_ago' => $order->created_at->diffForHumans(),
            ];
        });
    }

    private function getAlerts(Driver $driver)
    {
        $alerts = [];

        // تحقق من المستندات المنتهية
        if ($driver->has_expired_documents) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'مستندات منتهية الصلاحية',
                'message' => 'بعض مستنداتك منتهية الصلاحية. يرجى تحديثها.',
                'action' => 'update_documents',
                'priority' => 'high',
            ];
        }

        // تحقق من تقييم منخفض
        if ($driver->average_rating < 3.5) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'تحسين التقييم',
                'message' => 'تقييمك منخفض. يرجى تحسين خدمتك للحصول على المزيد من الطلبات.',
                'action' => 'improve_service',
                'priority' => 'medium',
            ];
        }

        // تحقق من رصيد المحفظة
        $wallet = $driver->wallet;
        if ($wallet && $wallet->available_balance >= 1000) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'رصيد قابل للسحب',
                'message' => 'لديك رصيد قابل للسحب. يمكنك سحبه الآن.',
                'action' => 'withdraw',
                'priority' => 'low',
            ];
        }

        return $alerts;
    }

    private function getDashboardSummary(Driver $driver)
    {
        return [
            'online_hours' => $this->calculateOnlineHours($driver),
            'acceptance_rate' => $this->calculateAcceptanceRate($driver),
            'completion_rate' => $driver->completion_rate,
            'customer_satisfaction' => $this->calculateCustomerSatisfaction($driver),
            'average_earnings_per_hour' => $this->calculateAverageEarningsPerHour($driver),
            'peak_hours' => $this->getPeakHours($driver),
        ];
    }

    private function getOverviewStats(Driver $driver, $period)
    {
        list($start, $end) = $this->getDateRange($period);

        return [
            'orders' => [
                'total' => Order::where('driver_id', $driver->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
                'completed' => Order::where('driver_id', $driver->id)
                    ->where('order_status_id', 4)
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
                'cancelled' => Order::where('driver_id', $driver->id)
                    ->whereIn('order_status_id', [5, 6])
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
                'average_delivery_time' => $this->calculateAverageDeliveryTime($driver, $start, $end),
            ],
            'earnings' => [
                'total' => Order::where('driver_id', $driver->id)
                    ->where('order_status_id', 4)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('price'),
                'average_per_order' => $this->calculateAveragePerOrder($driver, $start, $end),
                'tips' => $this->getTipsAmount($driver, $start, $end),
                'bonuses' => $this->getBonusesAmount($driver, $start, $end),
            ],
            'ratings' => [
                'average' => $this->calculatePeriodRating($driver, $start, $end),
                'total_reviews' => DB::table('order_ratings')
                    ->where('driver_id', $driver->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->count(),
                'distribution' => $this->getRatingDistribution($driver, $start, $end),
            ],
        ];
    }

    private function getPerformanceStats(Driver $driver, $period)
    {
        list($start, $end) = $this->getDateRange($period);

        return [
            'acceptance_rate' => $this->calculateAcceptanceRate($driver, $start, $end),
            'completion_rate' => $this->calculateCompletionRate($driver, $start, $end),
            'on_time_rate' => $this->calculateOnTimeRate($driver, $start, $end),
            'response_time' => $this->calculateResponseTime($driver, $start, $end),
            'distance_traveled' => $this->calculateDistanceTraveled($driver, $start, $end),
        ];
    }

    private function getEarningsBreakdown(Driver $driver, $period)
    {
        list($start, $end) = $this->getDateRange($period);

        return [
            'by_service' => $this->getEarningsByService($driver, $start, $end),
            'by_day' => $this->getEarningsByDay($driver, $start, $end),
            'by_hour' => $this->getEarningsByHour($driver, $start, $end),
            'by_payment_method' => $this->getEarningsByPaymentMethod($driver, $start, $end),
        ];
    }

    private function getOrderAnalysis(Driver $driver, $period)
    {
        list($start, $end) = $this->getDateRange($period);

        return [
            'order_volume_trend' => $this->getOrderVolumeTrend($driver, $start, $end),
            'order_value_trend' => $this->getOrderValueTrend($driver, $start, $end),
            'peak_periods' => $this->getPeakPeriods($driver, $start, $end),
            'repeat_customers' => $this->getRepeatCustomers($driver, $start, $end),
        ];
    }

    private function getComparisonStats(Driver $driver, $period)
    {
        // مقارنة مع متوسط السائقين في المنطقة
        return [
            'vs_average' => [
                'earnings' => $this->compareWithAverageEarnings($driver, $period),
                'rating' => $this->compareWithAverageRating($driver, $period),
                'acceptance_rate' => $this->compareWithAverageAcceptanceRate($driver, $period),
                'completion_rate' => $this->compareWithAverageCompletionRate($driver, $period),
            ],
            'rank' => $this->getDriverRank($driver, $period),
            'percentile' => $this->getDriverPercentile($driver, $period),
        ];
    }

    private function getDateRange($period)
    {
        switch ($period) {
            case 'today':
                return [Carbon::today(), Carbon::tomorrow()->subSecond()];
            case 'yesterday':
                return [Carbon::yesterday(), Carbon::today()->subSecond()];
            case 'week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'last_week':
                return [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()];
            case 'month':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'last_month':
                return [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];
            case 'year':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()];
            default:
                return [Carbon::now()->subDays(30), Carbon::now()];
        }
    }

    private function getDetailedEarnings(Driver $driver, $start, $end)
    {
        // كود مفصل لحساب الأرباح...
        // هذا مثال مبسط
        return [
            'summary' => [
                'total_earnings' => 5000,
                'total_orders' => 50,
                'average_per_order' => 100,
                'net_earnings' => 4500,
            ],
            'transactions' => collect([]),
            'daily_breakdown' => [],
            'payment_methods' => [],
            'withdrawals' => [],
            'taxes_and_fees' => [],
        ];
    }

    private function getPerformanceReport(Driver $driver, $start, $end)
    {
        // تقرير الأداء
        return [
            'key_metrics' => [],
            'trends' => [],
            'recommendations' => [],
        ];
    }

    private function getFinancialReport(Driver $driver, $start, $end)
    {
        // تقرير مالي
        return [
            'income_statement' => [],
            'expense_breakdown' => [],
            'profit_margin' => 0,
        ];
    }

    private function getCustomerFeedback(Driver $driver, $start, $end)
    {
        // تعليقات العملاء
        return [
            'sentiment_analysis' => [],
            'common_complaints' => [],
            'common_praises' => [],
        ];
    }

    private function getPeerComparison(Driver $driver, $start, $end)
    {
        // مقارنة مع الأقران
        return [
            'ranking' => [],
            'benchmarks' => [],
        ];
    }

    private function getInsights(Driver $driver, $start, $end)
    {
        // رؤى وتحليلات
        return [
            'best_practices' => [],
            'growth_opportunities' => [],
        ];
    }

    // ========== دوال الحسابات ==========

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        // حساب المسافة باستخدام Haversine formula
        $earthRadius = 6371; // كم

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $latDelta = $lat2 - $lat1;
        $lngDelta = $lng2 - $lng1;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($lat1) * cos($lat2) * pow(sin($lngDelta / 2), 2)));

        $distanceKm = $angle * $earthRadius;

        // حساب الوقت التقريبي (بافتراض سرعة 40 كم/ساعة)
        $estimatedTimeMinutes = ($distanceKm / 40) * 60;

        return [
            'distance_km' => $distanceKm,
            'estimated_time_minutes' => $estimatedTimeMinutes,
        ];
    }

    private function calculateAcceptanceRate(Driver $driver, $start = null, $end = null)
    {
        $query = Order::where('driver_id', $driver->id);

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $totalOrders = $query->count();
        $acceptedOrders = $query->where('order_status_id', '>=', 2)->count();

        return $totalOrders > 0 ? round(($acceptedOrders / $totalOrders) * 100, 1) : 0;
    }

    private function calculateCompletionRate(Driver $driver, $start = null, $end = null)
    {
        $query = Order::where('driver_id', $driver->id)
            ->where('order_status_id', '>=', 2); // المقبولة فما فوق

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $acceptedOrders = $query->count();
        $completedOrders = $query->where('order_status_id', 4)->count();

        return $acceptedOrders > 0 ? round(($completedOrders / $acceptedOrders) * 100, 1) : 0;
    }

    private function getTipsAmount(Driver $driver, $start, $end)
    {
        // افتراضي - يجب ربطه بنظام المدفوعات الفعلي
        return 0;
    }

    private function getBonusesAmount(Driver $driver, $start, $end)
    {
        // افتراضي - يجب ربطه بنظام المكافآت الفعلي
        return 0;
    }

    private function calculateAverageDailyEarnings(Driver $driver, $start, $end)
    {
        $totalDays = $start->diffInDays($end) + 1;
        $totalEarnings = Order::where('driver_id', $driver->id)
            ->where('order_status_id', 4)
            ->whereBetween('created_at', [$start, $end])
            ->sum('price');

        return $totalDays > 0 ? round($totalEarnings / $totalDays, 2) : 0;
    }

    private function projectWeeklyEarnings(Driver $driver)
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();

        $earningsSoFar = Order::where('driver_id', $driver->id)
            ->where('order_status_id', 4)
            ->whereBetween('created_at', [$weekStart, $today])
            ->sum('price');

        $daysPassed = $today->diffInDays($weekStart) + 1;

        return $daysPassed > 0 ? round(($earningsSoFar / $daysPassed) * 7, 2) : 0;
    }

    // ... المزيد من دوال الحسابات حسب الحاجة
}
