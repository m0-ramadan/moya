<?php

namespace App\Http\Resources\WebsiteUser;

use App\Http\Resources\AppUser\UserResource;
use App\Http\Resources\WebsiteUser\ContractDeliveryLocationResource;
use App\Http\Resources\WebsiteUser\OrderResource;
use App\Http\Resources\WebsiteUser\PaymentResource;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



class ContractResource extends JsonResource
{
    private const SERVICES = [
        'service_6_ton',
        'service_12_ton',
        'service_18_ton',
        'service_19_ton',
        'service_20_ton',
        'service_32_ton',
    ];
    public function toArray($request)
    {
        $services = Service::all();
        return [
            'id' => $this->id,
            'contract_number' => $this->contract_number,
            'contract_type' => $this->contract_type,
            'company_name' => $this->company_name,
            'applicant_name' => $this->applicant_name,
            'duration_type' => $this->duration_type,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'renewal_date' => $this->renewal_date ? $this->renewal_date->format('Y-m-d') : null,
            'total_orders_limit' => $this->total_orders_limit,
            'remaining_orders' => $this->remaining_orders,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'remaining_amount' => (float) $this->remaining_amount,
            'status' => $this->status,
            'status_arabic' => $this->getStatusArabic(),
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // العلاقات
            'user' => new UserResource($this->whenLoaded('user')),
            'delivery_locations' => ContractDeliveryLocationResource::collection($this->whenLoaded('deliveryLocations')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),

            // إحصائيات
            'stats' => $this->getStats(),
            'services' => $this->getServices(),
        ];
    }
    private function getServices()
    {
        $data = [];

        foreach (self::SERVICES as $service) {
            $data[$service] = rand(1, 100);
        }

        return $data;
    }
    private function getStatusArabic()
    {
        $statuses = [
            'active' => 'نشط',
            'expired' => 'منتهي',
            'pending' => 'معلق',
            'cancelled' => 'ملغي'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    private function getStats()
    {
        $now = Carbon::now();
        $endDate = Carbon::parse($this->end_date);

        return [
            'orders_used' => $this->total_orders_limit - $this->remaining_orders,
            'orders_remaining' => $this->remaining_orders,
            'orders_percentage' => $this->total_orders_limit > 0 ?
                round((($this->total_orders_limit - $this->remaining_orders) / $this->total_orders_limit) * 100, 2) : 0,
            'payment_progress' => $this->total_amount > 0 ?
                round(($this->paid_amount / $this->total_amount) * 100, 2) : 0,
            'days_remaining' => $now->diffInDays($endDate, false),
            'is_expired' => $now->gt($endDate),
            'can_renew' => $this->renewal_date ? $now->gte($this->renewal_date) : false,
            'is_active' => $this->status === 'active' && $now->lte($endDate),
        ];
    }
}
