<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ContractDeliveryLocation;
use App\Http\Requests\Admin\CreateContractRequest;

class ContractController extends Controller
{
    /**
     * عرض جميع العقود للمستخدم
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $contracts = Contract::where('user_id', $user->id)
            ->with(['deliveryLocations.savedLocation', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contracts,
            'message' => 'تم جلب العقود بنجاح'
        ]);
    }

    /**
     * إنشاء عقد جديد
     */
    public function store(CreateContractRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $data = $request->validated();

            // حساب تاريخ الانتهاء بناءً على نوع المدة
            $startDate = Carbon::parse($data['start_date']);
            $endDate = $this->calculateEndDate($startDate, $data['duration_type']);
            $renewalDate = $endDate->copy()->subDays(7); // تجديد قبل 7 أيام من الانتهاء

            // إنشاء العقد
            $contract = Contract::create([
                'user_id' => $user->id,
                'contract_number' => $this->generateContractNumber(),
                'contract_type' => $data['contract_type'] ?? null,
                'phone' => $data['phone'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'applicant_name' => $data['applicant_name'] ?? null,
                'duration_type' => $data['duration_type'] ?? null,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'renewal_date' => $renewalDate,
                'total_orders_limit' => $data['total_orders_limit'] ?? null,
                'remaining_orders' => $data['total_orders_limit'] ?? null,
                'total_amount' => $data['total_amount'] ?? null,
                'paid_amount' => 0,
                'remaining_amount' => $data['total_amount'] ?? null,
                'status' => 'pending', // يصبح active بعد الدفع الأول
                'notes' => $data['notes'] ?? null,
            ]);

            // إضافة مواقع التوصيل
            foreach ($data['delivery_locations'] as $location) {
                ContractDeliveryLocation::create([
                    'contract_id' => $contract->id,
                    'saved_location_id' => $location['saved_location_id'],
                    'priority' => $location['priority'] ?? 1,
                    'notes' => $location['notes'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $contract->load(['deliveryLocations.savedLocation']),
                'message' => 'تم إنشاء العقد بنجاح'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء العقد: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض تفاصيل عقد محدد
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $contract = Contract::where('user_id', $user->id)
            ->where('id', $id)
            ->with([
                'deliveryLocations.savedLocation',
                'payments',
                'orders' => function ($query) {
                    $query->with(['service', 'waterType', 'status']);
                }
            ])
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'العقد غير موجود'
            ], 404);
        }

        // حساب الإحصائيات
        $stats = [
            'total_orders_used' => $contract->total_orders_limit - $contract->remaining_orders,
            'payment_progress' => $contract->total_amount > 0 ?
                ($contract->paid_amount / $contract->total_amount) * 100 : 0,
            'days_remaining' => Carbon::parse($contract->end_date)->diffInDays(Carbon::now()),
            'can_renew' => Carbon::now()->gte($contract->renewal_date),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'contract' => $contract,
                'stats' => $stats
            ],
            'message' => 'تم جلب تفاصيل العقد بنجاح'
        ]);
    }

