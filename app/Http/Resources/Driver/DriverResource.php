<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            /* ================= User ================= */
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'phone' => $this->user?->full_phone,
                'status' => $this->user?->status,
                'avatar' => $this->user?->avatar ? asset('storage/' . ltrim($this->user->avatar, '/')) : null,
            ],

            /* ================= Personal ================= */
            'name' => $this->name ?? null,
            'citizenship' => $this->citizenship ?? null,
            'country_id' => $this->country_id ?? null,
            'date_of_birth' => $this->date_of_birth ?? null,

            /* ================= Identity ================= */
            'national_id' => $this->national_id ?? null,
            'id_number' => $this->id_number ?? null,
            'iqama_number' => $this->iqama_number ?? null,
            'iqama_expiry_date' => $this->iqama_expiry_date ?? null,

            /* ================= Images ================= */
            'personal_photo' => $this->personal_photo ? $this->image($this->personal_photo) : null,
            'id_image_front' => $this->id_image_front ? $this->image($this->id_image_front) : null,
            'id_image_back' => $this->id_image_back ? $this->image($this->id_image_back) : null,

            /* ================= License ================= */
            'license_number' => $this->license_number ?? null,
            'license_issue_date' => $this->license_issue_date ?? null,
            'license_expiry_date' => $this->license_expiry_date ?? null,
            'license_image_front' => $this->license_image_front ? $this->image($this->license_image_front) : null,
            'license_image_back' => $this->license_image_back ? $this->image($this->license_image_back) : null,

            /* ================= Vehicle ================= */
            'vehicle_size' => $this->vehicle_size ?? null,
            'is_vehicle_owner' => (bool) $this->is_vehicle_owner,
            'vehicle_plate_number' => $this->vehicle_plate_number ?? null,
            'vehicle_registration_number' => $this->vehicle_registration_number ?? null,
            'vehicle_residency_number' => $this->vehicle_residency_number ?? null,
            'vehicle_registration_image' => $this->vehicle_registration_image ? $this->image($this->vehicle_registration_image) : null,

            /* ================= Emergency Contact ================= */
            'emergency_contact_name' => $this->emergency_contact_name ?? null,
            'emergency_contact_phone' => $this->emergency_contact_phone ?? null,

            /* ================= Work Preferences ================= */
            'preferred_working_hours' => $this->preferred_working_hours ?? null,
            'max_daily_orders' => $this->max_daily_orders ?? null,
            'radius_km' => $this->radius_km ?? null,

            /* ================= Bank ================= */
            'bank_name' => $this->bank_name ?? null,
            'iban_number' => $this->iban_number ?? null,

            /* ================= Status ================= */
            'status' => $this->status,
            'is_active' => (bool) $this->is_active,
            'is_verified' => (bool) $this->is_verified,
            'verified_at' => $this->verified_at,
            'rejection_reason' => $this->rejection_reason,

            /* ================= Wallet ================= */
            'wallet' => [
                'balance' => optional($this->driverWallet)->balance ?? 0,
                'held_balance' => optional($this->driverWallet)->held_balance ?? 0,
                'currency' => optional($this->driverWallet)->currency ?? config('app.currency', 'SAR'),
            ],

            /* ================= Meta ================= */
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
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