<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractDeliveryLocation;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SavedLocation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ContractController extends Controller
{
    /**
     * Display a listing of contracts.
     */
    public function index(Request $request)
    {
        $query = Contract::with(['user', 'payments']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('contract_number', 'like', '%' . $request->search . '%')
                  ->orWhere('applicant_name', 'like', '%' . $request->search . '%')
                  ->orWhere('company_name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('phone_number', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        if ($request->filled('duration_type')) {
            $query->where('duration_type', $request->duration_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        if ($request->filled('amount_from')) {
            $query->where('total_amount', '>=', $request->amount_from);
        }

        if ($request->filled('amount_to')) {
            $query->where('total_amount', '<=', $request->amount_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        // Prevent SQL injection by whitelisting allowed columns
        $allowedSorts = ['created_at', 'updated_at', 'start_date', 'end_date', 'total_amount', 'paid_amount', 'contract_number'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->latest();
        }

        $contracts = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Contract::count(),
            'active' => Contract::where('status', 'active')->count(),
            'expiring_soon' => Contract::where('status', 'active')
                ->whereDate('end_date', '<=', now()->addDays(7))
                ->whereDate('end_date', '>=', now())
                ->count(),
            'total_revenue' => Contract::sum('paid_amount'),
            'expired' => Contract::where('status', 'expired')->count(),
            'pending' => Contract::where('status', 'pending')->count(),
            'cancelled' => Contract::where('status', 'cancelled')->count(),
        ];

        $users = User::whereHas('contracts')->select('id', 'name', 'phone_number', 'email')->get();

        return view('Admin.contracts.index', compact('contracts', 'stats', 'users'));
    }

    /**
     * Show the form for creating a new contract.
     */
    public function create()
    {
        $users = User::where('status', 'active')->get(['id', 'name', 'phone_number', 'email']);
        $savedLocations = collect();
        
        // If user selected via query param, load their locations
        if (request()->has('user_id')) {
            $user = User::find(request('user_id'));
            if ($user) {
                $savedLocations = $user->savedLocations;
            }
        }

        return view('Admin.contracts.create', compact('users', 'savedLocations'));
    }

    /**
     * Store a newly created contract in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'contract_number' => 'nullable|string|max:50|unique:contracts,contract_number',
            'contract_type' => 'required|in:individual,company',
            'company_name' => 'required_if:contract_type,company|nullable|string|max:255',
            'applicant_name' => 'nullable|string|max:255',
            'duration_type' => 'required|in:monthly,quarterly,semi_annual,annual',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'renewal_date' => 'nullable|date|after_or_equal:end_date',
            'total_orders_limit' => 'nullable|integer|min:0',
            'remaining_orders' => 'nullable|integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'remaining_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,expired,pending,cancelled',
            'notes' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'payment_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'locations' => 'nullable|array',
            'locations.*.saved_location_id' => 'required|exists:saved_locations,id',
            'locations.*.priority' => 'required|integer|min:1',
            'locations.*.notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Generate contract number if not provided
            $data = $request->all();
            if (empty($data['contract_number'])) {
                $data['contract_number'] = $this->generateContractNumber();
            }

            // Calculate remaining amount if not provided
            if (empty($data['remaining_amount'])) {
                $data['remaining_amount'] = $data['total_amount'] - ($data['paid_amount'] ?? 0);
            }

            // Set remaining orders if not provided
            if (empty($data['remaining_orders']) && !empty($data['total_orders_limit'])) {
                $data['remaining_orders'] = $data['total_orders_limit'];
            }

            // Handle payment proof upload
            if ($request->hasFile('payment_proof')) {
                $path = $request->file('payment_proof')->store('contracts/payments', 'public');
                $data['payment_proof'] = $path;
            }

            // Create contract
            $contract = Contract::create($data);

            // Save delivery locations
            if ($request->has('locations')) {
                foreach ($request->locations as $location) {
                    $contract->deliveryLocations()->create([
                        'saved_location_id' => $location['saved_location_id'],
                        'priority' => $location['priority'],
                        'notes' => $location['notes'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.contracts.show', $contract->id)
                ->with('success', 'تم إنشاء العقد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء العقد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified contract.
     */
    public function show($id)
    {
        $contract = Contract::with([
            'user',
            'deliveryLocations.savedLocation',
            'payments' => function($q) {
                $q->latest();
            },
            'orders' => function($q) {
                $q->latest()->limit(10);
            }
        ])->findOrFail($id);

        // Get full orders count
        $ordersCount = Order::where('contract_id', $id)->count();

        // Get payment statistics
        $paymentStats = [
            'total' => $contract->payments->count(),
            'completed' => $contract->payments->where('status', 'completed')->count(),
            'pending' => $contract->payments->where('status', 'pending')->count(),
            'failed' => $contract->payments->where('status', 'failed')->count(),
        ];

        // Get recent activities (you might need to implement activity logging)
        $activities = $this->getContractActivities($contract);

        return view('Admin.contracts.show', compact('contract', 'ordersCount', 'paymentStats', 'activities'));
    }

    /**
     * Show the form for editing the specified contract.
     */
    public function edit($id)
    {
        $contract = Contract::with(['user', 'deliveryLocations.savedLocation'])->findOrFail($id);
        $users = User::where('status', 'active')->get(['id', 'name', 'phone_number', 'email']);
        
        // Get user's saved locations for location selection
        $savedLocations = $contract->user ? $contract->user->savedLocations : collect();

        return view('Admin.contracts.edit', compact('contract', 'users', 'savedLocations'));
    }

    /**
     * Update the specified contract in storage.
     */
    public function update(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'contract_type' => 'required|in:individual,company',
            'company_name' => 'required_if:contract_type,company|nullable|string|max:255',
            'applicant_name' => 'nullable|string|max:255',
            'duration_type' => 'required|in:monthly,quarterly,semi_annual,annual',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'renewal_date' => 'nullable|date|after_or_equal:end_date',
            'total_orders_limit' => 'nullable|integer|min:0',
            'remaining_orders' => 'nullable|integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'remaining_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,expired,pending,cancelled',
            'notes' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'payment_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $data = $request->all();
            
            // Calculate remaining amount
            $data['remaining_amount'] = $data['total_amount'] - ($data['paid_amount'] ?? 0);

            // Handle payment proof upload
            if ($request->hasFile('payment_proof')) {
                // Delete old file if exists
                if ($contract->payment_proof) {
                    Storage::disk('public')->delete($contract->payment_proof);
                }
                
                $path = $request->file('payment_proof')->store('contracts/payments', 'public');
                $data['payment_proof'] = $path;
            }

            // Update contract
            $contract->update($data);

            DB::commit();

            return redirect()->route('admin.contracts.show', $contract->id)
                ->with('success', 'تم تحديث العقد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث العقد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified contract from storage.
     */
    public function destroy($id)
    {
        try {
            $contract = Contract::findOrFail($id);
            
            // Check if contract has related records
            if ($contract->orders()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف العقد لوجود طلبات مرتبطة به'
                ], 400);
            }

            if ($contract->payments()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف العقد لوجود مدفوعات مرتبطة به'
                ], 400);
            }

            // Delete payment proof if exists
            if ($contract->payment_proof) {
                Storage::disk('public')->delete($contract->payment_proof);
            }

            // Delete delivery locations
            $contract->deliveryLocations()->delete();

            // Delete contract
            $contract->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف العقد بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف العقد: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle contract status.
     */
    public function toggleStatus($id)
    {
        try {
            $contract = Contract::findOrFail($id);
            
            $contract->status = $contract->status === 'active' ? 'expired' : 'active';
            $contract->save();

            return response()->json([
                'success' => true,
                'message' => 'تم تغيير حالة العقد بنجاح',
                'status' => $contract->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير حالة العقد: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions on contracts.
     */
    public function bulkActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete,extend',
            'ids' => 'required|array',
            'ids.*' => 'exists:contracts,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            switch ($request->action) {
                case 'activate':
                    Contract::whereIn('id', $request->ids)
                        ->where('status', '!=', 'active')
                        ->update(['status' => 'active']);
                    $message = 'تم تفعيل العقود المحددة بنجاح';
                    break;

                case 'deactivate':
                    Contract::whereIn('id', $request->ids)
                        ->where('status', 'active')
                        ->update(['status' => 'expired']);
                    $message = 'تم تعطيل العقود المحددة بنجاح';
                    break;

                case 'extend':
                    // Extend contracts by 30 days
                    foreach ($request->ids as $id) {
                        $contract = Contract::find($id);
                        if ($contract && $contract->end_date) {
                            $contract->end_date = $contract->end_date->addDays(30);
                            $contract->renewal_date = $contract->end_date;
                            $contract->save();
                        }
                    }
                    $message = 'تم تمديد العقود المحددة بنجاح';
                    break;

                case 'delete':
                    // Check if contracts can be deleted
                    $contracts = Contract::whereIn('id', $request->ids)
                        ->withCount(['orders', 'payments'])
                        ->get();

                    $canDelete = true;
                    $errorMessage = '';

                    foreach ($contracts as $contract) {
                        if ($contract->orders_count > 0) {
                            $canDelete = false;
                            $errorMessage = 'لا يمكن حذف العقد ' . $contract->contract_number . ' لوجود طلبات مرتبطة به';
                            break;
                        }
                        if ($contract->payments_count > 0) {
                            $canDelete = false;
                            $errorMessage = 'لا يمكن حذف العقد ' . $contract->contract_number . ' لوجود مدفوعات مرتبطة به';
                            break;
                        }
                    }

                    if (!$canDelete) {
                        DB::rollBack();
                        return redirect()->back()->with('error', $errorMessage);
                    }

                    // Delete contracts and related data
                    foreach ($contracts as $contract) {
                        if ($contract->payment_proof) {
                            Storage::disk('public')->delete($contract->payment_proof);
                        }
                        $contract->deliveryLocations()->delete();
                        $contract->delete();
                    }
                    
                    $message = 'تم حذف العقود المحددة بنجاح';
                    break;
            }

            DB::commit();

            return redirect()->route('admin.contracts.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تنفيذ الإجراء: ' . $e->getMessage());
        }
    }

    /**
     * Export contracts data.
     */
    public function export(Request $request)
    {
        try {
            $query = Contract::with(['user']);

            // Apply same filters as index
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('contract_number', 'like', '%' . $request->search . '%')
                      ->orWhere('applicant_name', 'like', '%' . $request->search . '%')
                      ->orWhereHas('user', function($userQuery) use ($request) {
                          $userQuery->where('name', 'like', '%' . $request->search . '%');
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $contracts = $query->get();

            // Generate CSV
            $filename = 'contracts_export_' . now()->format('Y-m_d') . '.csv';
            $handle = fopen('php://temp', 'w+');

            // Add UTF-8 BOM for Arabic support
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($handle, [
                'رقم العقد',
                'العميل',
                'نوع العقد',
                'شركة',
                'تاريخ البداية',
                'تاريخ النهاية',
                'المدة',
                'إجمالي المبلغ',
                'المدفوع',
                'المتبقي',
                'الحالة',
                'تاريخ الإنشاء'
            ]);

            // Data
            foreach ($contracts as $contract) {
                fputcsv($handle, [
                    $contract->contract_number,
                    $contract->user->name ?? 'غير معروف',
                    $contract->contract_type == 'individual' ? 'فردي' : 'شركة',
                    $contract->company_name ?? '-',
                    $contract->start_date ? $contract->start_date->format('Y-m-d') : '-',
                    $contract->end_date ? $contract->end_date->format('Y-m-d') : '-',
                    $this->getDurationTypeArabic($contract->duration_type),
                    number_format($contract->total_amount, 2),
                    number_format($contract->paid_amount, 2),
                    number_format($contract->remaining_amount, 2),
                    $this->getStatusArabic($contract->status),
                    $contract->created_at->format('Y-m-d')
                ]);
            }

            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            return response($content)
                ->header('Content-Type', 'text/csv; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تصدير البيانات: ' . $e->getMessage());
        }
    }

    /**
     * Extend contract.
     */
    public function extend(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_end_date' => 'required|date|after:today',
            'extension_reason' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $contract = Contract::findOrFail($id);
            
            $oldEndDate = $contract->end_date;
            $contract->end_date = $request->new_end_date;
            $contract->renewal_date = $request->new_end_date;
            
            // If expired, activate it
            if ($contract->status === 'expired') {
                $contract->status = 'active';
            }
            
            $contract->save();

            // Log extension (you might want to create an activity log)
            // ActivityLog::create(...)

            return response()->json([
                'success' => true,
                'message' => 'تم تمديد العقد بنجاح',
                'new_end_date' => $contract->end_date->format('Y-m-d')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تمديد العقد: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contract payments.
     */
    public function payments($id)
    {
        $contract = Contract::findOrFail($id);
        $payments = $contract->payments()->with('user')->latest()->paginate(15);

        return view('Admin.contracts.payments', compact('contract', 'payments'));
    }

    /**
     * Get contract orders.
     */
    public function orders($id)
    {
        $contract = Contract::findOrFail($id);
        $orders = $contract->orders()->with('user')->latest()->paginate(15);

        return view('Admin.contracts.orders', compact('contract', 'orders'));
    }

    /**
     * Store delivery location.
     */
    public function storeLocation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'saved_location_id' => 'required|exists:saved_locations,id',
            'priority' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $contract = Contract::findOrFail($id);

            // Check if location already exists for this contract
            $exists = $contract->deliveryLocations()
                ->where('saved_location_id', $request->saved_location_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا الموقع مضاف بالفعل للعقد'
                ], 400);
            }

            $location = $contract->deliveryLocations()->create([
                'saved_location_id' => $request->saved_location_id,
                'priority' => $request->priority,
                'notes' => $request->notes
            ]);

            $location->load('savedLocation');

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الموقع بنجاح',
                'location' => $location
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الموقع: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete delivery location.
     */
    public function destroyLocation($id, $locationId)
    {
        try {
            $location = ContractDeliveryLocation::where('contract_id', $id)
                ->where('id', $locationId)
                ->firstOrFail();

            $location->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الموقع بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الموقع: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload document.
     */
    public function uploadDocument(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'document_type' => 'required|in:contract,payment_proof,identity,other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $contract = Contract::findOrFail($id);

            $path = $request->file('document')->store('contracts/documents/' . $id, 'public');

            // If it's payment proof, update the contract
            if ($request->document_type === 'payment_proof') {
                // Delete old file
                if ($contract->payment_proof) {
                    Storage::disk('public')->delete($contract->payment_proof);
                }
                
                $contract->payment_proof = $path;
                $contract->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'تم رفع المستند بنجاح',
                'path' => $path
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع المستند: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove document.
     */
    public function removeDocument(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:payment_proof'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $contract = Contract::findOrFail($id);

            if ($request->document_type === 'payment_proof' && $contract->payment_proof) {
                Storage::disk('public')->delete($contract->payment_proof);
                $contract->payment_proof = null;
                $contract->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المستند بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المستند: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique contract number.
     */
    private function generateContractNumber(): string
    {
        $prefix = 'CTR-' . date('Y') . '-';
        $lastContract = Contract::where('contract_number', 'like', $prefix . '%')
            ->orderBy('contract_number', 'desc')
            ->first();

        if ($lastContract) {
            $lastNumber = intval(substr($lastContract->contract_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get contract activities (mock method - implement with actual activity log)
     */
    private function getContractActivities($contract)
    {
        // This should be replaced with actual activity log implementation
        return collect([
            [
                'date' => $contract->created_at,
                'type' => 'created',
                'description' => 'تم إنشاء العقد',
                'user' => 'النظام'
            ],
            [
                'date' => $contract->start_date,
                'type' => 'started',
                'description' => 'بداية سريان العقد',
                'user' => 'النظام'
            ],
            [
                'date' => $contract->updated_at,
                'type' => 'updated',
                'description' => 'آخر تحديث للعقد',
                'user' => auth()->user()->name ?? 'النظام'
            ]
        ])->sortByDesc('date')->values();
    }

    /**
     * Get duration type in Arabic.
     */
    private function getDurationTypeArabic($type): string
    {
        return match($type) {
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'semi_annual' => 'نصف سنوي',
            'annual' => 'سنوي',
            default => $type
        };
    }

    /**
     * Get status in Arabic.
     */
    private function getStatusArabic($status): string
    {
        return match($status) {
            'active' => 'نشط',
            'expired' => 'منتهي',
            'pending' => 'معلق',
            'cancelled' => 'ملغي',
            default => $status
        };
    }

    /**
     * Get users for dropdown (AJAX).
     */
    public function getUsers(Request $request)
    {
        $search = $request->get('search');
        $users = User::where('status', 'active')
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'name', 'phone_number', 'email']);
        return response()->json($users);
    }

    /**
     * Get user locations (AJAX).
     */
    public function getUserLocations(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $locations = $user->savedLocations()
            ->select('id', 'name', 'full_address')
            ->get();

        return response()->json($locations);
    }

    /**
     * Check contract number availability (AJAX).
     */
    public function checkContractNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contract_number' => 'required|string',
            'exclude_id' => 'nullable|exists:contracts,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['valid' => false], 422);
        }

        $query = Contract::where('contract_number', $request->contract_number);
        
        if ($request->has('exclude_id')) {
            $query->where('id', '!=', $request->exclude_id);
        }

        $exists = $query->exists();

        return response()->json([
            'valid' => !$exists,
            'message' => $exists ? 'رقم العقد مستخدم بالفعل' : 'رقم العقد متاح'
        ]);
    }

    /**
     * Generate contract number (AJAX).
     */
    public function generateNumber()
    {
        return response()->json([
            'contract_number' => $this->generateContractNumber()
        ]);
    }

    /**
     * Get contract statistics (AJAX for dashboard).
     */
    public function statistics()
    {
        $stats = [
            'total' => Contract::count(),
            'active' => Contract::where('status', 'active')->count(),
            'expired' => Contract::where('status', 'expired')->count(),
            'pending' => Contract::where('status', 'pending')->count(),
            'cancelled' => Contract::where('status', 'cancelled')->count(),
            'total_revenue' => Contract::sum('paid_amount'),
            'expected_revenue' => Contract::sum('remaining_amount'),
            'monthly_revenue' => Contract::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('paid_amount'),
            'expiring_this_month' => Contract::where('status', 'active')
                ->whereMonth('end_date', now()->month)
                ->whereYear('end_date', now()->year)
                ->count(),
        ];

        // Monthly chart data
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = [
                'month' => now()->month($i)->format('F'),
                'contracts' => Contract::whereMonth('created_at', $i)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'revenue' => Contract::whereMonth('created_at', $i)
                    ->whereYear('created_at', now()->year)
                    ->sum('paid_amount')
            ];
        }

        $stats['monthly_data'] = $monthlyData;

        return response()->json($stats);
    }
    /**
 * Store a new payment for the contract.
 */
public function storePayment(Request $request, $id)
{
    $contract = Contract::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'amount' => 'required|numeric|min:0.01|max:' . $contract->remaining_amount,
        'payment_date' => 'required|date',
        'payment_method' => 'required|in:cash,card,bank_transfer,wallet',
        'status' => 'required|in:completed,pending,failed',
        'transaction_id' => 'nullable|string|max:100',
        'reference_number' => 'nullable|string|max:100',
        'notes' => 'nullable|string|max:500',
        'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    DB::beginTransaction();

    try {
        $data = $request->all();
        
        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('payments/receipts/' . $contract->id, 'public');
            $data['receipt_path'] = $path;
        }

        // Create payment
        $payment = $contract->payments()->create([
            'user_id' => $contract->user_id,
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'payment_method' => $data['payment_method'],
            'status' => $data['status'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'reference_number' => $data['reference_number'] ?? null,
            'notes' => $data['notes'] ?? null,
            'receipt_path' => $data['receipt_path'] ?? null,
        ]);

        // Update contract paid amount if payment is completed
        if ($data['status'] === 'completed') {
            $contract->paid_amount += $data['amount'];
            $contract->remaining_amount = $contract->total_amount - $contract->paid_amount;
            $contract->save();
        }

        DB::commit();

        return redirect()->route('admin.contracts.payments', $contract->id)
            ->with('success', 'تم تسجيل الدفعة بنجاح');

    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->back()
            ->with('error', 'حدث خطأ أثناء تسجيل الدفعة: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Export payments.
 */
public function exportPayments(Request $request, $id)
{
    $contract = Contract::findOrFail($id);
    
    $query = $contract->payments();

    // Apply filters
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('payment_method')) {
        $query->where('payment_method', $request->payment_method);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('payment_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('payment_date', '<=', $request->date_to);
    }

    $payments = $query->orderBy('payment_date', 'desc')->get();

    $format = $request->get('format', 'csv');

    switch ($format) {
        case 'pdf':
            return $this->exportPaymentsPdf($contract, $payments);
        case 'excel':
            return $this->exportPaymentsExcel($contract, $payments);
        case 'csv':
        default:
            return $this->exportPaymentsCsv($contract, $payments);
    }
}

/**
 * Export payments as CSV.
 */
private function exportPaymentsCsv($contract, $payments)
{
    $filename = 'payments_' . $contract->contract_number . '_' . now()->format('Y-m_d') . '.csv';
    $handle = fopen('php://temp', 'w+');

    // Add UTF-8 BOM for Arabic support
    fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

    // Headers
    fputcsv($handle, [
        'رقم العملية',
        'التاريخ',
        'المبلغ',
        'طريقة الدفع',
        'الحالة',
        'رقم المرجع',
        'ملاحظات'
    ]);

    // Data
    foreach ($payments as $payment) {
        fputcsv($handle, [
            $payment->transaction_id ?? '—',
            $payment->payment_date->format('Y-m-d'),
            number_format($payment->amount, 2) . ' ر.س',
            $this->getPaymentMethodArabic($payment->payment_method),
            $this->getPaymentStatusArabic($payment->status),
            $payment->reference_number ?? '—',
            $payment->notes ?? '—'
        ]);
    }

    rewind($handle);
    $content = stream_get_contents($handle);
    fclose($handle);

    return response($content)
        ->header('Content-Type', 'text/csv; charset=utf-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}

/**
 * Export payments as PDF.
 */
private function exportPaymentsPdf($contract, $payments)
{
    $pdf = Pdf::loadView('Admin.contracts.payments_pdf', [
        'contract' => $contract,
        'payments' => $payments
    ]);

    return $pdf->download('payments_' . $contract->contract_number . '_' . now()->format('Y-m-d') . '.pdf');
}

/**
 * Export payments as Excel.
 */
private function exportPaymentsExcel($contract, $payments)
{
    // You can use a package like maatwebsite/excel
    // This is a placeholder
    return redirect()->back()->with('error', 'ميزة تصدير Excel قيد التطوير');
}

/**
 * Get payment method in Arabic.
 */
private function getPaymentMethodArabic($method)
{
    return match($method) {
        'cash' => 'نقدي',
        'card' => 'بطاقة',
        'bank_transfer' => 'تحويل بنكي',
        'wallet' => 'محفظة',
        default => $method
    };
}

/**
 * Get payment status in Arabic.
 */
private function getPaymentStatusArabic($status)
{
    return match($status) {
        'completed' => 'مكتملة',
        'pending' => 'معلقة',
        'failed' => 'فاشلة',
        'refunded' => 'مسترجعة',
        default => $status
    };
}
}
