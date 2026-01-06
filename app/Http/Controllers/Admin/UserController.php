<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // البحث
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // الفلترة حسب النوع
        if ($request->filled('type')) {
            $type = $request->get('type');
            if ($type === 'social') {
                $query->where(function ($q) {
                    $q->whereNotNull('google_id')
                        ->orWhereNotNull('facebook_id')
                        ->orWhereNotNull('apple_id');
                });
            } elseif ($type === 'email') {
                $query->whereNull('google_id')
                    ->whereNull('facebook_id')
                    ->whereNull('apple_id');
            }
        }

        // الفلترة حسب الحالة (إذا كان لديك حقل is_active)
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status'));
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $users = $query->paginate(15)->withQueryString();

        return view('Admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('Admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('users', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إضافة المستخدم بنجاح.');
    }

    public function show(User $user)
    {
        $user->load([
            'orders' => function ($query) {
                $query->latest()->limit(10);
            },
            'reviews' => function ($query) {
                $query->latest()->limit(10);
            },
            'favouriteProducts',
            'notifications' => function ($query) {
                $query->latest()->limit(10);
            }
        ]);

        return view('Admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('Admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا وجدت
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $validated['image'] = $request->file('image')->store('users', 'public');
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }

    public function destroy(User $user)
    {
        // حذف الصورة إذا وجدت
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح.');
    }

    public function toggleStatus(User $user)
    {
        if (isset($user->is_active)) {
            $user->update(['is_active' => !$user->is_active]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير الحالة بنجاح',
            'is_active' => $user->is_active ?? null
        ]);
    }

    public function orders(User $user)
    {
        $orders = $user->orders()->latest()->paginate(10);
        return view('Admin.users.orders', compact('user', 'orders'));
    }

    public function reviews(User $user)
    {
        $reviews = $user->reviews()->latest()->paginate(10);
        return view('Admin.users.reviews', compact('user', 'reviews'));
    }

    public function favourites(User $user)
    {
        $favourites = $user->favouriteProducts()->paginate(12);
        return view('Admin.users.favourites', compact('user', 'favourites'));
    }

    public function activities(User $user)
    {
        // يمكنك إضافة سجل الأنشطة هنا
        return view('Admin.users.activities', compact('user'));
    }
}
