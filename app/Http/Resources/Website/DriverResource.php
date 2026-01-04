<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class DriverResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'father_name' => $this->father_name,
            'grandfather_name' => $this->grandfather_name,
            'family_name' => $this->family_name,
            'phone' => $this->phone,
            'id_number' => $this->id_number,
            'license_number' => $this->license_number,
            'issue_date' => $this->issue_date,
            'expiry_date' => $this->expiry_date,
            'national_id' => $this->national_id,
            'date_of_birth' => $this->date_of_birth,
            'blood_type' => $this->blood_type,
            'status' => $this->status,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'average_rating' => (float) $this->average_rating,
            'total_ratings' => (int) $this->total_ratings,
            'total_orders' => (int) $this->total_orders,
            'is_active' => (bool) $this->is_active,
            'allow_notifications' => (bool) $this->allow_notifications,
            // 'vehicle' => $this->whenLoaded('vehicle', function () {
            //     return new VehicleResource($this->vehicle);
            // }),
            // 'ratings' => $this->whenLoaded('ratings', function () {
            //     return DriverRatingResource::collection($this->ratings);
            // }),
            'statistics' => [
                'total_orders' => $this->orders()->count(),
                'completed_orders' => $this->orders()->where('status', 'completed')->count(),
                'cancelled_orders' => $this->orders()->where('status', 'cancelled')->count(),
                'total_earnings' => $this->orders()->where('status', 'completed')->sum('driver_amount'),
            ],
        ];
    }
}