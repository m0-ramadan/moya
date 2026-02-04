<?php

namespace App\Models\Wallet;


use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

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
    // إضافة هذه الدوال إذا لم تكن موجودة
public static function acquireLock(string $key, string $requestHash, int $ttl, string $ownerType)
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
            'payment_method'=>'nn',
            'request_hash' => $requestHash,
            'status' => self::STATUS_PROCESSING,
            'owner_type' => $ownerType,
            'expires_at' => now()->addSeconds($ttl)
        ]);
    } catch (\Exception $e) {
        Log::error('IdempotencyKey acquireLock failed: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'key' => $key
        ]);
        return null; // <- أهم تغيير
    }
}


    public function completeWithResponse(string $responseHash, string $resourceType = null, $resourceId = null)
    {
        return $this->update([
            'response_hash' => $responseHash,
            'status' => 'completed',
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'expires_at' => now()->addDays(7) // Keep record for 7 days
        ]);
    }

    public function markFailed()
    {
        return $this->update([
            'status' => 'failed',
            'expires_at' => now()->addHours(1) // Shorter expiration for failures
        ]);
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
