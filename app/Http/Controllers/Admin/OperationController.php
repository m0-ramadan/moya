<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use App\Models\Wallet\LedgerEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class OperationController extends Controller
{
    public function index(Request $request)
    {
        $query = LedgerEntry::query();

        $this->applyFilters($query, $request);

        $totalOperations = (clone $query)->count();
        $completedOperations = (clone $query)
            ->where('status', LedgerEntry::STATUS_COMPLETED)
            ->count();
        $pendingOperations = (clone $query)
            ->whereIn('status', [
                LedgerEntry::STATUS_PENDING,
                LedgerEntry::STATUS_PROCESSING,
                LedgerEntry::STATUS_APPROVED,
            ])
            ->count();
        $creditOperations = (clone $query)
            ->whereIn('type', $this->getCreditTypes())
            ->count();
        $totalVolume = (clone $query)->sum('amount');

        $operations = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $this->decorateOperations($operations);

        return view('Admin.operations.index', [
            'operations' => $operations,
            'totalOperations' => $totalOperations,
            'completedOperations' => $completedOperations,
            'pendingOperations' => $pendingOperations,
            'creditOperations' => $creditOperations,
            'totalVolume' => $totalVolume,
            'defaultCurrency' => config('wallet.default_currency', 'SAR'),
            'filters' => $request->only([
                'search',
                'status',
                'type',
                'owner_type',
                'wallet_type',
                'payment_method',
                'date_from',
                'date_to',
                'min_amount',
                'max_amount',
            ]),
            'typeLabels' => $this->getTypeLabels(),
            'statusLabels' => $this->getStatusLabels(),
            'ownerTypeLabels' => $this->getOwnerTypeLabels(),
            'walletTypeLabels' => $this->getWalletTypeLabels(),
            'paymentMethods' => LedgerEntry::query()
                ->whereNotNull('payment_method')
                ->distinct()
                ->orderBy('payment_method')
                ->pluck('payment_method'),
        ]);
    }

    public function show(LedgerEntry $ledgerEntry): JsonResponse
    {
        $entry = $ledgerEntry->fresh();

        if (! $entry) {
            return response()->json([
                'success' => false,
                'message' => 'العملية غير موجودة.',
            ], 404);
        }

        [$userOwners, $driverOwners] = $this->loadOwnerMaps(collect([$entry]));
        $relatedEntries = LedgerEntry::query()
            ->whereIn('id', array_filter([$entry->related_entry_id]))
            ->get()
            ->keyBy('id');
        $approvers = User::query()
            ->whereIn('id', array_filter([$entry->approved_by]))
            ->get()
            ->keyBy('id');

        $owner = $this->resolveOwnerSummary($entry->owner_type, $entry->owner_id, $userOwners, $driverOwners);
        $relatedOwner = $this->resolveOwnerSummary($entry->related_owner_type, $entry->related_owner_id, $userOwners, $driverOwners);
        $relatedEntry = $relatedEntries->get($entry->related_entry_id);
        $approver = $approvers->get($entry->approved_by);
        $canReview = $this->canReviewEntry($entry) && $entry->owner_type === LedgerEntry::OWNER_TYPE_USER;

        return response()->json([
            'success' => true,
            'entry' => [
                'id' => $entry->id,
                'reference' => $entry->reference,
                'wallet_type' => $entry->wallet_type,
                'wallet_type_label' => $this->getWalletTypeLabels()[$entry->wallet_type] ?? $entry->wallet_type,
                'wallet_id' => $entry->wallet_id,
                'owner_type' => $entry->owner_type,
                'owner_type_label' => $this->getOwnerTypeLabels()[$entry->owner_type] ?? $entry->owner_type,
                'owner' => $owner,
                'type' => $entry->type,
                'type_label' => $this->getTypeLabels()[$entry->type] ?? $entry->type,
                'direction' => $entry->direction,
                'direction_label' => $entry->direction === 'credit' ? 'دائن' : 'مدين',
                'amount' => $this->formatAmount($entry->amount),
                'amount_value' => (float) $entry->amount,
                'status' => $entry->status,
                'status_label' => $this->getStatusLabels()[$entry->status] ?? $entry->status,
                'description' => $entry->description,
                'payment_method' => $entry->payment_method,
                'payment_transaction_id' => $entry->payment_transaction_id,
                'payment_identifier' => $entry->payment_identifier,
                'balance_before' => $this->formatAmount($entry->balance_before),
                'balance_after' => $this->formatAmount($entry->balance_after),
                'available_balance_before' => $this->formatAmount($entry->available_balance_before),
                'available_balance_after' => $this->formatAmount($entry->available_balance_after),
                'related_owner' => $relatedOwner,
                'related_entry' => $relatedEntry ? [
                    'id' => $relatedEntry->id,
                    'reference' => $relatedEntry->reference,
                    'type' => $relatedEntry->type,
                    'type_label' => $this->getTypeLabels()[$relatedEntry->type] ?? $relatedEntry->type,
                    'status' => $relatedEntry->status,
                    'status_label' => $this->getStatusLabels()[$relatedEntry->status] ?? $relatedEntry->status,
                ] : null,
                'metadata' => $entry->metadata ?? [],
                'metadata_pretty' => ! empty($entry->metadata)
                    ? json_encode($entry->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null,
                'ip_address' => $entry->ip_address,
                'user_agent' => $entry->user_agent,
                'expires_at' => $entry->expires_at?->format('Y-m-d H:i:s'),
                'processed_at' => $entry->processed_at?->format('Y-m-d H:i:s'),
                'approved_at' => $entry->approved_at?->format('Y-m-d H:i:s'),
                'approved_by' => $approver?->name ?? data_get($entry->metadata, 'approved_by_admin_name'),
                'approved_by_id' => $entry->approved_by,
                'created_at' => $entry->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $entry->updated_at?->format('Y-m-d H:i:s'),
                'can_review' => $canReview,
                'approve_url' => $canReview
                    ? route('admin.users.wallet.transaction', [
                        'user' => $entry->owner_id,
                        'transactionId' => $entry->id,
                        'action' => 'approve',
                    ])
                    : null,
                'reject_url' => $canReview
                    ? route('admin.users.wallet.transaction', [
                        'user' => $entry->owner_id,
                        'transactionId' => $entry->id,
                        'action' => 'reject',
                    ])
                    : null,
            ],
        ]);
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function (Builder $builder) use ($search) {
                $builder->where('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('payment_transaction_id', 'like', "%{$search}%")
                    ->orWhere('payment_identifier', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $builder->orWhere('id', (int) $search)
                        ->orWhere('owner_id', (int) $search)
                        ->orWhere('wallet_id', (int) $search);
                }

                $builder->orWhere(function (Builder $ownerQuery) use ($search) {
                    $ownerQuery->where('owner_type', LedgerEntry::OWNER_TYPE_USER)
                        ->whereIn('owner_id', User::query()
                            ->select('id')
                            ->where(function (Builder $userQuery) use ($search) {
                                $userQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%")
                                    ->orWhere('full_phone', 'like', "%{$search}%");
                            }));
                });

                $builder->orWhere(function (Builder $ownerQuery) use ($search) {
                    $ownerQuery->where('owner_type', LedgerEntry::OWNER_TYPE_DRIVER)
                        ->whereIn('owner_id', Driver::query()
                            ->select('id')
                            ->where(function (Builder $driverQuery) use ($search) {
                                $driverQuery->where('national_id', 'like', "%{$search}%")
                                    ->orWhere('license_number', 'like', "%{$search}%")
                                    ->orWhere('vehicle_plate_number', 'like', "%{$search}%")
                                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                                        $userQuery->where('name', 'like', "%{$search}%")
                                            ->orWhere('email', 'like', "%{$search}%")
                                            ->orWhere('phone', 'like', "%{$search}%")
                                            ->orWhere('full_phone', 'like', "%{$search}%");
                                    });
                            }));
                });
            });
        }

        foreach (['status', 'type', 'owner_type', 'wallet_type', 'payment_method'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', (float) $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', (float) $request->max_amount);
        }
    }

    private function decorateOperations(LengthAwarePaginator $operations): void
    {
        [$userOwners, $driverOwners] = $this->loadOwnerMaps($operations->getCollection());

        $operations->getCollection()->transform(function (LedgerEntry $entry) use ($userOwners, $driverOwners) {
            $owner = $this->resolveOwnerSummary($entry->owner_type, $entry->owner_id, $userOwners, $driverOwners);

            $entry->setAttribute('type_label', $this->getTypeLabels()[$entry->type] ?? $entry->type);
            $entry->setAttribute('status_label', $this->getStatusLabels()[$entry->status] ?? $entry->status);
            $entry->setAttribute('wallet_type_label', $this->getWalletTypeLabels()[$entry->wallet_type] ?? $entry->wallet_type);
            $entry->setAttribute('owner_type_label', $this->getOwnerTypeLabels()[$entry->owner_type] ?? $entry->owner_type);
            $entry->setAttribute('direction_label', $entry->direction === 'credit' ? 'دائن' : 'مدين');
            $entry->setAttribute('owner_name', $owner['name']);
            $entry->setAttribute('owner_subtitle', $owner['subtitle']);
            $entry->setAttribute('owner_url', $owner['url']);
            $entry->setAttribute('can_review', $this->canReviewEntry($entry) && $entry->owner_type === LedgerEntry::OWNER_TYPE_USER);

            return $entry;
        });
    }

    private function loadOwnerMaps(Collection $entries): array
    {
        $ownerUserIds = $entries
            ->where('owner_type', LedgerEntry::OWNER_TYPE_USER)
            ->pluck('owner_id')
            ->filter()
            ->merge(
                $entries->where('related_owner_type', LedgerEntry::OWNER_TYPE_USER)
                    ->pluck('related_owner_id')
                    ->filter()
            )
            ->unique()
            ->values();

        $ownerDriverIds = $entries
            ->where('owner_type', LedgerEntry::OWNER_TYPE_DRIVER)
            ->pluck('owner_id')
            ->filter()
            ->merge(
                $entries->where('related_owner_type', LedgerEntry::OWNER_TYPE_DRIVER)
                    ->pluck('related_owner_id')
                    ->filter()
            )
            ->unique()
            ->values();

        $userOwners = User::query()
            ->whereIn('id', $ownerUserIds)
            ->get()
            ->keyBy('id');

        $driverOwners = Driver::query()
            ->with('user')
            ->whereIn('id', $ownerDriverIds)
            ->get()
            ->keyBy('id');

        return [$userOwners, $driverOwners];
    }

    private function resolveOwnerSummary(
        ?string $ownerType,
        ?int $ownerId,
        Collection $userOwners,
        Collection $driverOwners
    ): array {
        if ($ownerType === LedgerEntry::OWNER_TYPE_USER && $ownerId) {
            $user = $userOwners->get($ownerId);

            return [
                'id' => $ownerId,
                'type' => $ownerType,
                'type_label' => $this->getOwnerTypeLabels()[$ownerType] ?? $ownerType,
                'name' => $user?->name ?? 'مستخدم محذوف',
                'subtitle' => $user?->full_phone ?: $user?->email,
                'url' => $user ? route('admin.users.show', $user->id) : null,
            ];
        }

        if ($ownerType === LedgerEntry::OWNER_TYPE_DRIVER && $ownerId) {
            $driver = $driverOwners->get($ownerId);
            $driverName = $driver?->user?->name ?: 'سائق #' . $ownerId;

            return [
                'id' => $ownerId,
                'type' => $ownerType,
                'type_label' => $this->getOwnerTypeLabels()[$ownerType] ?? $ownerType,
                'name' => $driverName,
                'subtitle' => $driver?->user?->full_phone ?: $driver?->license_number,
                'url' => $driver ? route('admin.drivers.details', $driver->id) : null,
            ];
        }

        if ($ownerType === LedgerEntry::OWNER_TYPE_SYSTEM) {
            return [
                'id' => null,
                'type' => $ownerType,
                'type_label' => $this->getOwnerTypeLabels()[$ownerType] ?? $ownerType,
                'name' => 'النظام',
                'subtitle' => null,
                'url' => null,
            ];
        }

        return [
            'id' => $ownerId,
            'type' => $ownerType,
            'type_label' => $this->getOwnerTypeLabels()[$ownerType] ?? ($ownerType ?: '-'),
            'name' => 'غير محدد',
            'subtitle' => null,
            'url' => null,
        ];
    }

    private function canReviewEntry(LedgerEntry $entry): bool
    {
        if (! in_array($entry->status, [
            LedgerEntry::STATUS_PENDING,
            LedgerEntry::STATUS_PROCESSING,
        ], true)) {
            return false;
        }

        return in_array($entry->type, [
            LedgerEntry::TYPE_DEPOSIT_PENDING,
            LedgerEntry::TYPE_WITHDRAWAL,
            LedgerEntry::TYPE_CASHOUT,
        ], true);
    }

    private function getCreditTypes(): array
    {
        return [
            LedgerEntry::TYPE_DEPOSIT,
            LedgerEntry::TYPE_TRANSFER_IN,
            LedgerEntry::TYPE_REFUND,
            LedgerEntry::TYPE_RELEASE,
            LedgerEntry::TYPE_EARNING,
            LedgerEntry::TYPE_COMMISSION,
        ];
    }

    private function getTypeLabels(): array
    {
        return [
            LedgerEntry::TYPE_DEPOSIT => 'إيداع',
            LedgerEntry::TYPE_DEPOSIT_PENDING => 'إيداع معلق',
            LedgerEntry::TYPE_WITHDRAWAL => 'سحب',
            LedgerEntry::TYPE_TRANSFER_IN => 'تحويل وارد',
            LedgerEntry::TYPE_TRANSFER_OUT => 'تحويل صادر',
            LedgerEntry::TYPE_PAYMENT => 'دفع',
            LedgerEntry::TYPE_HOLD => 'حجز مبلغ',
            LedgerEntry::TYPE_RELEASE => 'فك حجز',
            LedgerEntry::TYPE_REFUND => 'استرداد',
            LedgerEntry::TYPE_FEE => 'رسوم',
            LedgerEntry::TYPE_EARNING => 'أرباح',
            LedgerEntry::TYPE_CASHOUT => 'سحب أرباح',
            LedgerEntry::TYPE_COMMISSION => 'عمولة',
            LedgerEntry::TYPE_PAYOUT => 'تحويل خارجي',
            LedgerEntry::TYPE_ADJUSTMENT => 'تسوية',
        ];
    }

    private function getStatusLabels(): array
    {
        return [
            LedgerEntry::STATUS_PENDING => 'معلقة',
            LedgerEntry::STATUS_PROCESSING => 'قيد المعالجة',
            LedgerEntry::STATUS_COMPLETED => 'مكتملة',
            LedgerEntry::STATUS_FAILED => 'فاشلة',
            LedgerEntry::STATUS_CANCELLED => 'ملغية',
            LedgerEntry::STATUS_APPROVED => 'تمت الموافقة',
        ];
    }

    private function getOwnerTypeLabels(): array
    {
        return [
            LedgerEntry::OWNER_TYPE_USER => 'عميل',
            LedgerEntry::OWNER_TYPE_DRIVER => 'سائق',
            LedgerEntry::OWNER_TYPE_SYSTEM => 'النظام',
        ];
    }

    private function getWalletTypeLabels(): array
    {
        return [
            'user' => 'محفظة عميل',
            'driver' => 'محفظة سائق',
        ];
    }

    private function formatAmount($amount): string
    {
        return number_format((float) $amount, 2);
    }
}
