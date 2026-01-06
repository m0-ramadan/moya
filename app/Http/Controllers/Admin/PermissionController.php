<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * عرض قائمة جميع الصلاحيات
     */
    public function index(Request $request)
    {
        if (!auth()->guard('admin')->user()->can('permissions.view')) {
            abort(403, 'غير مصرح لك بعرض الصلاحيات');
        }

        $query = Permission::withCount('roles');

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%");
            });
        }

        // التصفية حسب الوحدة
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        $permissions = $query->orderBy('module')->orderBy('name')->get();
        $permissionsByModule = $permissions->groupBy('module');

        // الحصول على قائمة الوحدات
        $modules = Permission::distinct()->pluck('module')->filter()->values();

        return view('Admin.permissions.index', compact('permissionsByModule', 'modules'));
    }

    /**
     * عرض نموذج إنشاء صلاحية جديدة
     */
    public function create()
    {
        if (!auth()->guard('admin')->user()->can('permissions.create')) {
            abort(403, 'غير مصرح لك بإنشاء صلاحيات');
        }

        $modules = $this->getAvailableModules();
        $permissionTypes = $this->getPermissionTypes();

        return view('Admin.permissions.form', [
            'title' => 'إنشاء صلاحية جديدة',
            'description' => 'أضف صلاحية جديدة إلى النظام',
            'action' => route('admin.permissions.store'),
            'modules' => $modules,
            'permissionTypes' => $permissionTypes,
            'permission' => null,
            'module' => old('module', ''),
            'actionVal' => old('action', '')
        ]);
    }

    /**
     * حفظ صلاحية جديدة
     */
    public function store(Request $request)
    {
        if (!auth()->guard('admin')->user()->can('permissions.create')) {
            abort(403, 'غير مصرح لك بإنشاء صلاحيات');
        }

        $validator = Validator::make($request->all(), [
            'module' => 'required|string|max:50',
            'action' => 'required|string|max:50',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ], [
            'module.required' => 'الوحدة مطلوبة',
            'action.required' => 'الإجراء مطلوب'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // توليد اسم الصلاحية
            $name = Str::slug($request->module) . '.' . Str::slug($request->action);

            // التحقق من عدم تكرار الاسم
            if (Permission::where('name', $name)->where('guard_name', 'admin')->exists()) {
                return redirect()->back()
                    ->with('error', 'هذه الصلاحية موجودة بالفعل')
                    ->withInput();
            }

            // إنشاء الصلاحية
            Permission::create([
                'name' => $name,
                'guard_name' => 'admin',
                'display_name' => $request->display_name,
                'description' => $request->description,
                'module' => Str::slug($request->module)
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'تم إنشاء الصلاحية بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الصلاحية: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * عرض نموذج تعديل صلاحية
     */
    public function edit($id)
    {
        if (!auth()->guard('admin')->user()->can('permissions.edit')) {
            abort(403, 'غير مصرح لك بتعديل الصلاحيات');
        }

        $permission = Permission::findOrFail($id);
        $modules = $this->getAvailableModules();
        $permissionTypes = $this->getPermissionTypes();

        // استخراج الوحدة والإجراء من الاسم
        $nameParts = explode('.', $permission->name);
        $module = $nameParts[0] ?? '';
        $actionVal = $nameParts[1] ?? '';

        return view('Admin.permissions.form', [
            'title' => 'تعديل صلاحية',
            'description' => 'تعديل بيانات الصلاحية',
            'action' => route('admin.permissions.update', $permission),
            'modules' => $modules,
            'permissionTypes' => $permissionTypes,
            'permission' => $permission,
            'module' => old('module', $module),
            'actionVal' => old('action', $actionVal)
        ]);
    }

    /**
     * تحديث الصلاحية
     */
    public function update(Request $request, $id)
    {
        if (!auth()->guard('admin')->user()->can('permissions.edit')) {
            abort(403, 'غير مصرح لك بتعديل الصلاحيات');
        }

        $permission = Permission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'module' => 'required|string|max:50',
            'action' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // توليد اسم الصلاحية الجديد
            $newName = Str::slug($request->module) . '.' . Str::slug($request->action);

            // التحقق من عدم تكرار الاسم (إذا تغير)
            if ($newName !== $permission->name && Permission::where('name', $newName)->where('guard_name', 'admin')->exists()) {
                return redirect()->back()
                    ->with('error', 'هذا الاسم مستخدم بالفعل لصلاحية أخرى')
                    ->withInput();
            }

            // تحديث الصلاحية
            $permission->update([
                'name' => $newName,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'module' => Str::slug($request->module)
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'تم تحديث الصلاحية بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الصلاحية: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * حذف صلاحية
     */
    public function destroy($id)
    {
        if (!auth()->guard('admin')->user()->can('permissions.delete')) {
            abort(403, 'غير مصرح لك بحذف الصلاحيات');
        }

        $permission = Permission::findOrFail($id);

        try {
            // التحقق من وجود رتب مرتبطة بالصلاحية
            if ($permission->roles()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف الصلاحية لأنها مرتبطة برتب'
                ], 400);
            }

            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الصلاحية بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الصلاحية: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * توليد صلاحيات افتراضية لوحدة معينة
     */
    public function generateForModule(Request $request)
    {
        if (!auth()->guard('admin')->user()->can('permissions.create')) {
            abort(403, 'غير مصرح لك بإنشاء صلاحيات');
        }

        $validator = Validator::make($request->all(), [
            'module' => 'required|string|max:50',
            'module_name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $module = Str::slug($request->module);
        $moduleName = $request->module_name;

        $permissions = [
            ['action' => 'view', 'display' => 'عرض ' . $moduleName, 'description' => 'القدرة على عرض ' . $moduleName],
            ['action' => 'create', 'display' => 'إنشاء ' . $moduleName, 'description' => 'القدرة على إنشاء ' . $moduleName],
            ['action' => 'edit', 'display' => 'تعديل ' . $moduleName, 'description' => 'القدرة على تعديل ' . $moduleName],
            ['action' => 'delete', 'display' => 'حذف ' . $moduleName, 'description' => 'القدرة على حذف ' . $moduleName],
            ['action' => 'manage', 'display' => 'إدارة ' . $moduleName, 'description' => 'القدرة على إدارة ' . $moduleName],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($permissions as $permission) {
            $name = $module . '.' . $permission['action'];

            if (!Permission::where('name', $name)->where('guard_name', 'admin')->exists()) {
                Permission::create([
                    'name' => $name,
                    'guard_name' => 'admin',
                    'display_name' => $permission['display'],
                    'description' => $permission['description'],
                    'module' => $module
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        $message = "تم إنشاء {$created} صلاحية جديدة";
        if ($skipped > 0) {
            $message .= " وتم تخطي {$skipped} صلاحية موجودة مسبقاً";
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', $message);
    }

    /**
     * الحصول على الوحدات المتاحة
     */
    private function getAvailableModules(): array
    {
        return [
            'users' => 'المستخدمين',
            'admins' => 'المشرفين',
            'roles' => 'الرتب',
            'permissions' => 'الصلاحيات',
            'products' => 'المنتجات',
            'categories' => 'الأقسام',
            'orders' => 'الطلبات',
            'banners' => 'البانرات',
            'coupons' => 'الكوبونات',
            'settings' => 'الإعدادات',
            'reports' => 'التقارير',
            'payment_methods' => 'طرق الدفع',
            'contact_us' => 'تواصل معنا',
            'about' => 'عن الموقع'
        ];
    }

    /**
     * الحصول على أنواع الإجراءات
     */
    private function getPermissionTypes(): array
    {
        return [
            'view' => 'عرض',
            'create' => 'إنشاء',
            'edit' => 'تعديل',
            'delete' => 'حذف',
            'manage' => 'إدارة كاملة',
            'export' => 'تصدير',
            'import' => 'استيراد',
            'approve' => 'موافقة',
            'reject' => 'رفض',
            'assign' => 'تعيين',
            'print' => 'طباعة',
            'restore' => 'استعادة',
            'force_delete' => 'حذف نهائي'
        ];
    }
}
