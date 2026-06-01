<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildFilteredQuery($request);

        $totalCoupons = Coupon::count();
        $activeCoupons = Coupon::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->count();
        $inactiveCoupons = Coupon::where('is_active', false)->count();
        $expiredCoupons = Coupon::whereNotNull('expires_at')->where('expires_at', '<', now())->count();
        $percentageCoupons = Coupon::where('type', 'percentage')->count();
        $fixedCoupons = Coupon::where('type', 'fixed')->count();

        $coupons = $query->withCount('usages')->paginate(20)->appends($request->all());

        return view('Admin.coupons.index', compact(
            'coupons',
            'totalCoupons',
            'activeCoupons',
            'inactiveCoupons',
            'expiredCoupons',
            'percentageCoupons',
            'fixedCoupons'
        ));
    }

    public function create()
    {
        return view('Admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCoupon($request);
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active', true);

        Coupon::create($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'تم إنشاء الكوبون بنجاح');
    }

    public function show(Coupon $coupon)
    {
        $usages = $coupon->usages()
            ->with(['user', 'order'])
            ->latest()
            ->paginate(20);

        $usageStatistics = [
            'total' => $coupon->usages()->count(),
            'today' => $coupon->usages()->whereDate('created_at', today())->count(),
            'this_week' => $coupon->usages()->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),
            'this_month' => $coupon->usages()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('Admin.coupons.show', compact('coupon', 'usages', 'usageStatistics'));
    }

    public function edit(Coupon $coupon)
    {
        return view('Admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coreFields = ['code', 'name', 'type', 'value'];
        $isStatusOnlyUpdate = ! $request->hasAny($coreFields) && $request->has('is_active');

        if ($isStatusOnlyUpdate) {
            $coupon->update([
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()
                ->route('admin.coupons.show', $coupon)
                ->with('success', 'تم تحديث حالة الكوبون بنجاح');
        }

        $validated = $this->validateCoupon($request, $coupon);
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');

        $coupon->update($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'تم تحديث الكوبون بنجاح');
    }

    public function destroy(Request $request, Coupon $coupon)
    {
        $coupon->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => 'تم حذف الكوبون بنجاح',
            ]);
        }

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'تم حذف الكوبون بنجاح');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'coupon_ids' => 'required|array',
            'coupon_ids.*' => 'exists:coupons,id',
        ]);

        $couponIds = $request->coupon_ids;

        switch ($request->action) {
            case 'activate':
                Coupon::whereIn('id', $couponIds)->update(['is_active' => true]);
                $message = 'تم تفعيل الكوبونات المحددة';
                break;
            case 'deactivate':
                Coupon::whereIn('id', $couponIds)->update(['is_active' => false]);
                $message = 'تم تعطيل الكوبونات المحددة';
                break;
            default:
                Coupon::whereIn('id', $couponIds)->delete();
                $message = 'تم حذف الكوبونات المحددة';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function duplicate(Request $request, Coupon $coupon)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
        ]);

        $baseCode = $coupon->code . '-' . strtoupper(Str::random(4));

        $copy = $coupon->replicate();
        $copy->name = $request->input('name', $coupon->name . ' - نسخة');
        $copy->code = $this->generateUniqueCode($baseCode);
        $copy->is_active = false;
        $copy->push();

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء نسخة من الكوبون بنجاح',
            'data' => [
                'id' => $copy->id,
                'code' => $copy->code,
            ],
        ]);
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'csv');
        $coupons = $this->buildFilteredQuery($request)->withCount('usages')->get();
        $filename = 'coupons-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->streamDownload(function () use ($coupons) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Code',
                'Name',
                'Type',
                'Value',
                'Min Order Amount',
                'Max Uses',
                'Max Uses Per User',
                'Usage Count',
                'Is Active',
                'Starts At',
                'Expires At',
                'Created At',
            ]);

            foreach ($coupons as $coupon) {
                fputcsv($handle, [
                    $coupon->code,
                    $coupon->name,
                    $coupon->type,
                    $coupon->value,
                    $coupon->min_order_amount,
                    $coupon->max_uses,
                    $coupon->max_uses_per_user,
                    $coupon->usages_count,
                    $coupon->is_active ? 'Yes' : 'No',
                    optional($coupon->starts_at)->format('Y-m-d H:i:s'),
                    optional($coupon->expires_at)->format('Y-m-d H:i:s'),
                    optional($coupon->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }

    public function generateCode()
    {
        return response()->json([
            'success' => true,
            'code' => $this->generateUniqueCode(),
        ]);
    }

    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'except' => 'nullable|exists:coupons,id',
        ]);

        $exists = Coupon::query()
            ->code($request->code)
            ->when($request->except, function ($q) use ($request) {
                $q->where('id', '!=', $request->except);
            })
            ->exists();

        return response()->json([
            'valid' => ! $exists,
            'message' => $exists ? 'الكود مستخدم بالفعل' : 'الكود متاح',
        ]);
    }

    private function validateCoupon(Request $request, ?Coupon $coupon = null): array
    {
        $couponId = $coupon?->id;

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $couponId,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($validated['type'] === 'percentage' && (float) $validated['value'] > 100) {
            throw ValidationException::withMessages([
                'value' => 'لا يمكن أن تتجاوز نسبة الخصم 100%.',
            ]);
        }

        return $validated;
    }

    private function buildFilteredQuery(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                    });
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->whereNotNull('expires_at')->where('expires_at', '<', now());
            }
        }

        if ($request->filled('starts_from')) {
            $query->whereDate('starts_at', '>=', $request->starts_from);
        }

        if ($request->filled('starts_to')) {
            $query->whereDate('starts_at', '<=', $request->starts_to);
        }

        if ($request->filled('expires_from')) {
            $query->whereDate('expires_at', '>=', $request->expires_from);
        }

        if ($request->filled('expires_to')) {
            $query->whereDate('expires_at', '<=', $request->expires_to);
        }

        if ($request->filled('value_from')) {
            $query->where('value', '>=', $request->value_from);
        }

        if ($request->filled('value_to')) {
            $query->where('value', '<=', $request->value_to);
        }

        if ($request->filled('min_order_amount')) {
            $query->where('min_order_amount', '>=', $request->min_order_amount);
        }

        if ($request->filled('max_uses')) {
            $query->where('max_uses', '<=', $request->max_uses);
        }

        if ($request->filled('max_uses_per_user')) {
            $query->where('max_uses_per_user', '<=', $request->max_uses_per_user);
        }

        $allowedSorts = ['created_at', 'starts_at', 'expires_at', 'value', 'code', 'name'];
        $orderBy = in_array($request->get('order_by'), $allowedSorts, true)
            ? $request->get('order_by')
            : 'created_at';
        $orderDir = $request->get('order_dir') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($orderBy, $orderDir);
    }

    private function generateUniqueCode(?string $preferred = null): string
    {
        $code = $preferred ?: strtoupper(Str::random(8));

        while (Coupon::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }

        return $code;
    }
}
