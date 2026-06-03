<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'contract_id',
        'payment_number',
        'amount',
        'payment_date',
        'payment_method', // 'cash', 'credit_card', 'bank_transfer'
        'transaction_id',
        'status', // 'completed', 'pending', 'failed', 'refunded'
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            $sequence = null;

            if (empty($payment->payment_number)) {
                $sequence ??= static::nextSequenceValue(true);
                $payment->payment_number = static::formatPaymentNumber($sequence);
            }

            if (empty($payment->transaction_id)) {
                $sequence ??= static::nextSequenceValue(true);
                $payment->transaction_id = static::formatTransactionId($sequence);
            }
        });
    }

    // علاقة الدفع مع المستخدم
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // علاقة الدفع مع العقد
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public static function previewNextTransactionId(): string
    {
        return static::formatTransactionId(static::nextSequenceValue());
    }

    protected static function nextSequenceValue(bool $lockForUpdate = false): int
    {
        $query = static::query()->orderByDesc('id');

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        $lastId = $query->value('id');

        return ($lastId ?? 0) + 1;
    }

    protected static function formatPaymentNumber(int $sequence): string
    {
        return 'PAY-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }

    protected static function formatTransactionId(int $sequence): string
    {
        return 'TRX-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }
}
