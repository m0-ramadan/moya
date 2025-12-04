<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermissionsController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:view permissions', ['only' => ['index', 'show']]);
    //     $this->middleware('permission:create permissions', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:edit permissions', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:delete permissions', ['only' => ['destroy']]);
    // }

    public function index()
    {
        $permissions = Permission::all();
        return view('Admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('Admin.permissions.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors(),
            ], 422);
        }

        Permission::create(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الصلاحية بنجاح.',
        ]);
    }



    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('Admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors(),
            ], 422);
        }

        $permission->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الصلاحية بنجاح.',
        ]);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'تم حذف الصلاحية بنجاح.');
    }
}
