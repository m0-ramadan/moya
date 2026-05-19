<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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
            ->with(['user:id,name,image'])
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
        $ordersStatus = [];

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
            toastr()->success('تم إضافة المسؤول بنجاح', 'نجاح');

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

            // Error notification
            toastr()->error('حدث خطأ أثناء إضافة المسؤول', 'خطأ');

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

            // Success notification
            toastr()->success('تم تحديث المسؤول بنجاح', 'نجاح');

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

            toastr()->error('حدث خطأ أثناء تحديث المسؤول', 'خطأ');

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
                toastr()->warning('لا يمكنك حذف حسابك الخاص', 'تحذير');
                return redirect()->back();
            }

            // Prevent deleting super admin if you're not super admin
            if ($admin->hasRole('super_admin') && !auth()->guard('admin')->user()->hasRole('super_admin')) {
                toastr()->error('لا يمكنك حذف السوبر أدمن', 'خطأ');
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

            toastr()->success('تم حذف المسؤول بنجاح', 'نجاح');

            return redirect()
                ->route('admin.admins.index')
                ->with('success', 'تم حذف المسؤول بنجاح.');

        } catch (\Exception $e) {
            Log::error('فشل حذف المسؤول: ' . $e->getMessage());

            toastr()->error('حدث خطأ أثناء حذف المسؤول', 'خطأ');

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
            $ids = array_filter($ids, function($id) use ($currentAdminId) {
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

            toastr()->success($message, 'نجاح');

            return redirect()
                ->route('admin.admins.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('فشل الإجراء الجماعي: ' . $e->getMessage());
            toastr()->error('حدث خطأ أثناء تنفيذ الإجراء', 'خطأ');
            return back()->with('error', $e->getMessage());
        }
    }
}