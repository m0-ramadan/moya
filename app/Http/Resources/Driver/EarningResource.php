<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class EarningResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_number' => 'ORD-' . str_pad($this->order_id, 6, '0', STR_PAD_LEFT),
            'amount' => $this->amount,
            'type' => $this->type, // order, tip, bonus, adjustment
            'description' => $this->description,
            'status' => $this->status, // pending, cleared, paid
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'date' => $this->created_at->format('Y-m-d'),
            'time' => $this->created_at->format('H:i'),
            'customer' => $this->order->user->name ?? null,
        ];
    }
}
