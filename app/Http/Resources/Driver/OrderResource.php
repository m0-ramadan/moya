<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_number' => 'ORD-' . str_pad($this->id, 6, '0', STR_PAD_LEFT),
            'customer' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'phone' => $this->user->phone,
            ],
            'service' => $this->service->name,
            'water_type' => $this->waterType->name ?? null,
            'status' => [
                'id' => $this->status?->id,
                'name' => $this->status?->name,
                'color' => $this->getStatusColor($this->status?->id),
            ],
            'price' => optional($this->acceptedOffer)->price,
            'location' => [
                'address' => $this->location->address,
                'latitude' => $this->location->latitude,
                'longitude' => $this->location->longitude,
                'city' => $this->location->city,
                'area' => $this->location->area,
            ],
            'delivery_info' => [
                'estimated_time' => $this->acceptedOffer->delivery_duration_minutes ?? null,
                'actual_time' => $this->delivery_actual_time ?? null,
                'distance' => $this->delivery_distance ?? null,
            ],
            'timestamps' => [
                'created_at' => $this->created_at->format('Y-m-d H:i'),
                'accepted_at' => $this->accepted_at ?? null,
                'delivered_at' => $this->delivered_at ?? null,
            ],
            'payment' => [
                'method' => $this->payment_method,
                'status' => $this->payment_status,
                'transaction_id' => $this->transaction_id,
            ],
            'rating' => $this->userRating ? [
                'value' => $this->userRating->rating,
                'comment' => $this->userRating->comment,
            ] : null,
        ];
    }

    private function getStatusColor($statusId)
    {
        $colors = [
            1 => 'gray',   // معلق
            2 => 'blue',   // مقبول
            3 => 'orange', // جاري التوصيل
            4 => 'green',  // تم التسليم
            5 => 'red',    // منتهي
            6 => 'red',    // ملغي
        ];

        return $colors[$statusId] ?? 'gray';
    }
}
