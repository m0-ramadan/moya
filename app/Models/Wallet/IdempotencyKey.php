<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IdempotencyKey extends Model
{
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'key',
        'request_hash',
        'response_hash',
        'status',
        'processed_at',
        'expires_at',
        'resource_type',
        'resource_id',
        'owner_type',
        'owner_id'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'processed_at' => 'datetime'
    ];

    /**
     * Generate unique key
     */
    public static function generateKey(string $prefix = 'idemp'): string
    {
        return $prefix . '_' . Str::uuid();
    }

    /**
     * Atomic processing lock with owner context
     */
    public static function acquireLock(
        string $key,
        string $requestHash,
        string $ownerType = null,
        int $ownerId = null,
        int $ttl = 3600
    ): ?self {
        return DB::transaction(function () use ($key, $requestHash, $ownerType, $ownerId, $ttl) {
            // Try to find existing completed request
            $existing = self::where('key', $key)
                ->where('status', self::STATUS_COMPLETED)
                ->where('expires_at', '>', now())
                ->when($ownerType, function ($query, $type) use ($ownerId) {
                    $query->where('owner_type', $type)
                        ->where('owner_id', $ownerId);
                })
                ->first();

            if ($existing) {
                return $existing;
            }

            // Check for processing request
            $processing = self::where('key', $key)
                ->where('status', self::STATUS_PROCESSING)
                ->where('expires_at', '>', now())
                ->when($ownerType, function ($query, $type) use ($ownerId) {
                    $query->where('owner_type', $type)
                        ->where('owner_id', $ownerId);
                })
                ->lockForUpdate()
                ->first();

            if ($processing) {
                return null;
            }

            // Create new processing request
            return self::create([
                'key' => $key,
                'request_hash' => $requestHash,
                'status' => self::STATUS_PROCESSING,
                'expires_at' => now()->addSeconds($ttl),
                'owner_type' => $ownerType,
                'owner_id' => $ownerId
            ]);
        });
    }

    /**
     * Mark as completed with response
     */
    public function completeWithResponse(string $responseHash, string $resourceType = null, int $resourceId = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'response_hash' => $responseHash,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'processed_at' => now()
        ]);
    }

    /**
     * Mark as failed
     */
    public function markFailed(): void
    {
        $this->update(['status' => self::STATUS_FAILED]);
    }

    /**
     * Check if key is valid
     */
    public function isValid(): bool
    {
        return $this->status === self::STATUS_COMPLETED &&
            $this->expires_at &&
            $this->expires_at->isFuture();
    }

    /**
     * Clean up old idempotency keys
     */
    public static function cleanup(int $hours = 24): int
    {
        return self::where('expires_at', '<', now()->subHours($hours))->delete();
    }
}
