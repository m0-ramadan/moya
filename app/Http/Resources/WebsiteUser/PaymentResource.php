<?php

namespace App\Http\Resources\WebsiteUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class PaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'payment_number' => $this->payment_number,
            'amount' => (float) $this->amount,
            'payment_date' => $this->payment_date->format('Y-m-d H:i:s'),
            'payment_method' => $this->payment_method,
            'payment_method_arabic' => $this->getPaymentMethodArabic(),
            'transaction_id' => $this->transaction_id,
            'status' => $this->status,
            'status_arabic' => $this->getStatusArabic(),
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // العلاقات
            'user' => new UserResource($this->whenLoaded('user')),
            'contract' => new ContractResource($this->whenLoaded('contract')),
        ];
    }

    private function getPaymentMethodArabic()
    {
        $methods = [
            'cash' => 'نقدي',
            'credit_card' => 'بطاقة ائتمان',
            'bank_transfer' => 'تحويل بنكي',
            'wallet' => 'محفظة'
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    private function getStatusArabic()
    {
        $statuses = [
            'completed' => 'مكتمل',
            'pending' => 'معلق',
            'failed' => 'فشل',
            'refunded' => 'تم الاسترجاع'
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}
