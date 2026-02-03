<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverDetailResource extends JsonResource
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
                'email' => $this->user?->email,
                'phone' => $this->user?->full_phone,
                'phone_verified_at' => $this->user?->phone_verified_at,
                'status' => $this->user?->status,
                'avatar' => $this->user?->avatar ? asset('storage/' . ltrim($this->user->avatar, '/')) : null,
                'created_at' => $this->user?->created_at?->toDateTimeString(),
                'last_login_at' => $this->user?->last_login_at,
                'device_token' => $this->user?->device_token,
                'device_type' => $this->user?->device_type,
            ],

            /* ================= Personal ================= */
            'name' => $this->name ?? null,
            'citizenship' => $this->citizenship ?? null,
            'country_id' => $this->country_id ?? null,
            'date_of_birth' => $this->date_of_birth ?? null,
            'age' => $this->date_of_birth ? now()->diffInYears($this->date_of_birth) : null,

            /* ================= Identity ================= */
            'national_id' => $this->national_id ?? null,
            'id_number' => $this->id_number ?? null,
            'iqama_number' => $this->iqama_number ?? null,
            'iqama_expiry_date' => $this->iqama_expiry_date ?? null,
            'is_iqama_expired' => $this->iqama_expiry_date ? now()->gt($this->iqama_expiry_date) : false,

            /* ================= Images ================= */
            'personal_photo' => $this->personal_photo ? $this->image($this->personal_photo) : null,
            'id_image_front' => $this->id_image_front ? $this->image($this->id_image_front) : null,
            'id_image_back' => $this->id_image_back ? $this->image($this->id_image_back) : null,

            /* ================= License ================= */
            'license_number' => $this->license_number ?? null,
            'license_issue_date' => $this->license_issue_date ?? null,
            'license_expiry_date' => $this->license_expiry_date ?? null,
            'license_days_left' => $this->license_expiry_date ? now()->diffInDays($this->license_expiry_date, false) : null,
            'is_license_expired' => $this->license_expiry_date ? now()->gt($this->license_expiry_date) : false,
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

            /* ================= Statistics ================= */
            'statistics' => [
                'total_orders' => $this->orders_count ?? $this->orders()->count(),
                'completed_orders' => $this->completed_orders_count ?? $this->orders()->where('status', 'completed')->count(),
                'cancelled_orders' => $this->cancelled_orders_count ?? $this->orders()->where('status', 'cancelled')->count(),
                'avg_rating' => round($this->ratings_avg_rating ?? $this->ratings()->avg('rating') ?? 0, 2),
                'total_ratings' => $this->ratings_count ?? $this->ratings()->count(),
                'reports_count' => $this->reports_count ?? $this->reports()->count(),
            ],

            /* ================= Wallet ================= */
            'wallet' => [
                'id' => $this->driverWallet?->id,
                'balance' => optional($this->driverWallet)->balance ?? 0,
                'held_balance' => optional($this->driverWallet)->held_balance ?? 0,
                'currency' => optional($this->driverWallet)->currency ?? config('app.currency', 'SAR'),
                'status' => $this->driverWallet?->status,
                'daily_limit' => $this->driverWallet?->daily_limit,
                'monthly_limit' => $this->driverWallet?->monthly_limit,
                'total_earnings' => optional($this->driverWallet)->total_earnings ?? 0,
                'total_withdrawals' => optional($this->driverWallet)->total_withdrawals ?? 0,
                'last_transaction_at' => $this->driverWallet?->last_transaction_at,
            ],

            /* ================= Location ================= */
            'current_location' => $this->latestLocation ? [
                'latitude' => $this->latestLocation->latitude,
                'longitude' => $this->latestLocation->longitude,
                'address' => $this->latestLocation->address,
                'accuracy' => $this->latestLocation->accuracy,
                'speed' => $this->latestLocation->speed,
                'heading' => $this->latestLocation->heading,
                'is_moving' => $this->latestLocation->is_moving,
                'battery_level' => $this->latestLocation->battery_level,
                'device_timestamp' => $this->latestLocation->device_timestamp,
                'updated_at' => $this->latestLocation->updated_at?->toDateTimeString(),
            ] : null,

            /* ================= Ratings ================= */
            'recent_ratings' => $this->whenLoaded('ratings', function () {
                return $this->ratings->take(5)->map(function ($rating) {
                    return [
                        'id' => $rating->id,
                        'rating' => $rating->rating,
                        'comment' => $rating->comment,
                        'user_name' => $rating->user?->name,
                        'user_phone' => $rating->user?->full_phone,
                        'order_id' => $rating->order_id,
                        'created_at' => $rating->created_at?->toDateTimeString(),
                    ];
                });
            }),

            /* ================= Reports ================= */
            'recent_reports' => $this->whenLoaded('reports', function () {
                return $this->reports->take(5)->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'type' => $report->type,
                        'description' => $report->description,
                        'status' => $report->status,
                        'user_name' => $report->user?->name,
                        'order_id' => $report->order_id,
                        'created_at' => $report->created_at?->toDateTimeString(),
                        'resolved_at' => $report->resolved_at,
                    ];
                });
            }),

            /* ================= Recent Orders ================= */
            'recent_orders' => $this->whenLoaded('orders', function () {
                return $this->orders()->latest()->take(5)->get()->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'total_amount' => $order->total_amount,
                        'pickup_address' => $order->pickup_address,
                        'delivery_address' => $order->delivery_address,
                        'created_at' => $order->created_at?->toDateTimeString(),
                        'completed_at' => $order->completed_at,
                    ];
                });
            }),

            /* ================= Meta ================= */
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'verified_at' => $this->verified_at,
            'deleted_at' => $this->deleted_at,
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

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Load relationships for detailed view
     */
    public static function withDetailedRelations()
    {
        return function ($query) {
            $query->with([
                'user',
                'driverWallet',
                'ratings' => function ($q) {
                    $q->latest()->with('user');
                },
                'reports' => function ($q) {
                    $q->latest()->with('user');
                },
                'orders' => function ($q) {
                    $q->latest()->select(['id', 'driver_id', 'order_number', 'status', 'total_amount', 
                                         'pickup_address', 'delivery_address', 'created_at', 'completed_at']);
                },
                'latestLocation',
            ])->withCount([
                'orders',
                'ratings',
                'reports',
                'orders as completed_orders_count' => function ($q) {
                    $q->where('status', 'completed');
                },
                'orders as cancelled_orders_count' => function ($q) {
                    $q->where('status', 'cancelled');
                },
            ])->withAvg('ratings', 'rating');
        };
    }
}