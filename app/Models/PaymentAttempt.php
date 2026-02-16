<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAttempt extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'reference_id',
        'payment_url',
        'amount_cents',
        'currency',
        'status',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function markAsPaid(array $response = [])
    {
        $this->update([
            'status' => 'paid',
            'gateway_response' => $response,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(array $response = [])
    {
        $this->update([
            'status' => 'failed',
            'gateway_response' => $response,
        ]);
    }
}
