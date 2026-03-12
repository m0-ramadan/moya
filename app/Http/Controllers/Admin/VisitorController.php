<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitorController extends Controller
{
    /**
     * Display a listing of the visitors with statistics.
     */
    public function index(Request $request)
    {
        // إحصائيات عامة
        $stats = [
            'total_visitors' => Visitor::count(),
            'unique_visitors' => Visitor::distinct('ip')->count('ip'),
            'today_visitors' => Visitor::whereDate('created_at', Carbon::today())->count(),
            'unique_today' => Visitor::whereDate('created_at', Carbon::today())->distinct('ip')->count('ip'),
            'this_week' => Visitor::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'this_month' => Visitor::whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        // إحصائيات الأجهزة
        $deviceStats = [
            'desktop' => Visitor::where('is_desktop', true)->count(),
            'mobile' => Visitor::where('is_mobile', true)->count(),
            'tablet' => Visitor::where('is_tablet', true)->count(),
            'bot' => Visitor::where('is_bot', true)->count(),
        ];

        // إحصائيات المتصفحات
        $browserStats = Visitor::select('browser', DB::raw('count(*) as total'))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // إحصائيات أنظمة التشغيل
        $platformStats = Visitor::select('platform', DB::raw('count(*) as total'))
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // إحصائيات الدول
        $countryStats = Visitor::select('country', DB::raw('count(*) as total'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('total', 'desc')
            ->limit(20)
            ->get();

        // إحصائيات المسارات الأكثر زيارة
        $pathStats = Visitor::select('path', DB::raw('count(*) as total'))
            ->groupBy('path')
            ->orderBy('total', 'desc')
            ->limit(20)
            ->get();

        // الزيارات اليومية لآخر 30 يوم
$dailyVisits = Visitor::select(
        DB::raw('DATE(created_at) as date'),
        DB::raw('count(*) as total'),
        DB::raw('count(DISTINCT ip) as unique_visitors')
    )
    ->where('created_at', '>=', Carbon::now()->subDays(30))
    ->groupBy(DB::raw('DATE(created_at)'))
    ->orderBy('date', 'desc')
    ->get();

        // آخر الزوار
        $latestVisitors = Visitor::latest()
            ->take(20)
            ->get();

        // تصفية الزوار
        $visitorsQuery = Visitor::query();

        if ($request->has('search')) {
            $search = $request->search;
            $visitorsQuery->where(function($q) use ($search) {
                $q->where('ip', 'like', "%{$search}%")
                  ->orWhere('path', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('browser', 'like', "%{$search}%")
                  ->orWhere('platform', 'like', "%{$search}%");
            });
        }

        if ($request->has('device')) {
            switch ($request->device) {
                case 'desktop':
                    $visitorsQuery->where('is_desktop', true);
                    break;
                case 'mobile':
                    $visitorsQuery->where('is_mobile', true);
                    break;
                case 'tablet':
                    $visitorsQuery->where('is_tablet', true);
                    break;
                case 'bot':
                    $visitorsQuery->where('is_bot', true);
                    break;
            }
        }

        if ($request->has('country') && $request->country) {
            $visitorsQuery->where('country', $request->country);
        }

        if ($request->has('date_from') && $request->date_from) {
            $visitorsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $visitorsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $visitors = $visitorsQuery->latest()->paginate(50)->withQueryString();

        return view('Admin.visitors.index', compact(
            'stats',
            'deviceStats',
            'browserStats',
            'platformStats',
            'countryStats',
            'pathStats',
            'dailyVisits',
            'latestVisitors',
            'visitors'
        ));
    }

    /**
     * Get visitor details.
     */
    public function show($id)
    {
        $visitor = Visitor::findOrFail($id);
        return response()->json($visitor);
    }

    /**
     * Get statistics API.
     */
    public function apiStats(Request $request)
    {
        $query = Visitor::query();

        // فلترة حسب التاريخ
        if ($request->has('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', Carbon::now()->month);
                    break;
                case 'year':
                    $query->whereYear('created_at', Carbon::now()->year);
                    break;
            }
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
        }

        $stats = [
            'total' => $query->count(),
            'unique' => (clone $query)->distinct('ip')->count('ip'),
            'devices' => [
                'desktop' => (clone $query)->where('is_desktop', true)->count(),
                'mobile' => (clone $query)->where('is_mobile', true)->count(),
                'tablet' => (clone $query)->where('is_tablet', true)->count(),
                'bot' => (clone $query)->where('is_bot', true)->count(),
            ],
            'browsers' => (clone $query)
                ->select('browser', DB::raw('count(*) as total'))
                ->whereNotNull('browser')
                ->groupBy('browser')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'platforms' => (clone $query)
                ->select('platform', DB::raw('count(*) as total'))
                ->whereNotNull('platform')
                ->groupBy('platform')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'countries' => (clone $query)
                ->select('country', DB::raw('count(*) as total'))
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderBy('total', 'desc')
                ->limit(20)
                ->get(),
            'paths' => (clone $query)
                ->select('path', DB::raw('count(*) as total'))
                ->groupBy('path')
                ->orderBy('total', 'desc')
                ->limit(20)
                ->get(),
        ];

        // بيانات للرسم البياني
        $chartData = Visitor::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw('count(DISTINCT ip) as unique')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'chart' => $chartData
        ]);
    }

    /**
     * Clear old visitors data.
     */
    public function clearOld(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1'
        ]);

        $date = Carbon::now()->subDays($request->days);
        $deleted = Visitor::where('created_at', '<', $date)->delete();

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$deleted} زيارة قديمة",
            'deleted' => $deleted
        ]);
    }

    /**
     * Export visitors data.
     */
    public function export(Request $request)
    {
        $query = Visitor::query();

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $visitors = $query->get();

        $filename = 'visitors_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // رؤوس الأعمدة
        fputcsv($handle, [
            'ID', 'IP', 'Path', 'Method', 'Country', 'City', 
            'Browser', 'Platform', 'Device', 'Created At'
        ]);

        // البيانات
        foreach ($visitors as $visitor) {
            $device = 'Desktop';
            if ($visitor->is_mobile) $device = 'Mobile';
            if ($visitor->is_tablet) $device = 'Tablet';
            if ($visitor->is_bot) $device = 'Bot';

            fputcsv($handle, [
                $visitor->id,
                $visitor->ip,
                $visitor->path,
                $visitor->method,
                $visitor->country,
                $visitor->city,
                $visitor->browser,
                $visitor->platform,
                $device,
                $visitor->created_at
            ]);
        }

        fclose($handle);
        exit;
    }
}