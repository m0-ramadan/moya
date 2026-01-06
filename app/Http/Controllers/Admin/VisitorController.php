<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Visitor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VisitorController extends Controller
{
     public function chartData(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);

        $data = Visitor::selectRaw('country, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('country')
            ->orderByDesc('total')
            ->take(10) // أشهر 10 دول فقط
            ->get();

        return response()->json([
            'countries' => $data->pluck('country'),
            'count' => $data->pluck('total')
        ]);
    }
    public function ordersStats($year)
{
    $data = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->whereYear('created_at', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    // تجهيز البيانات للشارت
    $months = range(1, 12);
    $result = [];

    foreach ($months as $m) {
        $result[] = [
            'month' => date("M", mktime(0, 0, 0, $m, 1)),
            'total' => (int)($data->firstWhere('month', $m)->total ?? 0),
        ];
    }

   // return response()->json($result);
}

}
