<?php

namespace App\Http\Resources\Driver;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverShortResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $driver=Driver::findOrFail($this->id);
        return [
            'id' => $driver->id,

            /* ================= User ================= */
            'user' => [
                'id' => $driver->user?->id,
                'name' => $driver->user?->name,
                'phone' => $driver->user?->full_phone,
                'status' => $driver->user?->status,
                'avatar' => $driver->user?->avatar ? asset('storage/' . ltrim($driver->user->avatar, '/')) : null,
            ],

            /* ================= Personal ================= */
            'citizenship' => $driver->citizenship ?? null,
            'country_id' => $driver->country_id ?? null,
            'date_of_birth' => $driver->date_of_birth ?? null,

            /* ================= Identity ================= */
            'national_id' => $driver->national_id ?? null,
            'id_number' => $driver->id_number ?? null,
            'iqama_number' => $driver->iqama_number ?? null,
            'iqama_expiry_date' => $driver->iqama_expiry_date ?? null,

            /* ================= Images ================= */
            'personal_photo' => $driver->personal_photo ? $this->image($driver->personal_photo) : null,

            /* ================= Vehicle ================= */
            'vehicle_size' => $driver->vehicle_size ?? null,
            'is_vehicle_owner' => (bool) $driver->is_vehicle_owner,
            'vehicle_plate_number' => $driver->vehicle_plate_number ?? null,
            'vehicle_registration_number' => $driver->vehicle_registration_number ?? null,
            'vehicle_residency_number' => $driver->vehicle_residency_number ?? null,
            'vehicle_registration_image' => $driver->vehicle_registration_image ? $this->image($driver->vehicle_registration_image) : null,

            /* ================= Work Preferences ================= */
            'preferred_working_hours' => $driver->preferred_working_hours ?? null,
            'max_daily_orders' => $driver->max_daily_orders ?? null,
            'radius_km' => $driver->radius_km ?? null,

            /* ================= Status ================= */
            'status' => $driver->status,
            'is_active' => (bool) $driver->is_active,
            'is_verified' => (bool) $driver->is_verified,
            'verified_at' => $driver->verified_at,
            'rejection_reason' => $driver->rejection_reason,

            'currect_location'=>$driver->currectLocation??null,

            /* ================= Meta ================= */
            'created_at' => $driver->created_at?->toDateTimeString(),
            'updated_at' => $driver->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * Return full asset URL for image
     */
    private function image(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // إذا كان المسار يحتوي بالفعل على رابط كامل
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}