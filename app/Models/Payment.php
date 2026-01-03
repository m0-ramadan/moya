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
}
