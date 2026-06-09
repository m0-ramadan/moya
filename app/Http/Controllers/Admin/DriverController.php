<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    /**
     * Display a listing of the drivers.
     */
    public function index(Request $request)
    {
        // Base query with relationships
        $query = Driver::with(['user', 'driverWallet', 'ratings'])
            ->withCount('orders');

        // Apply filters
        if ($request->filled('citizenship')) {
            $query->where('citizenship', $request->citizenship);
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vehicle_size')) {
            $query->where('vehicle_size', $request->vehicle_size);
        }

        if ($request->filled('is_vehicle_owner')) {
            $query->where('is_vehicle_owner', $request->is_vehicle_owner);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('full_phone', 'like', "%{$search}%");
            })
                ->orWhere('national_id', 'like', "%{$search}%")
                ->orWhere('iqama_number', 'like', "%{$search}%")
                ->orWhere('license_number', 'like', "%{$search}%")
                ->orWhere('vehicle_plate_number', 'like', "%{$search}%");
        }

        // Statistics for cards
        $totalDrivers = Driver::count();
        $activeDrivers = Driver::where('status', 'active')->where('is_active', true)->count();
        $pendingDrivers = Driver::where('is_verified', false)->whereNull('rejection_reason')->count();
        $inactiveDrivers = Driver::where('status', '!=', 'active')->orWhere('is_active', false)->count();

        // New this month
        $newThisMonth = Driver::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Available now (with active location in last 5 minutes)
        $availableNow = Driver::where('status', 'active')
            ->where('is_active', true)
            ->whereHas('currectLocation', function ($q) {
                $q->where('updated_at', '>=', now()->subMinutes(5));
            })
            ->count();

        // Pending this week
        $pendingThisWeek = Driver::where('is_verified', false)
            ->whereNull('rejection_reason')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Suspended today
        $suspendedToday = Driver::where('status', 'suspended')
            ->whereDate('updated_at', today())
            ->count();

        // Storage usage for display (example calculation)
        $storageUsage = [
            'percentage' => 45,
            'available_human' => '1.2 GB'
        ];

        // Get drivers with pagination
        $drivers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('Admin.drivers.index', compact(
            'drivers',
            'totalDrivers',
            'activeDrivers',
            'pendingDrivers',
            'inactiveDrivers',
            'newThisMonth',
            'availableNow',
            'pendingThisWeek',
            'suspendedToday',
            'storageUsage'
        ));
    }

    /**
     * Show the form for creating a new driver.
     */
    public function create()
    {
        // Get users who are not drivers yet
        $users = User::whereDoesntHave('driver')->get();

        return view('Admin.drivers.create', compact('users'));
    }

    /**
     * Store a newly created driver in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:drivers,user_id',
            'citizenship' => 'required|in:saudi,resident',
            'country_id' => 'nullable|exists:countries,id',
            'date_of_birth' => 'required|date',
            'national_id' => 'required_if:citizenship,saudi|string|unique:drivers,national_id|nullable',
            'iqama_number' => 'required_if:citizenship,resident|string|unique:drivers,iqama_number|nullable',
            'iqama_expiry_date' => 'required_if:citizenship,resident|date|nullable',

            // Images
            'personal_photo' => 'nullable|image|max:2048',
            'id_image_front' => 'required|image|max:2048',
            'id_image_back' => 'required|image|max:2048',

            // License
            'license_number' => 'required|string|unique:drivers',
            'license_expiry_date' => 'required|date',
            'license_image_front' => 'required|image|max:2048',
            'license_image_back' => 'required|image|max:2048',

            // Vehicle
            'vehicle_size' => 'required|in:small,medium,large',
            'is_vehicle_owner' => 'required|boolean',
            'vehicle_plate_number' => 'required|string',
            'vehicle_registration_number' => 'required|string',
            'vehicle_residency_number' => 'required|string',
            'vehicle_registration_image' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $driverData = $request->except([
                'personal_photo',
                'id_image_front',
                'id_image_back',
                'license_image_front',
                'license_image_back',
                'vehicle_registration_image'
            ]);

            // Handle file uploads
            if ($request->hasFile('personal_photo')) {
                $driverData['personal_photo'] = $request->file('personal_photo')->store('drivers/personal', 'public');
            }

            if ($request->hasFile('id_image_front')) {
                $driverData['id_image_front'] = $request->file('id_image_front')->store('drivers/id', 'public');
            }

            if ($request->hasFile('id_image_back')) {
                $driverData['id_image_back'] = $request->file('id_image_back')->store('drivers/id', 'public');
            }

            if ($request->hasFile('license_image_front')) {
                $driverData['license_image_front'] = $request->file('license_image_front')->store('drivers/license', 'public');
            }

            if ($request->hasFile('license_image_back')) {
                $driverData['license_image_back'] = $request->file('license_image_back')->store('drivers/license', 'public');
            }

            if ($request->hasFile('vehicle_registration_image')) {
                $driverData['vehicle_registration_image'] = $request->file('vehicle_registration_image')->store('drivers/vehicle', 'public');
            }

            // Set default values
            $driverData['is_verified'] = false;
            $driverData['status'] = 'pending';
            $driverData['is_active'] = true;

            // Create driver
            $driver = Driver::create($driverData);

            DB::commit();

            return redirect()->route('admin.drivers.index')
                ->with('success', 'تم إضافة السائق بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إضافة السائق: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified driver.
     */
    public function show($id)
    {
        $driver = Driver::with([
            'user',
            'driverWallet',
            'ratings',
            'country',
            'orders' => function ($q) {
                $q->latest()->limit(10);
            }
        ])
            ->withCount('orders')
            ->withAvg('ratings', 'rating')
            ->findOrFail($id);

        return response()->json($driver);
    }

    public function details($id)
    {
        $driver = Driver::with([
            'user',
            'driverWallet',
            'ratings.user',
            'reports.reportedBy',
            'orders.service',
            'orders.waterType',
            'orders.location',
            'orders.status'
        ])
            ->withCount('orders')
            ->withAvg('ratings', 'rating')
            ->findOrFail($id);

        return view('Admin.drivers.show', compact('driver'));
    }

    /**
     * Show the form for editing the specified driver.
     */
    public function edit($id)
    {
        $driver = Driver::with('user')->findOrFail($id);
        $users = User::whereDoesntHave('driver')->orWhere('id', $driver->user_id)->get();

        return view('Admin.drivers.edit', compact('driver', 'users'));
    }

    /**
     * Update the specified driver in storage.
     */
    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:drivers,user_id,' . $id,
            'citizenship' => 'required|in:saudi,resident',
            'country_id' => 'nullable|exists:countries,id',
            'date_of_birth' => 'required|date',
            'national_id' => 'required_if:citizenship,saudi|string|unique:drivers,national_id,' . $id . '|nullable',
            'iqama_number' => 'required_if:citizenship,resident|string|unique:drivers,iqama_number,' . $id . '|nullable',
            'iqama_expiry_date' => 'required_if:citizenship,resident|date|nullable',

            // Images
            'personal_photo' => 'nullable|image|max:2048',
            'id_image_front' => 'nullable|image|max:2048',
            'id_image_back' => 'nullable|image|max:2048',

            // License
            'license_number' => 'required|string|unique:drivers,license_number,' . $id,
            'license_expiry_date' => 'required|date',
            'license_image_front' => 'nullable|image|max:2048',
            'license_image_back' => 'nullable|image|max:2048',

            // Vehicle
            'vehicle_size' => 'required|in:small,medium,large',
            'is_vehicle_owner' => 'required|boolean',
            'vehicle_plate_number' => 'required|string',
            'vehicle_registration_number' => 'required|string',
            'vehicle_residency_number' => 'required|string',
            'vehicle_registration_image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $driverData = $request->except([
                'personal_photo',
                'id_image_front',
                'id_image_back',
                'license_image_front',
                'license_image_back',
                'vehicle_registration_image'
            ]);

            // Handle file uploads
            if ($request->hasFile('personal_photo')) {
                // Delete old file
                if ($driver->personal_photo) {
                    Storage::disk('public')->delete($driver->personal_photo);
                }
                $driverData['personal_photo'] = $request->file('personal_photo')->store('drivers/personal', 'public');
            }

            if ($request->hasFile('id_image_front')) {
                if ($driver->id_image_front) {
                    Storage::disk('public')->delete($driver->id_image_front);
                }
                $driverData['id_image_front'] = $request->file('id_image_front')->store('drivers/id', 'public');
            }

            if ($request->hasFile('id_image_back')) {
                if ($driver->id_image_back) {
                    Storage::disk('public')->delete($driver->id_image_back);
                }
                $driverData['id_image_back'] = $request->file('id_image_back')->store('drivers/id', 'public');
            }

            if ($request->hasFile('license_image_front')) {
                if ($driver->license_image_front) {
                    Storage::disk('public')->delete($driver->license_image_front);
                }
                $driverData['license_image_front'] = $request->file('license_image_front')->store('drivers/license', 'public');
            }

            if ($request->hasFile('license_image_back')) {
                if ($driver->license_image_back) {
                    Storage::disk('public')->delete($driver->license_image_back);
                }
                $driverData['license_image_back'] = $request->file('license_image_back')->store('drivers/license', 'public');
            }

            if ($request->hasFile('vehicle_registration_image')) {
                if ($driver->vehicle_registration_image) {
                    Storage::disk('public')->delete($driver->vehicle_registration_image);
                }
                $driverData['vehicle_registration_image'] = $request->file('vehicle_registration_image')->store('drivers/vehicle', 'public');
            }

            // Update driver
            $driver->update($driverData);

            DB::commit();

            return redirect()->route('admin.drivers.index')
                ->with('success', 'تم تحديث بيانات السائق بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified driver from storage.
     */
    public function destroy($id)
    {
        try {
            $driver = Driver::findOrFail($id);

            // Check if driver has orders
            if ($driver->orders()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف السائق لأنه لديه طلبات مرتبطة'
                ], 400);
            }

            DB::beginTransaction();

            // Delete files
            $files = [
                $driver->personal_photo,
                $driver->id_image_front,
                $driver->id_image_back,
                $driver->license_image_front,
                $driver->license_image_back,
                $driver->vehicle_registration_image
            ];

            foreach ($files as $file) {
                if ($file) {
                    Storage::disk('public')->delete($file);
                }
            }

            // Delete driver (wallet will be deleted automatically due to cascade)
            $driver->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف السائق بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve driver verification.
     */
    public function approve($id)
    {
        try {
            $driver = Driver::findOrFail($id);

            $driver->update([
                'is_verified' => true,
                'verified_at' => now(),
                'rejection_reason' => null,
                'status' => 'active',
                'is_active' => true
            ]);

            $driver->user?->update([
                'status' => 'active',
            ]);

            // TODO: Send notification to driver

            return response()->json([
                'success' => true,
                'message' => 'تم توثيق السائق بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التوثيق: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject driver verification.
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $driver = Driver::findOrFail($id);

            $driver->update([
                'is_verified' => false,
                'verified_at' => null,
                'rejection_reason' => $request->rejection_reason,
                'status' => 'inactive',
                'is_active' => false
            ]);

            // TODO: Send notification to driver with rejection reason

            return response()->json([
                'success' => true,
                'message' => 'تم رفض طلب السائق'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الرفض: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle driver status.
     */
    public function toggleStatus(Request $request, $id)
    {
        $driver = Driver::with('user')->findOrFail($id);
        $targetUserStatus = $request->input('status', $driver->user?->status === 'banned' ? 'active' : 'banned');

        $validator = Validator::make([
            'status' => $targetUserStatus,
        ], [
            'status' => 'required|in:active,banned'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($driver, $targetUserStatus) {
                if ($driver->user) {
                    $driver->user->update([
                        'status' => $targetUserStatus,
                    ]);

                    if ($targetUserStatus === 'banned') {
                        $driver->user->revokeAllSessions();
                    }
                }

                $driver->update([
                    'status' => $targetUserStatus === 'banned'
                        ? 'suspended'
                        : ($driver->is_verified
                            ? 'active'
                            : ($driver->rejection_reason ? 'inactive' : 'pending')),
                    'is_active' => $targetUserStatus === 'active' && $driver->is_verified ? 1 : 0,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => $targetUserStatus === 'banned'
                    ? 'تم حظر السائق بنجاح'
                    : 'تم فك حظر السائق بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير الحالة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver wallet information.
     */
    public function wallet($id)
    {
        try {
            $driver = Driver::with('driverWallet')->findOrFail($id);

            // Get wallet transactions
            $transactions = $driver->ledgerEntries()
                ->latest()
                ->limit(20)
                ->get();

            $walletData = [
                'balance' => $driver->driverWallet?->balance ?? 0,
                'held_balance' => $driver->driverWallet?->held_balance ?? 0,
                'currency' => $driver->driverWallet?->currency ?? 'SAR',
                'total_earnings' => $driver->ledgerEntries()
                    ->where('type', 'credit')
                    ->where('status', 'completed')
                    ->sum('amount'),
                'transactions' => $transactions->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'type' => $transaction->type,
                        'status' => $transaction->status,
                        'description' => $transaction->description,
                        'created_at' => $transaction->created_at->format('Y-m-d H:i'),
                    ];
                })
            ];

            return response()->json($walletData);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل المحفظة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export drivers list.
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality (Excel, PDF, etc.)
        return redirect()->back()->with('info', 'جاري تطوير ميزة التصدير');
    }

    /**
     * Get storage usage statistics.
     */
    public function storageUsage()
    {
        // Calculate storage used by driver documents
        $totalSize = 0;
        $drivers = Driver::all();

        foreach ($drivers as $driver) {
            $files = [
                $driver->personal_photo,
                $driver->id_image_front,
                $driver->id_image_back,
                $driver->license_image_front,
                $driver->license_image_back,
                $driver->vehicle_registration_image
            ];

            foreach ($files as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    $totalSize += Storage::disk('public')->size($file);
                }
            }
        }

        // Convert to human readable
        $totalSizeHuman = $this->formatBytes($totalSize);
        $percentage = min(round(($totalSize / (5 * 1024 * 1024 * 1024)) * 100), 100); // Assuming 5GB limit

        return response()->json([
            'percentage' => $percentage,
            'available_human' => $this->formatBytes(5 * 1024 * 1024 * 1024 - $totalSize),
            'total_human' => $this->formatBytes(5 * 1024 * 1024 * 1024),
            'used_human' => $totalSizeHuman
        ]);
    }

    /**
     * Get quick statistics for dashboard.
     */
    public function quickStats()
    {
        return response()->json([
            'total' => Driver::count(),
            'active' => Driver::where('status', 'active')->where('is_active', true)->count(),
            'pending' => Driver::where('is_verified', false)->whereNull('rejection_reason')->count(),
            'inactive' => Driver::where('status', '!=', 'active')->orWhere('is_active', false)->count(),
            'verified' => Driver::where('is_verified', true)->count(),
            'unverified' => Driver::where('is_verified', false)->count(),
            'saudi' => Driver::where('citizenship', 'saudi')->count(),
            'resident' => Driver::where('citizenship', 'resident')->count(),
        ]);
    }

    /**
     * Get recent activities.
     */
    public function recentActivities()
    {
        $recentDrivers = Driver::with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($driver) {
                return [
                    'id' => $driver->id,
                    'type' => $driver->is_verified ? 'success' : 'info',
                    'icon' => 'user-plus',
                    'title' => 'سائق جديد',
                    'description' => $driver->user->name . ' - ' . ($driver->citizenship == 'saudi' ? 'سعودي' : 'مقيم'),
                    'time' => $driver->created_at->diffForHumans()
                ];
            });

        return response()->json($recentDrivers);
    }

    /**
     * Get system status.
     */
    public function systemStatus()
    {
        $smtpStatus = $this->checkSmtpStatus();
        $storageUsage = $this->storageUsage()->getData();
        $maintenanceMode = app()->isDownForMaintenance();

        return response()->json([
            'smtp' => [
                'class' => $smtpStatus ? 'success' : 'warning',
                'text' => $smtpStatus ? 'نشط' : 'يتطلب إعداد'
            ],
            'storage' => [
                'class' => $storageUsage->percentage < 80 ? 'success' : 'warning',
                'text' => $storageUsage->percentage . '%'
            ],
            'maintenance' => [
                'class' => $maintenanceMode ? 'warning' : 'success',
                'text' => $maintenanceMode ? 'مفعل' : 'معطل'
            ]
        ]);
    }

    /**
     * Clear cache (for system status).
     */
    public function clearCache()
    {
        try {
            // Clear application cache
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return response()->json([
                'success' => true,
                'message' => 'تم مسح الكاش بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مسح الكاش: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle maintenance mode.
     */
    public function toggleMaintenance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mode' => 'required|in:enable,disable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->mode === 'enable') {
                Artisan::call('down', [
                    '--secret' => 'secret' // You can change this
                ]);
                $message = 'تم تفعيل وضع الصيانة';
            } else {
                Artisan::call('up');
                $message = 'تم تعطيل وضع الصيانة';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to format bytes.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Helper function to check SMTP status.
     */
    private function checkSmtpStatus()
    {
        // This is a simple check, you might want to implement actual SMTP test
        $config = config('mail');
        return !empty($config['username']) && !empty($config['password']);
    }
    /**
     * Delete driver image
     */
    public function deleteImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'field' => 'required|in:personal_photo,id_image_front,id_image_back,license_image_front,license_image_back,vehicle_registration_image'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $driver = Driver::findOrFail($id);
            $field = $request->field;

            if ($driver->$field) {
                Storage::disk('public')->delete($driver->$field);
                $driver->$field = null;
                $driver->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الصورة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الصورة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset driver verification status
     */
    public function resetVerification($id)
    {
        try {
            $driver = Driver::findOrFail($id);

            $driver->update([
                'is_verified' => false,
                'verified_at' => null,
                'rejection_reason' => null,
                'status' => 'pending',
                'is_active' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إعادة تعيين حالة التحقق بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إعادة التعيين: ' . $e->getMessage()
            ], 500);
        }
    }
}
