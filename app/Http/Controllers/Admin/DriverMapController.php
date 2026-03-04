<?php

namespace App\Http\Controllers\Admin;

use App\Models\Driver;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DriverMapController extends Controller
{
    /**
     * Display the driver map page.
     */
    public function index(Request $request)
    {
        // Get all active drivers with their current locations and current orders
        $drivers = Driver::with(['user', 'currectLocation', 'orders' => function($q) {
            $q->whereIn('order_status_id', [1, 2, 3]) // pending, accepted, in_progress
                  ->with('user')
                  ->latest();
            }])
            ->where('status', 'active')
            ->where('is_active', true)
            ->whereHas('currectLocation') // فقط السائقين الذين لديهم موقع حالي
            ->get()
            ->map(function($driver) {
                // Get current active order
                $currentOrder = $driver->orders->first();
                
                return [
                    'id' => $driver->id,
                    'name' => $driver->user->name,
                    'phone' => $driver->user->full_phone ?? $driver->user->phone,
                    'avatar' => $driver->personal_photo ? asset('storage/' . $driver->personal_photo) : null,
                    'location' => $driver->currectLocation ? [
                        'lat' => (float) $driver->currectLocation->lat,
                        'lng' => (float) $driver->currectLocation->lng,
                        'speed' => $driver->currectLocation->speed,
                        'heading' => $driver->currectLocation->heading,
                        'last_updated' => $driver->currectLocation->last_updated_at?->diffForHumans(),
                    ] : null,
                    'vehicle' => [
                        'size' => $driver->vehicle_size,
                        'plate' => $driver->vehicle_plate_number,
                        'is_owner' => $driver->is_vehicle_owner,
                    ],
                    'stats' => [
                        'orders_count' => $driver->orders()->count(),
                        'rating' => $driver->ratings()->avg('rating') ?? 0,
                        'is_verified' => $driver->is_verified,
                    ],
                    'current_order' => $currentOrder ? [
                        'id' => $currentOrder->id,
                        'status' => $currentOrder->order_status_id,
                        'status_text' => $this->getOrderStatusText($currentOrder->order_status_id),
                        'customer' => $currentOrder->user?->name,
                        'customer_phone' => $currentOrder->user?->full_phone ?? $currentOrder->user?->phone,
                        'created_at' => $currentOrder->created_at->diffForHumans(),
                    ] : null,
                ];
            });

        // Statistics
        $stats = [
            'total_active' => Driver::where('status', 'active')->where('is_active', true)->count(),
            'online_now' => Driver::whereHas('currectLocation', function($q) {
                $q->where('last_updated_at', '>=', now()->subMinutes(5));
            })->count(),
            'on_delivery' => Driver::whereHas('orders', function($q) {
                $q->whereIn('order_status_id', [2, 3]); // accepted, in_progress
            })->count(),
            'available' => Driver::whereDoesntHave('orders', function($q) {
                $q->whereIn('order_status_id', [1, 2, 3]); // ليس لديه طلب نشط
            })->whereHas('currectLocation')->count(),
        ];

        return view('Admin.drivers.map', compact('drivers', 'stats'));
    }

    /**
     * Get driver locations for AJAX refresh.
     */
    public function getLocations(Request $request)
    {
        $drivers = Driver::with(['user', 'currectLocation', 'orders' => function($q) {
                $q->whereIn('order_status_id', [1, 2, 3])
                  ->with('user')
                  ->latest();
            }])
            ->where('status', 'active')
            ->where('is_active', true)
            ->whereHas('currectLocation')
            ->get()
            ->map(function($driver) {
                $currentOrder = $driver->orders->first();
                
                return [
                    'id' => $driver->id,
                    'name' => $driver->user->name,
                    'avatar' => $driver->personal_photo ? asset('storage/' . $driver->personal_photo) : null,
                    'location' => $driver->currectLocation ? [
                        'lat' => (float) $driver->currectLocation->lat,
                        'lng' => (float) $driver->currectLocation->lng,
                        'speed' => $driver->currectLocation->speed,
                        'heading' => $driver->currectLocation->heading,
                    ] : null,
                    'vehicle' => [
                        'plate' => $driver->vehicle_plate_number,
                    ],
                    'has_order' => !is_null($currentOrder),
                    'order_status' => $currentOrder ? $this->getOrderStatusText($currentOrder->order_status_id) : null,
                    'last_update' => $driver->currectLocation->last_updated_at?->timestamp,
                ];
            });

        return response()->json([
            'success' => true,
            'drivers' => $drivers,
            'stats' => [
                'online_now' => Driver::whereHas('currectLocation', function($q) {
                    $q->where('last_updated_at', '>=', now()->subMinutes(5));
                })->count(),
                'on_delivery' => Driver::whereHas('orders', function($q) {
                    $q->whereIn('order_status_id', [2, 3]);
                })->count(),
            ]
        ]);
    }

    /**
     * Get single driver details.
     */
    public function getDriverDetails($id)
    {
        $driver = Driver::with([
            'user',
            'currectLocation',
            'orders' => function($q) {
                $q->latest()->limit(5)->with('user');
            },
            'ratings' => function($q) {
                $q->latest()->limit(10)->with('user');
            }
        ])->findOrFail($id);

        $currentOrder = $driver->orders()
            ->whereIn('order_status_id', [1, 2, 3])
            ->with('user')
            ->first();

        return response()->json([
            'success' => true,
            'driver' => [
                'id' => $driver->id,
                'name' => $driver->user->name,
                'email' => $driver->user->email,
                'phone' => $driver->user->full_phone ?? $driver->user->phone,
                'avatar' => $driver->personal_photo ? asset('storage/' . $driver->personal_photo) : null,
                'citizenship' => $driver->citizenship == 'saudi' ? 'سعودي' : 'مقيم',
                'national_id' => $driver->national_id ?? $driver->iqama_number,
                'is_verified' => $driver->is_verified,
                'status' => $driver->status,
                'location' => $driver->currectLocation ? [
                    'lat' => $driver->currectLocation->lat,
                    'lng' => $driver->currectLocation->lng,
                    'speed' => $driver->currectLocation->speed,
                    'heading' => $driver->currectLocation->heading,
                    'last_updated' => $driver->currectLocation->last_updated_at?->diffForHumans(),
                ] : null,
                'vehicle' => [
                    'size' => $driver->vehicle_size,
                    'plate' => $driver->vehicle_plate_number,
                    'registration' => $driver->vehicle_registration_number,
                ],
                'stats' => [
                    'total_orders' => $driver->orders()->count(),
                    'completed_orders' => $driver->orders()->where('order_status_id', 4)->count(),
                    'avg_rating' => number_format($driver->ratings()->avg('rating') ?? 0, 1),
                    'total_ratings' => $driver->ratings()->count(),
                    'wallet_balance' => $driver->driverWallet?->balance ?? 0,
                ],
                'current_order' => $currentOrder ? [
                    'id' => $currentOrder->id,
                    'status' => $this->getOrderStatusText($currentOrder->order_status_id),
                    'customer' => $currentOrder->user->name,
                    'customer_phone' => $currentOrder->user->full_phone ?? $currentOrder->user->phone,
                    'created_at' => $currentOrder->created_at->format('Y-m-d H:i'),
                    'address' => $currentOrder->location?->address,
                ] : null,
                'recent_orders' => $driver->orders->take(5)->map(function($order) {
                    return [
                        'id' => $order->id,
                        'date' => $order->created_at->format('Y-m-d H:i'),
                        'customer' => $order->user->name,
                        'status' => $this->getOrderStatusText($order->order_status_id),
                    ];
                }),
                'recent_ratings' => $driver->ratings->take(5)->map(function($rating) {
                    return [
                        'user' => $rating->user->name,
                        'rating' => $rating->rating,
                        'comment' => $rating->comment,
                        'date' => $rating->created_at->diffForHumans(),
                    ];
                }),
            ]
        ]);
    }

    /**
     * Get order status text.
     */
    private function getOrderStatusText($statusId)
    {
        $statuses = [
            1 => 'قيد الانتظار',
            2 => 'تم القبول',
            3 => 'جاري التوصيل',
            4 => 'مكتمل',
            5 => 'ملغي',
        ];

        return $statuses[$statusId] ?? 'غير معروف';
    }

    /**
     * Search drivers.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $drivers = Driver::with(['user', 'currectLocation'])
            ->where('status', 'active')
            ->where('is_active', true)
            ->whereHas('user', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->orWhere('vehicle_plate_number', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function($driver) {
                return [
                    'id' => $driver->id,
                    'name' => $driver->user->name,
                    'phone' => $driver->user->full_phone ?? $driver->user->phone,
                    'plate' => $driver->vehicle_plate_number,
                    'avatar' => $driver->personal_photo ? asset('storage/' . $driver->personal_photo) : null,
                    'has_location' => !is_null($driver->currectLocation),
                ];
            });

        return response()->json([
            'success' => true,
            'drivers' => $drivers
        ]);
    }

    /**
     * Filter drivers.
     */
    public function filter(Request $request)
    {
        $query = Driver::with(['user', 'currectLocation', 'orders' => function($q) {
                $q->whereIn('order_status_id', [1, 2, 3]);
            }])
            ->where('status', 'active')
            ->where('is_active', true)
            ->whereHas('currectLocation');

        // Filter by vehicle size
        if ($request->filled('vehicle_size')) {
            $query->where('vehicle_size', $request->vehicle_size);
        }

        // Filter by verification status
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified === 'true');
        }

        // Filter by order status
        if ($request->filled('order_status')) {
            if ($request->order_status === 'has_order') {
                $query->whereHas('orders', function($q) {
                    $q->whereIn('order_status_id', [1, 2, 3]);
                });
            } elseif ($request->order_status === 'available') {
                $query->whereDoesntHave('orders', function($q) {
                    $q->whereIn('order_status_id', [1, 2, 3]);
                });
            }
        }

        $drivers = $query->get()->map(function($driver) {
            $currentOrder = $driver->orders->first();
            
            return [
                'id' => $driver->id,
                'name' => $driver->user->name,
                'avatar' => $driver->personal_photo ? asset('storage/' . $driver->personal_photo) : null,
                'location' => $driver->currectLocation ? [
                    'lat' => (float) $driver->currectLocation->lat,
                    'lng' => (float) $driver->currectLocation->lng,
                ] : null,
                'has_order' => !is_null($currentOrder),
                'vehicle' => [
                    'size' => $driver->vehicle_size,
                    'plate' => $driver->vehicle_plate_number,
                ],
                'is_verified' => $driver->is_verified,
            ];
        });

        return response()->json([
            'success' => true,
            'drivers' => $drivers
        ]);
    }
}