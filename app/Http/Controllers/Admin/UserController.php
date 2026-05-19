<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\DeviceToken;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet\LedgerEntry;
use App\Models\Wallet\UserWallet;
use App\Notifications\AdminNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::where('type', 'user')
            ->with(['driver', 'deviceTokens'])
            ->withCount(['orders', 'contracts', 'payments', 'deviceTokens']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('phone_verified')) {
            if ($request->phone_verified == '1') {
                $query->whereNotNull('phone_verified_at');
            } else {
                $query->whereNull('phone_verified_at');
            }
        }

        if ($request->filled('has_driver')) {
            if ($request->has_driver == '1') {
                $query->whereHas('driver');
            } else {
                $query->whereDoesntHave('driver');
            }
        }

        if ($request->filled('auth_method')) {
            switch ($request->auth_method) {
                case 'email':
                    $query->whereNotNull('email');
                    break;
                case 'google':
                    $query->whereNotNull('google_id');
                    break;
                case 'facebook':
                    $query->whereNotNull('facebook_id');
                    break;
                case 'apple':
                    $query->whereNotNull('apple_id');
                    break;
                case 'phone':
                    $query->whereNotNull('phone');
                    break;
            }
        }

        if ($request->filled('notifications')) {
            $query->where('allow_notifications', $request->notifications);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Get users with pagination
        $users = $query->paginate(15)->withQueryString();

        // Get statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', '!=', 'active')->count();
        $verifiedUsers = User::whereNotNull('phone_verified_at')->count();


        $newThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $activeToday = User::where('status', 'active')
            ->whereDate('updated_at', today())
            ->count();

        $suspendedToday = User::where('status', 'inactive')
            ->whereDate('updated_at', today())
            ->count();

        $verifiedThisMonth = User::whereNotNull('phone_verified_at')
            ->whereMonth('phone_verified_at', now()->month)
            ->whereYear('phone_verified_at', now()->year)
            ->count();

        return view('Admin.users.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'verifiedUsers',
            'newThisMonth',
            'activeToday',
            'suspendedToday',
            'verifiedThisMonth',

        ));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('Admin.users.create');
    }
    public function locations($user)
    {
        $user = User::findOrFail($user);
        $locations = $user->savedLocations()
            ->select('id', 'address', 'latitude', 'longitude')
            ->get();

        return response()->json([
            'locations' => $locations
        ]);
    }
    /**
     * Store a newly created user in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|unique:users,phone',
            'country_code' => 'nullable|string|max:5',
            'avatar' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'allow_notifications' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->except(['password', 'password_confirmation', 'avatar']);
            $data['password'] = Hash::make($request->password);

            // Generate full phone
            if ($request->filled('phone') && $request->filled('country_code')) {
                $data['full_phone'] = $request->country_code . $request->phone;
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('users/avatars', 'public');
                $data['avatar'] = $path;
            }

            // Set default values
            $data['allow_notifications'] = $request->boolean('allow_notifications', true);

            // Create user
            $user = User::create($data);

            // Create wallet for user
            $user->createUserWallet();

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'تم إنشاء المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified user.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function show($user)
    {
        $user = User::findOrFail($user);
        $user->load(['driver', 'deviceTokens', 'wallet']);
        $user->loadCount(['orders', 'contracts', 'payments']);

        if (request()->wantsJson()) {
            // إحصائيات الطلبات
            $ordersCompleted = $user->orders()->where('order_status_id', 5)->count();
            $ordersCancelled = $user->orders()->where('order_status_id', 7)->count();
            $ordersPending   = $user->orders()->whereIn('order_status_id', [1, 2, 4])->count();

            return response()->json([
                // بيانات أساسية
                'id'                  => $user->id,
                'name'                => $user->name,
                'phone'               => $user->phone_number,
                'full_phone'          => $user->full_phone,
                'country_code'        => $user->country_code,
                'status'              => $user->status,
                'type'                => $user->type,
                'avatar'              => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'allow_notifications' => (bool) $user->allow_notifications,

                // التحقق
                'is_phone_verified'   => !is_null($user->phone_verified_at),
                'is_email_verified'   => !is_null($user->email_verified_at),
                'phone_verified_at'   => $user->phone_verified_at?->format('Y-m-d H:i') ?? null,
                'email_verified_at'   => $user->email_verified_at?->format('Y-m-d H:i') ?? null,

                // طرق التسجيل
                'has_google'          => !empty($user->google_id),
                'has_facebook'        => !empty($user->facebook_id),

                // التواريخ - مُنسّقة كنص
                'created_at'          => $user->created_at?->format('Y-m-d H:i') ?? '--',
                'updated_at'          => $user->updated_at?->format('Y-m-d H:i') ?? '--',
                'created_at_human'    => $user->created_at?->diffForHumans() ?? '',

                // إحصائيات
                'orders_count'        => $user->orders_count,
                'orders_completed'    => $ordersCompleted,
                'orders_cancelled'    => $ordersCancelled,
                'orders_pending'      => $ordersPending,
                'contracts_count'     => $user->contracts_count,
                'payments_count'      => $user->payments_count,
                'device_tokens_count' => $user->deviceTokens->count(),

                // المحفظة
                'wallet'              => $user->wallet ? [
                    'id'           => $user->wallet->id,
                    'balance'      => number_format($user->wallet->balance, 2),
                    'held_balance' => number_format($user->wallet->held_balance ?? 0, 2),
                    'currency'     => $user->wallet->currency ?? 'SAR',
                    'status'       => $user->wallet->status,
                ] : null,

                // السائق
                'driver'              => $user->driver ? [
                    'id'                   => $user->driver->id,
                    'vehicle_size'         => $user->driver->vehicle_size,
                    'vehicle_plate_number' => $user->driver->vehicle_plate_number,
                    'is_verified'          => (bool) $user->driver->is_verified,
                    'is_active'            => (bool) $user->driver->is_active,
                ] : null,
            ]);
        }

        return view('Admin.users.show', compact('user'));
    }


    /**
     * Show the form for editing the specified user.
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('Admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|unique:users,phone,' . $user->id,
            'country_code' => 'nullable|string|max:5',
            'avatar' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'allow_notifications' => 'boolean',
            'phone_verified' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $data = $request->except(['password', 'password_confirmation', 'avatar']);

            // Update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Generate full phone
            if ($request->filled('phone') && $request->filled('country_code')) {
                $data['full_phone'] = $request->country_code . $request->phone;
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $path = $request->file('avatar')->store('users/avatars', 'public');
                $data['avatar'] = $path;
            }

            // Update phone verification
            if ($request->has('phone_verified')) {
                if ($request->phone_verified && !$user->phone_verified_at) {
                    $data['phone_verified_at'] = now();
                } elseif (!$request->phone_verified && $user->phone_verified_at) {
                    $data['phone_verified_at'] = null;
                }
            }

            $data['allow_notifications'] = $request->boolean('allow_notifications', true);

            // Update user
            $user->update($data);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'تم تحديث المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث المستخدم: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            // Check if user has related data
            if ($user->orders()->exists() || $user->contracts()->exists() || $user->payments()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف المستخدم لأنه لديه طلبات أو عقود أو مدفوعات مرتبطة به'
                ], 400);
            }

            // Delete avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Delete wallet and ledger entries
            if ($user->wallet) {
                $user->wallet->ledgerEntries()->delete();
                $user->wallet->delete();
            }

            // Delete device tokens
            $user->deviceTokens()->delete();

            // Delete OTP history
            $user->otpHistory()->delete();

            // Delete user
            $user->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف المستخدم بنجاح'
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'تم حذف المستخدم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف المستخدم: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status (active/inactive)
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صالحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldStatus = $user->status;
            $user->status = $request->status;
            $user->save();

            // Log the action
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties(['old_status' => $oldStatus, 'new_status' => $request->status])
                ->log('تم تغيير حالة المستخدم');

            return response()->json([
                'success' => true,
                'message' => $request->status == 'active' ? 'تم تفعيل المستخدم بنجاح' : 'تم تعطيل المستخدم بنجاح',
                'data' => [
                    'user_id' => $user->id,
                    'status' => $user->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير حالة المستخدم: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user wallet information
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function wallet(User $user)
    {
        try {
            $wallet = $user->wallet;

            if (!$wallet) {
                $wallet = $user->createUserWallet();
            }

            $ledgerEntries = LedgerEntry::where('owner_type', 'user')
                ->where('owner_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($entry) {
                    $entry->direction = $entry->direction;
                    $entry->formatted_date = $entry->created_at->format('Y-m-d H:i');
                    return $entry;
                });

            return response()->json([
                'success' => true,
                'balance' => $wallet->balance,
                'held_balance' => $wallet->held_balance,
                'currency' => $wallet->currency,
                'status' => $wallet->status,
                'ledger_entries' => $ledgerEntries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات المحفظة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process transaction (approve/reject)
     *
     * @param Request $request
     * @param User $user
     * @param int $transactionId
     * @param string $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function processTransactionAction(Request $request, User $user, $transactionId, $action)
    {
        try {
            $transaction = LedgerEntry::where('owner_type', 'user')
                ->where('owner_id', $user->id)
                ->where('id', $transactionId)
                ->firstOrFail();

            if ($transaction->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'هذه العملية ليست معلقة.'], 400);
            }

            if (!in_array($action, ['approve', 'reject'])) {
                return response()->json(['success' => false, 'message' => 'إجراء غير صالح.'], 400);
            }

            if ($action === 'approve') {
                // If it's a deposit, we might need to actually credit the wallet using a service,
                // but since the admin is forcing it, we just update status and balance.
                // Assuming it's a pending deposit or withdrawal.
                $wallet = $transaction->wallet;
                if ($wallet && $transaction->direction === 'credit') {
                    $wallet->updateBalance($transaction->amount, 'increment');
                } elseif ($wallet && $transaction->direction === 'debit') {
                    // For debit, it might already be deducted when initiated, or not.
                    // Usually pending debits (like hold) decrement available_balance.
                    // Let's assume balance was already held.
                }

                $transaction->markApproved(auth()->id() ?? 1);
                $transaction->markCompleted();

                return response()->json([
                    'success' => true,
                    'message' => 'تم الموافقة على العملية بنجاح'
                ]);
            } else {
                $transaction->markFailed('Rejected by Admin');
                return response()->json([
                    'success' => true,
                    'message' => 'تم رفض العملية بنجاح'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة العملية: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to user
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotification(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صالحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if user allows notifications
            if (!$user->allow_notifications) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم عطل الإشعارات'
                ], 400);
            }

            // Send notification via database
            $notification = new AdminNotification(
                $request->title,
                $request->body,
                $request->data ?? []
            );

            $user->notify($notification);

            // Send push notification if device tokens exist
            $deviceTokens = $user->activeDeviceTokens()->pluck('token')->toArray();

            if (!empty($deviceTokens)) {
                // You can implement push notification here
                // using Firebase, OneSignal, etc.
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الإشعار بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user orders
     *
     * @param User $user
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function orders(User $user)
    {
        $orders = $user->orders()
            ->with(['driver', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        }

        return view('Admin.users.orders', compact('user', 'orders'));
    }

    /**
     * Get user contracts
     *
     * @param User $user
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function contracts(User $user)
    {
        $contracts = $user->contracts()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'contracts' => $contracts
            ]);
        }

        return view('Admin.users.contracts', compact('user', 'contracts'));
    }

    /**
     * Get user device tokens
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function devices(User $user)
    {
        try {
            $devices = $user->deviceTokens()
                ->orderBy('last_used_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'devices' => $devices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات الأجهزة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export users to Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        // You can implement Excel export using Maatwebsite Excel
        // return Excel::download(new UsersExport($request->all()), 'users.xlsx');
    }

    /**
     * Export users to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $query = User::query();

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('phone_verified')) {
            if ($request->phone_verified == '1') {
                $query->whereNotNull('phone_verified_at');
            } else {
                $query->whereNull('phone_verified_at');
            }
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        $pdf = PDF::loadView('Admin.users.pdf', compact('users'));

        return $pdf->download('users.pdf');
    }

    /**
     * Bulk toggle users status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkToggleStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صالحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $count = User::whereIn('id', $request->user_ids)
                ->update(['status' => $request->status]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "تم تحديث حالة {$count} مستخدم بنجاح"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحالة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete users
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صالحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $users = User::whereIn('id', $request->user_ids)->get();
            $deletedCount = 0;

            foreach ($users as $user) {
                // Check if user has related data
                if (
                    !$user->orders()->exists() &&
                    !$user->contracts()->exists() &&
                    !$user->payments()->exists()
                ) {

                    // Delete avatar
                    if ($user->avatar) {
                        Storage::disk('public')->delete($user->avatar);
                    }

                    // Delete wallet and ledger entries
                    if ($user->wallet) {
                        $user->wallet->ledgerEntries()->delete();
                        $user->wallet->delete();
                    }

                    // Delete device tokens
                    $user->deviceTokens()->delete();

                    // Delete user
                    $user->delete();
                    $deletedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "تم حذف {$deletedCount} مستخدم بنجاح"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المستخدمين: ' . $e->getMessage()
            ], 500);
        }
    }
}
