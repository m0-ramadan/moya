<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Branchs;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Flasher\Toastr\Laravel\Facade\Toastr;

class AdminController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('permission:عرض الإدمن', ['only' => ['index']]);
    //     $this->middleware('permission:إضافة الإدمن', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:تعديل الإدمن', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:حذف الإدمن', ['only' => ['destroy']]);
    // }

    public function home()
    {
        $admins = Admin::all();

        return view('Admin.index', compact('admins', 'topCustomers'));
    }
    public function index()
    {
        // ---------------------------------------
        // زيارات آخر 10 أيام
        // ---------------------------------------

        $visits = Visitor::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(10))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
        $topCustomers = \App\Models\Order::select('user_id')->selectRaw('COUNT(*) as orders_count')->with(['user:id,name'])->groupBy('user_id')->orderByDesc('orders_count')->take(10)->get();

        $visitsLabels = [];
        $visitsData = [];

        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $visitsLabels[] = Carbon::now()->subDays($i)->format('d M');
            $visitsData[] = $visits[$date] ?? 0;
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
        // dd( $countriesData);
        // ---------------------------------------
        // حالة الطلبات (Orders Status)
        // ---------------------------------------

        $ordersStatus = []; // You can uncomment and use your orders logic when ready

        // ---------------------------------------
        // إرجاع البيانات للصفحة
        // ---------------------------------------

        return view('Admin.index', compact(
            'visitsLabels',
            'visitsData',
            'countriesData',
            'ordersStatus',
            'topCustomers'
        ));
    }

    public function create()
    {
        //  $branches = Branchs::all();
        $roles = Role::all();

        return view('Admin.admin.create', compact('branches', 'roles'));
    }


    public function store(Request $request)
    {
        try {
            // التحقق من البيانات
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'branch_id' => 'nullable|exists:branchss,id', // Adjust table name if needed
                'email' => 'required|string|email|max:255|unique:admins,email',
                'password' => 'required|string|min:6',
                'role' => 'required',
            ], [
                'name.required' => 'الاسم مطلوب.',
                'email.required' => 'البريد الإلكتروني مطلوب.',
                'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
                'password.required' => 'كلمة المرور مطلوبة.',
                'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
                'role.required' => 'الدور مطلوب.',
                'role.exists' => 'الدور المحدد غير موجود.',
            ]);

            // إنشاء المسؤول
            $admin = Admin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'branch_id' => $validated['branch_id'],
                'password' => $validated['password'], // Handled by setPasswordAttribute
            ]);

            // Assign the role using Spatie
            $admin->assignRole($validated['role']);

            Log::info('Admin created:', ['admin_id' => $admin->id, 'role' => $validated['role']]);
            return redirect()->route('admin.admins.index')->with(['success' => 'تم إضافة المسؤول بنجاح']);
        } catch (\Exception $e) {
            Log::error('فشل إنشاء المسؤول: ' . $e->getMessage());
            toastr()->error('حدث خطأ أثناء إضافة المسؤول', 'خطأ');
            return back()->withInput()->with(['error' => 'فشل إنشاء المسؤول: ' . $e->getMessage()]);
        }
    }
    public function edit(Admin $admin)
    {
        // $branches = Branchs::all();
        $roles = Role::all();

        return view('Admin.admin.edit', compact('admin', 'branches', 'roles'));
    }

    public function update(Request $request, Admin $admin)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'branch_id' => 'nullable|exists:branchss,id', // Adjust table name if needed
                'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
                'password' => 'nullable|string|min:6',
                'role' => 'required|exists:roles,name',
            ], [
                'name.required' => 'الاسم مطلوب.',
                'email.required' => 'البريد الإلكتروني مطلوب.',
                'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
                'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
                'role.required' => 'الدور مطلوب.',
                'role.exists' => 'الدور المحدد غير موجود.',
            ]);

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'branch_id' => $validated['branch_id'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = $validated['password']; // Handled by setPasswordAttribute
            }

            $admin->update($updateData);
            $admin->syncRoles($validated['role']);

            return redirect()->route('admin.admins.index')->with(['success' => 'تم تحديث المسؤول بنجاح']);
        } catch (\Exception $e) {
            toastr()->error('حدث خطأ أثناء تحديث المسؤول', 'خطأ');
            return back()->withInput()->with(['error' =>  $e->getMessage()]);
        }
    }
    public function destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->back()->with(['success' => 'تم حذف المسؤول بنجاح.']);
    }
}
