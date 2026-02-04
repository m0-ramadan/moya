<?php

namespace App\Services\Wallet;

use App\Models\Wallet\LedgerEntry;
use App\Models\Wallet\IdempotencyKey;
use App\Services\Security\FraudDetector;
use App\Services\Wallet\LedgerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class AbstractWalletService
{
    protected LedgerService $ledgerService;
    protected FraudDetector $fraudDetector;

    public function __construct(
        LedgerService $ledgerService,
        FraudDetector $fraudDetector
    ) {
        $this->ledgerService = $ledgerService;
        $this->fraudDetector = $fraudDetector;
    }

    /**
     * Credit wallet
     */
    protected function credit(
        $wallet,
        float $amount,
        string $type,
        string $description,
        array $metadata = [],
        array $ownerInfo = []
    ): LedgerEntry {
        return $this->ledgerService->credit(
            $wallet,
            $amount,
            $type,
            $description,
            $metadata,
            $ownerInfo
        );
    }

    /**
     * Debit wallet
     */
    protected function debit(
        $wallet,
        float $amount,
        string $type,
        string $description,
        array $metadata = [],
        array $ownerInfo = []
    ): LedgerEntry {
        return $this->ledgerService->debit(
            $wallet,
            $amount,
            $type,
            $description,
            $metadata,
            $ownerInfo
        );
    }

    /**
     * Hold amount
     */
    protected function hold(
        $wallet,
        float $amount,
        string $description,
        string $reference,
        array $metadata = [],
        array $ownerInfo = [],
        int $expiresInHours = 24
    ): LedgerEntry {
        return DB::transaction(function () use ($wallet, $amount, $description, $reference, $metadata, $ownerInfo, $expiresInHours) {
            $lockedWallet = $this->lockWallet($wallet);

            if ($lockedWallet->available_balance < $amount) {
                throw new \Exception('رصيد غير كافي للحجز');
            }

            $ledgerEntry = $this->debit(
                $lockedWallet,
                $amount,
                LedgerEntry::TYPE_HOLD,
                $description,
                array_merge([
                    'hold_reference' => $reference,
                    'hold_expires_at' => now()->addHours($expiresInHours)
                ], $metadata),
                $ownerInfo
            );

            $lockedWallet->updateHeldBalance($amount, 'increment');

            $ledgerEntry->update([
                'expires_at' => now()->addHours($expiresInHours),
                'available_balance_before' => $lockedWallet->available_balance + $amount,
                'available_balance_after' => $lockedWallet->available_balance
            ]);

            return $ledgerEntry;
        });
    }

    /**
     * Release hold
     */
    protected function releaseHold(
        string $reference,
        bool $complete = true,
        array $metadata = []
    ): array {
        return DB::transaction(function () use ($reference, $complete, $metadata) {
            $holdEntry = LedgerEntry::where('reference', $reference)
                ->where('type', LedgerEntry::TYPE_HOLD)
                ->where('status', LedgerEntry::STATUS_PENDING)
                ->lockForUpdate()
                ->first();

            if (!$holdEntry) {
                throw new \Exception('حجز غير موجود أو تم معالجته');
            }

            $wallet = $this->getWalletByType($holdEntry->wallet_type, $holdEntry->wallet_id);

            if ($complete) {
                // Complete the hold
                $paymentEntry = $this->debit(
                    $wallet,
                    $holdEntry->amount,
                    LedgerEntry::TYPE_PAYMENT,
                    'دفع: ' . $holdEntry->description,
                    array_merge([
                        'hold_reference' => $reference,
                        'original_hold_id' => $holdEntry->id
                    ], $metadata),
                    [
                        'owner_type' => $holdEntry->owner_type,
                        'owner_id' => $holdEntry->owner_id
                    ]
                );

                $holdEntry->markCompleted(['completed_as' => 'payment']);
                $wallet->updateHeldBalance($holdEntry->amount, 'decrement');

                $result = [
                    'action' => 'completed',
                    'payment_entry' => $paymentEntry
                ];
            } else {
                // Release hold
                $releaseEntry = $this->credit(
                    $wallet,
                    $holdEntry->amount,
                    LedgerEntry::TYPE_RELEASE,
                    'إلغاء حجز: ' . $holdEntry->description,
                    array_merge([
                        'hold_reference' => $reference,
                        'original_hold_id' => $holdEntry->id
                    ], $metadata),
                    [
                        'owner_type' => $holdEntry->owner_type,
                        'owner_id' => $holdEntry->owner_id
                    ]
                );

                $holdEntry->markFailed('released');
                $wallet->updateHeldBalance($holdEntry->amount, 'decrement');

                $result = [
                    'action' => 'released',
                    'release_entry' => $releaseEntry
                ];
            }

            return $result;
        });
    }

    /**
     * Process with idempotency
     */
public function processWithIdempotency(
    string $key,
    array $requestData,
    callable $processCallback,
    string $ownerType = null,
    int $ownerId = null,
    int $ttl = 3600
) {
    $requestHash = md5(json_encode($requestData));

    $idempotencyKey = IdempotencyKey::acquireLock(
        $key,
        $requestHash,
        $ttl,
        $ownerType,
      //  $ownerId
    );
dd($idempotencyKey);
    if (!$idempotencyKey) {
        throw new \Exception('العملية قيد المعالجة حالياً');
    }

    if ($idempotencyKey->status === IdempotencyKey::STATUS_COMPLETED) {
        $resource = $this->getResourceByIdempotency($idempotencyKey);
        return $resource; // رجع الـ LedgerEntry اللي سبق تنفيذ العملية
    }

    try {
        DB::beginTransaction();

        $result = $processCallback();

        DB::commit();

        $idempotencyKey->completeWithResponse(
            md5(json_encode($result)),
            get_class($result),
            $result->id
        );

        return $result;
    } catch (\Exception $e) {
        DB::rollBack();
        $idempotencyKey->markFailed();
        Log::error('Idempotent processing failed', [
            'key' => $key,
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}


    /**
     * Generate unique reference
     */
    protected function generateReference(string $prefix): string
    {
        return $prefix . '-' . now()->format('YmdHis') . '-' . Str::random(6);
    }

    /**
     * Abstract methods to be implemented
     */
    abstract protected function lockWallet($wallet);
    abstract protected function getWalletByType(string $walletType, int $walletId);
    abstract protected function getResourceByIdempotency(IdempotencyKey $key);
}