    /**
     * تجديد العقد
     */
    public function renew(Request $request, $id)
    {
        $user = $request->user();

        $contract = Contract::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'العقد غير موجود'
            ], 404);
        }

        if ($contract->status != 'active') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تجديد عقد غير نشط'
            ], 400);
        }

        if (Carbon::now()->lt($contract->renewal_date)) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن التجديد إلا قبل 7 أيام من انتهاء العقد'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // إنشاء عقد جديد كتجديد
            $newContract = Contract::create([
                'user_id' => $user->id,
                'contract_number' => $this->generateContractNumber(),
                'contract_type' => $contract->contract_type,
                'company_name' => $contract->company_name,
                'duration_type' => $contract->duration_type,
                'start_date' => $contract->end_date->addDay(),
                'end_date' => $this->calculateEndDate($contract->end_date->addDay(), $contract->duration_type),
                'renewal_date' => $this->calculateEndDate($contract->end_date->addDay(), $contract->duration_type)->subDays(7),
                'total_orders_limit' => $contract->total_orders_limit,
                'remaining_orders' => $contract->total_orders_limit,
                'total_amount' => $contract->total_amount,
                'paid_amount' => 0,
                'remaining_amount' => $contract->total_amount,
                'status' => 'pending',
                'notes' => "تجديد للعقد السابق رقم: {$contract->contract_number}",
            ]);

            // نسخ مواقع التوصيل
            foreach ($contract->deliveryLocations as $location) {
                ContractDeliveryLocation::create([
                    'contract_id' => $newContract->id,
                    'saved_location_id' => $location->saved_location_id,
                    'priority' => $location->priority,
                    'notes' => $location->notes,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $newContract->load(['deliveryLocations.savedLocation']),
                'message' => 'تم إنشاء عقد تجديد جديد'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تجديد العقد: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على العقد النشط للمستخدم
     */
    public function active(Request $request)
    {
        $user = $request->user();

        $contract = Contract::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::now())
            ->with(['deliveryLocations.savedLocation', 'payments'])
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد عقد نشط'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data' => $contract,
            'message' => 'تم جلب العقد النشط بنجاح'
        ]);
    }

    /**
     * إضافة دفعة للعقد
     */
    public function addPayment(Request $request, $contractId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,credit_card,bank_transfer,wallet',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();

        $contract = Contract::where('user_id', $user->id)
            ->where('id', $contractId)
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'العقد غير موجود'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // إنشاء الدفعة
            $payment = Payment::create([
                'user_id' => $user->id,
                'contract_id' => $contract->id,
                'payment_number' => $this->generatePaymentNumber(),
                'amount' => $request->amount,
                'payment_date' => Carbon::parse($request->payment_date),
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            // تحديث العقد
            $contract->paid_amount += $request->amount;
            $contract->remaining_amount -= $request->amount;

            // إذا كانت هذه أول دفعة، تفعيل العقد
            if ($contract->status == 'pending' && $contract->paid_amount > 0) {
                $contract->status = 'active';
            }

            // إذا تم سداد المبلغ كاملاً
            if ($contract->remaining_amount <= 0) {
                $contract->remaining_amount = 0;
            }

            $contract->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'payment' => $payment,
                    'contract' => $contract
                ],
                'message' => 'تمت إضافة الدفعة بنجاح'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الدفعة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إضافة موقع توصيل جديد للعقد
     */
    public function addDeliveryLocation(Request $request, $contractId)
    {
        $request->validate([
            'saved_location_id' => 'required|exists:saved_locations,id',
            'priority' => 'integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();

        $contract = Contract::where('user_id', $user->id)
            ->where('id', $contractId)
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'العقد غير موجود'
            ], 404);
        }

        $location = ContractDeliveryLocation::create([
            'contract_id' => $contract->id,
            'saved_location_id' => $request->saved_location_id,
            'priority' => $request->priority ?? 1,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'data' => $location->load('savedLocation'),
            'message' => 'تم إضافة موقع التوصيل بنجاح'
        ], 201);
    }

    /**
     * حذف موقع توصيل من العقد
     */
    public function removeDeliveryLocation(Request $request, $contractId, $locationId)
    {
        $user = $request->user();

        $contract = Contract::where('user_id', $user->id)
            ->where('id', $contractId)
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'العقد غير موجود'
            ], 404);
        }

        $location = ContractDeliveryLocation::where('contract_id', $contract->id)
            ->where('id', $locationId)
            ->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'موقع التوصيل غير موجود'
            ], 404);
        }

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف موقع التوصيل بنجاح'
        ]);
    }

    /**
     * إلغاء العقد
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();

        $contract = Contract::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'العقد غير موجود'
            ], 404);
        }

        if ($contract->status == 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'العقد ملغي بالفعل'
            ], 400);
        }

        $contract->status = 'cancelled';
        $contract->save();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء العقد بنجاح'
        ]);
    }

    /**
     * دالة مساعدة: حساب تاريخ الانتهاء
     */
    private function calculateEndDate(Carbon $startDate, $durationType)
    {
        switch ($durationType) {
            case 'monthly':
                return $startDate->copy()->addMonth();
            case 'quarterly':
                return $startDate->copy()->addMonths(3);
            case 'semi_annual':
                return $startDate->copy()->addMonths(6);
            case 'annual':
                return $startDate->copy()->addYear();
            default:
                return $startDate->copy()->addMonth();
        }
    }

    /**
     * دالة مساعدة: إنشاء رقم عقد فريد
     */
    private function generateContractNumber()
    {
        $prefix = 'CONTRACT-';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));

        return $prefix . $date . '-' . $random;
    }

    /**
     * دالة مساعدة: إنشاء رقم دفع فريد
     */
    private function generatePaymentNumber()
    {
        $prefix = 'PAY-';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));

        return $prefix . $date . '-' . $random;
    }
}
