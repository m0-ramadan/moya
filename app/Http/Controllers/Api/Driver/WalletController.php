<?php

namespace App\Http\Controllers\Api\Driver;


use App\Http\Controllers\Controller;
use App\Models\Driver\Driver;
use App\Services\Wallet\DriverWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    private DriverWalletService $walletService;

    public function __construct(DriverWalletService $walletService)
    {
        $this->walletService = $walletService;
        $this->middleware('auth:sanctum');
        $this->middleware('driver.only');
    }

    /**
     * Get wallet balance
     */
    public function getBalance(Request $request)
    {
        $user = $request->user();
        $driver = $user->driver;

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية سائق'
            ], 403);
        }

        try {
            $balance = $this->walletService->getBalance($driver);

            return response()->json([
                'success' => true,
                'wallet' => $balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في الحصول على رصيد المحفظة'
            ], 500);
        }
    }

    /**
     * Cash out earnings
     */
    public function cashOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'description' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $driver = $user->driver;

        try {
            $entry = $this->walletService->cashOut($driver, $request->amount, [
                'bank_account_id' => $request->bank_account_id,
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'entry' => [
                    'id' => $entry->id,
                    'reference' => $entry->reference,
                    'amount' => $entry->amount,
                    'status' => $entry->status
                ],
                'message' => 'تم تقديم طلب السحب بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get earnings history
     */
    public function getEarnings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:earning,commission,cashout',
            'status' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $driver = $user->driver;

        try {
            $entries = $this->walletService->getTransactionHistory($driver, [
                'type' => $request->type,
                'status' => $request->status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'limit' => $request->limit ?? 20
            ]);

            // Calculate totals
            $totalEarnings = $driver->ledgerEntries()
                ->where('type', 'earning')
                ->where('status', 'completed')
                ->sum('amount');

            $totalCashouts = $driver->ledgerEntries()
                ->where('type', 'cashout')
                ->where('status', 'completed')
                ->sum('amount');

            return response()->json([
                'success' => true,
                'entries' => $entries,
                'summary' => [
                    'total_earnings' => (float) $totalEarnings,
                    'total_cashouts' => (float) $totalCashouts,
                    'available_balance' => $driver->wallet()->available_balance
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في الحصول على سجل الأرباح'
            ], 500);
        }
    }
}
