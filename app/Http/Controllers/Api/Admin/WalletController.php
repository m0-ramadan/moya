<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Models\Wallet\LedgerEntry;
use App\Services\Wallet\DriverWalletService;
use App\Services\Withdrawal\WithdrawalService;
use App\Services\Wallet\ReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    private DriverWalletService $driverWalletService;
    private WithdrawalService $withdrawalService;
    private ReconciliationService $reconciliationService;

    public function __construct(
        DriverWalletService $driverWalletService,
        WithdrawalService $withdrawalService,
        ReconciliationService $reconciliationService
    ) {
        $this->driverWalletService = $driverWalletService;
        $this->withdrawalService = $withdrawalService;
        $this->reconciliationService = $reconciliationService;

        $this->middleware('auth:sanctum');
        $this->middleware('admin.only');
    }

    /**
     * Get pending cashouts
     */
    public function getPendingCashouts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'nullable|exists:drivers,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = LedgerEntry::where('type', LedgerEntry::TYPE_CASHOUT)
                ->where('status', LedgerEntry::STATUS_PENDING)
                ->with(['owner', 'wallet'])
                ->latest();

            if ($request->driver_id) {
                $query->where('owner_type', LedgerEntry::OWNER_TYPE_DRIVER)
                    ->where('owner_id', $request->driver_id);
            }

            if ($request->start_date) {
                $query->where('created_at', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->where('created_at', '<=', $request->end_date);
            }

            $entries = $query->paginate($request->limit ?? 20);

            // Summary
            $pendingTotal = LedgerEntry::where('type', LedgerEntry::TYPE_CASHOUT)
                ->where('status', LedgerEntry::STATUS_PENDING)
                ->sum('amount');

            return response()->json([
                'success' => true,
                'entries' => $entries,
                'summary' => [
                    'pending_count' => $entries->total(),
                    'pending_total' => (float) $pendingTotal
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في الحصول على طلبات السحب'
            ], 500);
        }
    }

    /**
     * Approve cashout
     */
    public function approveCashout(Request $request, $entryId)
    {
        $user = $request->user();

        try {
            $entry = $this->driverWalletService->approveCashout($entryId, $user->id);

            return response()->json([
                'success' => true,
                'message' => 'تم الموافقة على السحب',
                'entry' => $entry
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Reject cashout
     */
    public function rejectCashout(Request $request, $entryId)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        try {
            $entry = $this->driverWalletService->rejectCashout($entryId, $user->id, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'تم رفض طلب السحب',
                'entry' => $entry
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process withdrawals batch
     */
    public function processWithdrawalsBatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entry_ids' => 'required|array',
            'entry_ids.*' => 'integer|exists:ledger_entries,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->withdrawalService->processBatch($request->entry_ids);

            return response()->json([
                'success' => true,
                'result' => $result,
                'message' => 'تم معالجة عمليات السحب'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Reconcile wallets
     */
    public function reconcileWallets(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wallet_type' => 'nullable|in:user,driver,all',
            'wallet_ids' => 'nullable|array',
            'wallet_ids.*' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get wallets to reconcile
            $wallets = $this->getWalletsForReconciliation(
                $request->wallet_type ?? 'all',
                $request->wallet_ids ?? []
            );

            $result = $this->reconciliationService->reconcileBatch($wallets);

            return response()->json([
                'success' => true,
                'result' => $result,
                'message' => 'تم تصحيح المحافظ'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find discrepancies
     */
    public function findDiscrepancies(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'threshold' => 'nullable|numeric|min:0',
            'wallet_type' => 'nullable|in:user,driver'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $threshold = $request->threshold ?? config('wallet.reconciliation.threshold', 0.01);

            // Get all wallets
            $wallets = $this->getAllWallets($request->wallet_type);

            $discrepancies = $this->reconciliationService->findDiscrepancies($wallets);

            // Filter by threshold
            $filtered = array_filter($discrepancies, function ($item) use ($threshold) {
                return $item['absolute_difference'] > $threshold;
            });

            return response()->json([
                'success' => true,
                'discrepancies' => array_values($filtered),
                'count' => count($filtered),
                'threshold' => $threshold
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wallets for reconciliation
     */
    private function getWalletsForReconciliation(string $type, array $ids = []): array
    {
        $wallets = [];

        if (in_array($type, ['user', 'all'])) {
            $userWallets = \App\Models\Wallet\UserWallet::query();

            if (!empty($ids)) {
                $userWallets->whereIn('id', $ids);
            }

            $wallets = array_merge($wallets, $userWallets->get()->all());
        }

        if (in_array($type, ['driver', 'all'])) {
            $driverWallets = \App\Models\Wallet\DriverWallet::query();

            if (!empty($ids)) {
                $driverWallets->whereIn('id', $ids);
            }

            $wallets = array_merge($wallets, $driverWallets->get()->all());
        }

        return $wallets;
    }

    /**
     * Get all wallets
     */
    private function getAllWallets(?string $type = null): array
    {
        $wallets = [];

        if (!$type || $type === 'user') {
            $wallets = array_merge($wallets, \App\Models\Wallet\UserWallet::all()->all());
        }

        if (!$type || $type === 'driver') {
            $wallets = array_merge($wallets, \App\Models\Wallet\DriverWallet::all()->all());
        }

        return $wallets;
    }
}
