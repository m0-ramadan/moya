<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * عرض قائمة جميع الرتب
     */
    public function index(Request $request)
    {
        // التحقق من الصلاحية
        if (!auth()->guard('admin')->user()->can('roles.view')) {
            abort(403, 'غير مصرح لك بعرض الرتب');
        }

        $query = Role::withCount(['users', 'permissions']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $query->orderBy('created_at', 'desc')->paginate(15);

        // إحصائيات
        $stats = [
            'total' => Role::count(),
            'super_admins' => Role::where('name', 'super_admin')->count(),
            'admins' => Role::where('name', 'admin')->count(),
            'editors' => Role::where('name', 'editor')->count(),
            'viewers' => Role::where('name', 'viewer')->count(),
        ];

        return view('Admin.roles.index', compact('roles', 'stats'));
    }

    /**
     * عرض نموذج إنشاء رتبة جديدة
     */
    public function create()
    {
        if (!auth()->guard('admin')->user()->can('roles.create')) {
            abort(403, 'غير مصرح لك بإنشاء رتب');
        }

        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $modules = Permission::distinct()->pluck('module')->filter()->values();

        return view('Admin.roles.create', compact('permissions', 'modules'));
    }

    /**
     * حفظ رتبة جديدة
     */
    public function store(Request $request)
    {
        if (!auth()->guard('admin')->user()->can('roles.create')) {
            abort(403, 'غير مصرح لك بإنشاء رتب');
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
                'regex:/^[a-z_]+$/'
            ],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ], [
            'name.regex' => 'اسم الرتبة يجب أن يحتوي على أحرف صغيرة وشرطات سفلية فقط',
            'name.unique' => 'هذا الاسم مستخدم بالفعل لرتبة أخرى'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // إنشاء الرتبة
            $role = Role::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
            ]);

            // ربط الصلاحيات
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'تم إنشاء الرتبة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الرتبة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض تفاصيل رتبة معينة
     */
    public function show($id)
    {
        if (!auth()->guard('admin')->user()->can('roles.view')) {
            abort(403, 'غير مصرح لك بعرض الرتب');
        }

        $role = Role::with(['permissions', 'users' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }])->findOrFail($id);

        $permissionsByModule = $role->permissions->groupBy('module');

        return view('Admin.roles.show', compact('role', 'permissionsByModule'));
    }

    /**
     * عرض نموذج تعديل رتبة
     */
    public function edit($id)
    {
        if (!auth()->guard('admin')->user()->can('roles.edit')) {
            abort(403, 'غير مصرح لك بتعديل الرتب');
        }

        $role = Role::findOrFail($id);

        // منع تعديل الرتب المحمية
        if ($this->isProtectedRole($role)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن تعديل هذه الرتبة لأنها محمية');
        }

        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $modules = Permission::distinct()->pluck('module')->filter()->values();
        $selectedPermissions = $role->permissions->pluck('id')->toArray();

        return view('Admin.roles.edit', compact('role', 'permissions', 'modules', 'selectedPermissions'));
    }

    /**
     * تحديث الرتبة
     */
    public function update(Request $request, $id)
    {
        if (!auth()->guard('admin')->user()->can('roles.edit')) {
            abort(403, 'غير مصرح لك بتعديل الرتب');
        }

        $role = Role::findOrFail($id);

        // منع تعديل الرتب المحمية
        if ($this->isProtectedRole($role)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن تعديل هذه الرتبة لأنها محمية');
        }

        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // تحديث الرتبة
            $role->update([
                'display_name' => $request->display_name,
                'description' => $request->description,
            ]);

            // تحديث الصلاحيات
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'تم تحديث الرتبة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الرتبة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * حذف رتبة
     */
    public function destroy($id)
    {
        if (!auth()->guard('admin')->user()->can('delete_roles')) {
            abort(403, 'غير مصرح لك بحذف الرتب');
        }

        $role = Role::findOrFail($id);

        // منع حذف الرتب المحمية
        if ($this->isProtectedRole($role)) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف هذه الرتبة لأنها محمية'
            ], 403);
        }

        // التحقق من وجود مستخدمين مرتبطين بالرتبة
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الرتبة لأنها مرتبطة بمستخدمين'
            ], 400);
        }

        try {
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الرتبة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الرتبة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض صفحة إدارة صلاحيات الرتبة
     */
    public function permissions($id)
    {
        if (!auth()->guard('admin')->user()->can('roles.edit')) {
            abort(403, 'غير مصرح لك بتعديل صلاحيات الرتب');
        }

        $role = Role::findOrFail($id);

        // منع تعديل الرتب المحمية
        if ($this->isProtectedRole($role)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'لا يمكن تعديل صلاحيات هذه الرتبة لأنها محمية');
        }

        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $modules = Permission::distinct()->pluck('module')->filter()->values();
        $selectedPermissions = $role->permissions->pluck('id')->toArray();

        return view('Admin.roles.permissions', compact('role', 'permissions', 'modules', 'selectedPermissions'));
    }

    /**
     * مزامنة صلاحيات الرتبة
     */
    public function syncPermissions(Request $request, $id)
    {
        if (!auth()->guard('admin')->user()->can('roles.edit')) {
            abort(403, 'غير مصرح لك بتعديل صلاحيات الرتب');
        }

        $role = Role::findOrFail($id);

        // منع تعديل الرتب المحمية
        if ($this->isProtectedRole($role)) {
            return redirect()->back()
                ->with('error', 'لا يمكن تعديل صلاحيات هذه الرتبة لأنها محمية');
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            return redirect()->route('admin.roles.show', $role)
                ->with('success', 'تم تحديث صلاحيات الرتبة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الصلاحيات: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض صفحة تعيين الرتب للمستخدمين
     */
    public function assignIndex(Request $request)
    {
        if (!auth()->guard('admin')->user()->can('roles.assign')) {
            abort(403, 'غير مصرح لك بتعيين الرتب');
        }

        $admins = Admin::with('roles')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $roles = Role::orderBy('name')->get();

        return view('Admin.roles.assign', compact('admins', 'roles'));
    }

    /**
     * تعيين الرتب للمستخدمين
     */
    public function assignRoles(Request $request)
    {
        if (!auth()->guard('admin')->user()->can('assign_roles')) {
            abort(403, 'غير مصرح لك بتعيين الرتب');
        }

        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:admins,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $admin = Admin::findOrFail($request->admin_id);

            // منع إزالة دور المشرف الرئيسي إذا كان المستخدم هو المشرف الرئيسي الوحيد
            if ($admin->hasRole('super_admin')) {
                $superAdminCount = Admin::role('super_admin')->count();

                if ($superAdminCount <= 1 && !in_array('super_admin', $request->roles ?? [])) {
                    return redirect()->back()
                        ->with('error', 'لا يمكن إزالة دور المشرف الرئيسي لأنه المشرف الوحيد في النظام');
                }
            }

            if ($request->has('roles')) {
                $roleNames = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
                $admin->syncRoles($roleNames);
            } else {
                $admin->syncRoles([]);
            }

            return redirect()->route('admin.roles.assign.index')
                ->with('success', 'تم تحديث رتب المستخدم بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الرتب: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * التحقق مما إذا كانت الرتبة محمية
     */
    private function isProtectedRole(Role $role): bool
    {
        $protectedRoles = ['super_admin', 'admin'];
        return in_array($role->name, $protectedRoles);
    }
}
