<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Order;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Flasher\Toastr\Laravel\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('permission:عرض الإدمن', ['only' => ['index']]);
    //     $this->middleware('permission:إضافة الإدمن', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:تعديل الإدمن', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:حذف الإدمن', ['only' => ['destroy']]);
    // }

    /**
     * Get validation rules for admin creation/update
     */
    protected function getValidationRules($adminId = null)
    {
        $emailRule = 'required|string|email|max:255|unique:admins,email';
        if ($adminId) {
            $emailRule .= ',' . $adminId;
        }

        return [
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'password' => $adminId ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    /**
     * Get validation error messages in Arabic
     */
    protected function getValidationMessages()
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'name.max' => 'يجب ألا يتجاوز الاسم 255 حرف.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'email.email' => 'البريد الإلكتروني غير صحيح.',
            'email.max' => 'يجب ألا يتجاوز البريد الإلكتروني 255 حرف.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
            'role.required' => 'الدور مطلوب.',
            'role.exists' => 'الدور المحدد غير موجود.',
            'phone.max' => 'يجب ألا يتجاوز رقم الهاتف 20 رقم.',
            'avatar.image' => 'الملف يجب أن يكون صورة.',
            'avatar.mimes' => 'صيغ الصور المدعومة: jpeg, png, jpg, webp.',
            'avatar.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',
        ];
    }

    /**
     * Display a listing of the admins.
     */
    public function index()
    {
        $admins = Admin::with('roles')->get();
        $roles = Role::orderBy('name')->get();

        return view('Admin.admin.index', compact('admins', 'roles'));
    }

    /**
     * Display the dashboard home page.
     */
    public function home()
    {

        // ---------------------------------------
        // زيارات آخر 10 أيام
        // ---------------------------------------
        $visits = Visitor::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(10))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $topCustomers = \App\Models\Order::select('user_id')
            ->selectRaw('COUNT(*) as orders_count')
            ->groupBy('user_id')
            ->orderByDesc('orders_count')
            ->take(10)
            ->get();

        $visitsLabels = [];
        $visitsData = [];

        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $visitsLabels[] = Carbon::now()->subDays($i)->format('d M');
            $visitsData[] = $visits[$date] ?? 0;
        }

        // ---------------------------------------
        // إحصائيات الزيارات حسب الشهر واليوم للرئيسية
        // ---------------------------------------
        $visitorYears = range(Carbon::now()->year, Carbon::now()->year - 5);
        $visitorMonths = [];

        for ($month = 1; $month <= 12; $month++) {
            $visitorMonths[$month] = Carbon::create(null, $month, 1)->locale('ar')->translatedFormat('F');
        }

        $monthlyVisits = Visitor::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total, COUNT(DISTINCT ip) as unique_total')
            ->whereYear('created_at', '>=', min($visitorYears))
            ->whereYear('created_at', '<=', max($visitorYears))
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn($row) => $row->year . '-' . $row->month);

        $dailyVisits = Visitor::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, DAY(created_at) as day, COUNT(*) as total, COUNT(DISTINCT ip) as unique_total')
            ->whereYear('created_at', '>=', min($visitorYears))
            ->whereYear('created_at', '<=', max($visitorYears))
            ->groupBy('year', 'month', 'day')
            ->get()
            ->keyBy(fn($row) => $row->year . '-' . $row->month . '-' . $row->day);

        $visitorChartData = [
            'years' => $visitorYears,
            'months' => $visitorMonths,
            'monthly' => [],
            'daily' => [],
        ];

        foreach ($visitorYears as $year) {
            $visitorChartData['monthly'][$year] = [
                'labels' => array_values($visitorMonths),
                'visits' => [],
                'unique_visitors' => [],
            ];

            for ($month = 1; $month <= 12; $month++) {
                $monthRow = $monthlyVisits->get($year . '-' . $month);
                $visitorChartData['monthly'][$year]['visits'][] = (int) ($monthRow->total ?? 0);
                $visitorChartData['monthly'][$year]['unique_visitors'][] = (int) ($monthRow->unique_total ?? 0);

                $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
                $visitorChartData['daily'][$year][$month] = [
                    'labels' => [],
                    'visits' => [],
                    'unique_visitors' => [],
                ];

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $dayRow = $dailyVisits->get($year . '-' . $month . '-' . $day);
                    $visitorChartData['daily'][$year][$month]['labels'][] = (string) $day;
                    $visitorChartData['daily'][$year][$month]['visits'][] = (int) ($dayRow->total ?? 0);
                    $visitorChartData['daily'][$year][$month]['unique_visitors'][] = (int) ($dayRow->unique_total ?? 0);
                }
            }
        }

        // ---------------------------------------
        // الدول الأكثر زيارة (Top Countries)
        // ---------------------------------------
        $countriesData = Visitor::selectRaw('country, COUNT(*) as count')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(6)
            ->pluck('count', 'country')
            ->toArray();

        // ---------------------------------------
        // حالة الطلبات (Orders Status)
        // ---------------------------------------
        $ordersStatus = \App\Models\Order::selectRaw('order_status_id, COUNT(*) as count')
            ->with(['status:id,name'])
            ->groupBy('order_status_id')
            ->get()
            ->map(function ($order) {
                return [
                    'status_name' => $order->status->name ?? 'Unknown',
                    'status_id' => $order->order_status_id,
                    'count' => $order->count,
                ];
            })
            ->toArray();

        // إحصائيات البيانات
        $totalOrders = Order::count();
        $totalCustomers = User::count();
        $totalStaff = Admin::count();
        $totalVisits = Visitor::count();
        $thisMonthVisits = Visitor::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $thisMonthOrders = Order::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $thisMonthCustomers = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $cancelledOrders = Order::where('order_status_id', 6)->count();

        // ---------------------------------------
        // إرجاع البيانات للصفحة
        // ---------------------------------------
        return view('Admin.index', compact(
            'visitsLabels',
            'visitsData',
            'countriesData',
            'ordersStatus',
            'topCustomers',
            'totalOrders',
            'totalCustomers',
            'totalStaff',
            'totalVisits',
            'thisMonthVisits',
            'thisMonthOrders',
            'thisMonthCustomers',
            'cancelledOrders',
            'visitorYears',
            'visitorMonths',
            'visitorChartData'
        ));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        $roles = Role::all();

        return view('Admin.admin.create', compact('roles'));
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate(
                $this->getValidationRules(),
                $this->getValidationMessages()
            );

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $validated['avatar'] = $request->file('avatar')->store('admins', 'public');
            }

            // Prepare admin data
            $adminData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'phone' => $validated['phone'] ?? null,
                'avatar' => $validated['avatar'] ?? null,
            ];

            // Create admin
            $admin = Admin::create($adminData);

            // Assign role using Spatie
            $admin->assignRole($validated['role']);

            // Log activity
            Log::info('Admin created:', [
                'admin_id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => $validated['role']
            ]);

            // Success notification


            return redirect()
                ->route('admin.admins.index')
                ->with('success', 'تم إضافة المسؤول بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled by Laravel
            throw $e;
        } catch (\Exception $e) {
            // Log error
            Log::error('فشل إنشاء المسؤول: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);



            return back()
                ->withInput()
                ->with('error', 'فشل إنشاء المسؤول: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified admin.
     */
    public function show(Admin $admin)
    {
        $admin->load('roles', 'permissions');

        return view('Admin.admin.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(Admin $admin)
    {
        $roles = Role::all();

        return view('Admin.admin.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        try {
            $validated = $request->validate(
                $this->getValidationRules($admin->id),
                $this->getValidationMessages()
            );

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($admin->avatar) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($admin->avatar);
                }
                $validated['avatar'] = $request->file('avatar')->store('admins', 'public');
            }

            // Handle avatar removal
            if ($request->has('remove_avatar') && $request->remove_avatar) {
                if ($admin->avatar) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($admin->avatar);
                }
                $validated['avatar'] = null;
            }

            // Prepare update data
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $admin->phone,
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = $validated['password'];
            }

            // Only update avatar if changed
            if (isset($validated['avatar'])) {
                $updateData['avatar'] = $validated['avatar'];
            }

            // Update admin
            $admin->update($updateData);

            // Sync roles
            $admin->syncRoles($validated['role']);

            // Log activity
            Log::info('Admin updated:', [
                'admin_id' => $admin->id,
                'name' => $admin->name,
                'role' => $validated['role']
            ]);

            return redirect()
                ->route('admin.admins.index')
                ->with('success', 'تم تحديث المسؤول بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('فشل تحديث المسؤول: ' . $e->getMessage(), [
                'admin_id' => $admin->id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(Admin $admin)
    {
        try {
            // Prevent deleting yourself
            if (auth()->guard('admin')->id() === $admin->id) {
                return redirect()->back();
            }

            // Prevent deleting super admin if you're not super admin
            if ($admin->hasRole('super_admin') && !auth()->guard('admin')->user()->hasRole('super_admin')) {
                return redirect()->back();
            }

            // Delete avatar if exists
            if ($admin->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($admin->avatar);
            }

            $adminName = $admin->name;
            $admin->delete();

            // Log activity
            Log::info('Admin deleted:', [
                'admin_id' => $admin->id,
                'name' => $adminName,
                'deleted_by' => auth()->guard('admin')->id()
            ]);


            return redirect()
                ->route('admin.admins.index')
                ->with('success', 'تم حذف المسؤول بنجاح.');
        } catch (\Exception $e) {
            Log::error('فشل حذف المسؤول: ' . $e->getMessage());


            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle admin status (activate/deactivate).
     */
    public function toggleStatus(Admin $admin)
    {
        try {
            // Prevent deactivating yourself
            if (auth()->guard('admin')->id() === $admin->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكنك تعطيل حسابك الخاص'
                ], 403);
            }

            $admin->is_active = !$admin->is_active;
            $admin->save();

            return response()->json([
                'success' => true,
                'is_active' => $admin->is_active,
                'message' => $admin->is_active ? 'تم تفعيل المسؤول' : 'تم تعطيل المسؤول'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير الحالة'
            ], 500);
        }
    }

    /**
     * Bulk actions (delete, activate, deactivate).
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'ids' => 'required|array',
            'ids.*' => 'exists:admins,id',
        ]);

        try {
            $ids = $request->ids;
            $currentAdminId = auth()->guard('admin')->id();

            // Remove current admin from IDs to prevent self-action
            $ids = array_filter($ids, function ($id) use ($currentAdminId) {
                return $id != $currentAdminId;
            });

            if (empty($ids)) {
                return back()->with('warning', 'لا يمكنك تنفيذ هذا الإجراء على حسابك الخاص');
            }

            $action = $request->action;
            $count = count($ids);

            switch ($action) {
                case 'delete':
                    // Delete avatars
                    $admins = Admin::whereIn('id', $ids)->get();
                    foreach ($admins as $admin) {
                        if ($admin->avatar) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($admin->avatar);
                        }
                    }
                    Admin::whereIn('id', $ids)->delete();
                    $message = "تم حذف {$count} مسؤول بنجاح";
                    break;

                case 'activate':
                    Admin::whereIn('id', $ids)->update(['is_active' => true]);
                    $message = "تم تفعيل {$count} مسؤول بنجاح";
                    break;

                case 'deactivate':
                    Admin::whereIn('id', $ids)->update(['is_active' => false]);
                    $message = "تم تعطيل {$count} مسؤول بنجاح";
                    break;

                default:
                    return back()->with('error', 'إجراء غير معروف');
            }


            return redirect()
                ->route('admin.admins.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('فشل الإجراء الجماعي: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }
}
