<?php

namespace App\Models\Wallet;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LedgerEntry extends Model
{
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_DEPOSIT_PENDING = 'deposit_pending';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_TRANSFER_IN = 'transfer_in';
    const TYPE_TRANSFER_OUT = 'transfer_out';
    const TYPE_PAYMENT = 'payment';
    const TYPE_HOLD = 'hold';
    const TYPE_RELEASE = 'release';
    const TYPE_REFUND = 'refund';
    const TYPE_FEE = 'fee';
    const TYPE_EARNING = 'earning';
    const TYPE_CASHOUT = 'cashout';
    const TYPE_COMMISSION = 'commission';
    const TYPE_PAYOUT = 'payout';
    const TYPE_ADJUSTMENT = 'adjustment';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_APPROVED = 'approved';
    const STATUS_PROCESSING = 'processing';

    const OWNER_TYPE_USER = 'user';
    const OWNER_TYPE_DRIVER = 'driver';
    const OWNER_TYPE_SYSTEM = 'system';

    protected $fillable = [
        'wallet_id',
        'wallet_type',
        'owner_type',
        'owner_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'available_balance_before',
        'available_balance_after',
        'payment_method',
        'payment_transaction_id',
        'description',
        'status',
        'reference',
        'related_entry_id',
        'related_owner_type',
        'related_owner_id',
        'metadata',
        'ip_address',
        'user_agent',
        'expires_at',
        'processed_at',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'available_balance_before' => 'decimal:2',
        'available_balance_after' => 'decimal:2',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime'
    ];

    /**
     * Get the owning model
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get related owner
     */
    public function relatedOwner(): MorphTo
    {
        return $this->morphTo(null, 'related_owner_type', 'related_owner_id');
    }

    /**
     * Get related ledger entry
     */
    public function relatedEntry()
    {
        return $this->belongsTo(LedgerEntry::class, 'related_entry_id');
    }

    /**
     * Get wallet
     */
    public function wallet()
    {
        if ($this->wallet_type === 'user') {
            return $this->belongsTo(UserWallet::class, 'wallet_id');
        }

        return $this->belongsTo(DriverWallet::class, 'wallet_id');
    }

    /**
     * Get approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeByOwnerType($query, $type)
    {
        return $query->where('owner_type', $type);
    }

    public function scopeByOwner($query, $ownerType, $ownerId)
    {
        return $query->where('owner_type', $ownerType)
            ->where('owner_id', $ownerId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_PENDING)
            ->where('expires_at', '<', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Check if entry is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Mark as completed
     */
    public function markCompleted(array $additionalData = []): bool
    {
        return $this->update(array_merge([
            'status' => self::STATUS_COMPLETED,
            'processed_at' => now()
        ], $additionalData));
    }

    /**
     * Mark as processing
     */
    public function markProcessing(): bool
    {
        return $this->update([
            'status' => self::STATUS_PROCESSING,
            'processed_at' => now()
        ]);
    }

    /**
     * Mark as approved
     */
    public function markApproved(int $approverId): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $approverId,
            'metadata' => array_merge($this->metadata ?? [], [
                'approved_by_user_id' => $approverId,
                'approved_at' => now()->toIso8601String()
            ])
        ]);
    }

    /**
     * Mark as failed
     */
    public function markFailed(string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_FAILED,
            'metadata' => array_merge($this->metadata ?? [], [
                'failure_reason' => $reason,
                'failed_at' => now()->toIso8601String()
            ])
        ]);
    }

    /**
     * Get transaction direction
     */
    public function getDirectionAttribute(): string
    {
        $creditTypes = [
            self::TYPE_DEPOSIT,
            self::TYPE_TRANSFER_IN,
            self::TYPE_REFUND,
            self::TYPE_RELEASE,
            self::TYPE_EARNING,
            self::TYPE_COMMISSION
        ];

        return in_array($this->type, $creditTypes) ? 'credit' : 'debit';
    }
    public static function acquireLock(string $key, string $requestHash, int $ttl, string $ownerType = null, int $ownerId = null)
{
    try {
        // تحقق من وجود lock مسبق
        $existing = self::where('key', $key)
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return null; // Already locked
        }

        // إنشاء lock جديد
        return self::create([
            'key' => $key,
            'request_hash' => $requestHash,
            'status' => self::STATUS_PROCESSING,
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'expires_at' => now()->addSeconds($ttl)
        ]);
    } catch (\Exception $e) {
        Log::error('IdempotencyKey acquireLock failed: ' . $e->getMessage(), [
            'key' => $key,
            'owner_type' => $ownerType,
            'owner_id' => $ownerId
        ]);
        return null; // null بدل string
    }
}

}
