<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverCurrentLocation;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DriverMapController extends Controller
{
    /**
     * Build a formatted driver array from a Driver model.
     */
    private function formatDriver(Driver $driver): array
    {
        $currentOrder = $driver->relationLoaded('orders')
            ? $driver->orders->first()
            : $driver->orders()->whereIn('order_status_id', [1, 2, 3])->with('user')->latest()->first();

        $hasLocation = !is_null($driver->currectLocation);
        $isAvailable = $driver->is_available && !$currentOrder;

        return [
            'id'           => $driver->id,
            'name'         => $driver->full_name ?: ($driver->user?->name ?? 'سائق #' . $driver->id),
            'phone'        => $driver->phone_number ?: ($driver->user?->phone ?? ''),
            'avatar'       => $driver->personal_photo ? asset('storage/' . $driver->personal_photo) : null,
            'status'       => $driver->status,
            'is_active'    => (bool) $driver->is_active,
            'is_available' => (bool) $isAvailable,
            'is_verified'  => (bool) $driver->is_verified,
            'location'     => $hasLocation ? [
                'lat'          => (float) $driver->currectLocation->lat,
                'lng'          => (float) $driver->currectLocation->lng,
                'speed'        => (float) ($driver->currectLocation->speed ?? 0),
                'heading'      => (float) ($driver->currectLocation->heading ?? 0),
                'last_updated' => $driver->currectLocation->last_updated_at?->diffForHumans() ?? 'غير معروف',
                'last_updated_at' => $driver->currectLocation->last_updated_at?->format('Y-m-d H:i:s'),
            ] : null,
            'vehicle' => [
                'size'  => $driver->vehicle_size ?? '--',
                'plate' => $driver->vehicle_plate_number ?? 'غير محدد',
                'model' => $driver->vehicle_model ?? '',
                'year'  => $driver->vehicle_year ?? '',
            ],
            'stats' => [
                'orders_count'      => $driver->total_orders ?? 0,
                'rating'            => (float) ($driver->average_rating ?? 0),
                'total_ratings'     => $driver->total_ratings ?? 0,
                'is_verified'       => (bool) $driver->is_verified,
                'wallet_balance'    => $driver->driverWallet?->balance ?? 0,
            ],
            'has_order'   => !is_null($currentOrder),
            'order_status'=> $currentOrder ? $this->getOrderStatusText($currentOrder->order_status_id) : null,
            'current_order' => $currentOrder ? [
                'id'             => $currentOrder->id,
                'status'         => $currentOrder->order_status_id,
                'status_text'    => $this->getOrderStatusText($currentOrder->order_status_id),
                'customer'       => $currentOrder->user?->name ?? 'غير معروف',
                'customer_phone' => $currentOrder->user?->phone ?? '',
                'created_at'     => $currentOrder->created_at->diffForHumans(),
            ] : null,
        ];
    }

    /**
     * Display the driver map page.
     */
    public function index(Request $request)
    {
        try {
            // Load ALL drivers (no status restriction) with their location and current orders
            $driversQuery = Driver::with([
                'user',
                'currectLocation',
                'driverWallet',
                'orders' => function ($q) {
                    $q->whereIn('order_status_id', [1, 2, 3])
                      ->with('user')
                      ->latest();
                }
            ]);

            $drivers = $driversQuery->get()->map(fn($d) => $this->formatDriver($d));

            // Statistics
            $locationDriverIds = DriverCurrentLocation::pluck('driver_id');
            $recentLocationDriverIds = DriverCurrentLocation::where('last_updated_at', '>=', now()->subMinutes(15))
                ->pluck('driver_id');

            $stats = [
                'total_active'  => Driver::where('is_active', true)->count(),
                'total_drivers' => Driver::count(),
                'online_now'    => $recentLocationDriverIds->count(),
                'on_delivery'   => Driver::whereHas('orders', function ($q) {
                    $q->whereIn('order_status_id', [2, 3]);
                })->count(),
                'available'     => Driver::where('is_available', true)
                    ->whereDoesntHave('orders', function ($q) {
                        $q->whereIn('order_status_id', [1, 2, 3]);
                    })
                    ->count(),
                'has_location'  => $locationDriverIds->count(),
            ];

            return view('Admin.drivers.map', compact('drivers', 'stats'));

        } catch (\Exception $e) {
            Log::error('Error in driver map index: ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            return view('Admin.drivers.map', [
                'drivers' => collect([]),
                'stats'   => ['total_active' => 0, 'total_drivers' => 0, 'online_now' => 0, 'on_delivery' => 0, 'available' => 0, 'has_location' => 0]
            ])->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Get driver locations for AJAX refresh.
     */
    public function getLocations(Request $request)
    {
        try {
            $drivers = Driver::with([
                'user',
                'currectLocation',
                'orders' => function ($q) {
                    $q->whereIn('order_status_id', [1, 2, 3])->with('user')->latest();
                }
            ])->get()->map(fn($d) => $this->formatDriver($d));

            $recentLocationDriverIds = DriverCurrentLocation::where('last_updated_at', '>=', now()->subMinutes(15))
                ->pluck('driver_id');

            return response()->json([
                'success' => true,
                'drivers' => $drivers,
                'stats'   => [
                    'total_drivers' => Driver::count(),
                    'total_active'  => Driver::where('is_active', true)->count(),
                    'online_now'    => $recentLocationDriverIds->count(),
                    'on_delivery'   => Driver::whereHas('orders', function ($q) {
                        $q->whereIn('order_status_id', [2, 3]);
                    })->count(),
                    'available'     => Driver::where('is_available', true)
                        ->whereDoesntHave('orders', function ($q) {
                            $q->whereIn('order_status_id', [1, 2, 3]);
                        })->count(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getLocations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحميل المواقع: ' . $e->getMessage(),
                'drivers' => [],
                'stats'   => ['total_drivers' => 0, 'online_now' => 0, 'on_delivery' => 0, 'available' => 0]
            ], 500);
        }
    }

    /**
     * Get single driver details.
     */
    public function getDriverDetails($id)
    {
        try {
            $driver = Driver::with([
                'user',
                'currectLocation',
                'driverWallet',
                'orders' => function ($q) {
                    $q->latest()->limit(5)->with('user');
                },
                'ratings' => function ($q) {
                    $q->latest()->limit(10)->with('user');
                }
            ])->findOrFail($id);

            $currentOrder = $driver->orders()
                ->whereIn('order_status_id', [1, 2, 3])
                ->with('user')
                ->first();

            return response()->json([
                'success' => true,
                'driver'  => [
                    'id'          => $driver->id,
                    'name'        => $driver->full_name ?: ($driver->user?->name ?? 'سائق #' . $driver->id),
                    'email'       => $driver->user?->email ?? '',
                    'phone'       => $driver->phone_number ?: ($driver->user?->phone ?? ''),
                    'avatar'      => $driver->personal_photo ? asset('storage/' . $driver->personal_photo) : null,
                    'citizenship' => $driver->citizenship === 'saudi' ? 'سعودي' : 'مقيم',
                    'is_verified' => (bool) $driver->is_verified,
                    'is_active'   => (bool) $driver->is_active,
                    'is_available'=> (bool) $driver->is_available,
                    'status'      => $driver->status,
                    'location'    => $driver->currectLocation ? [
                        'lat'          => (float) $driver->currectLocation->lat,
                        'lng'          => (float) $driver->currectLocation->lng,
                        'speed'        => (float) ($driver->currectLocation->speed ?? 0),
                        'heading'      => (float) ($driver->currectLocation->heading ?? 0),
                        'last_updated' => $driver->currectLocation->last_updated_at?->diffForHumans(),
                    ] : null,
                    'vehicle' => [
                        'size'         => $driver->vehicle_size ?? '--',
                        'plate'        => $driver->vehicle_plate_number ?? 'غير محدد',
                        'registration' => $driver->vehicle_registration_number ?? '',
                        'model'        => $driver->vehicle_model ?? '',
                        'year'         => $driver->vehicle_year ?? '',
                    ],
                    'stats' => [
                        'total_orders'     => $driver->total_orders ?? 0,
                        'completed_orders' => $driver->orders()->where('order_status_id', 4)->count(),
                        'avg_rating'       => number_format($driver->average_rating ?? 0, 1),
                        'total_ratings'    => $driver->total_ratings ?? 0,
                        'wallet_balance'   => $driver->driverWallet?->balance ?? 0,
                    ],
                    'current_order' => $currentOrder ? [
                        'id'             => $currentOrder->id,
                        'status'         => $this->getOrderStatusText($currentOrder->order_status_id),
                        'customer'       => $currentOrder->user?->name ?? 'غير معروف',
                        'customer_phone' => $currentOrder->user?->phone ?? '',
                        'created_at'     => $currentOrder->created_at->format('Y-m-d H:i'),
                    ] : null,
                    'recent_orders' => $driver->orders->take(5)->map(function ($order) {
                        return [
                            'id'       => $order->id,
                            'date'     => $order->created_at->format('Y-m-d H:i'),
                            'customer' => $order->user?->name ?? 'غير معروف',
                            'status'   => $this->getOrderStatusText($order->order_status_id),
                        ];
                    }),
                    'recent_ratings' => $driver->ratings->take(5)->map(function ($rating) {
                        return [
                            'user'    => $rating->user?->name ?? 'مجهول',
                            'rating'  => $rating->rating,
                            'comment' => $rating->comment ?? '',
                            'date'    => $rating->created_at->diffForHumans(),
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getDriverDetails: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'لم يتم العثور على السائق'], 404);
        }
    }

    /**
     * Search drivers.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $drivers = Driver::with(['user', 'currectLocation'])
            ->where(function ($q) use ($query) {
                $q->where('full_name', 'LIKE', "%{$query}%")
                  ->orWhere('phone_number', 'LIKE', "%{$query}%")
                  ->orWhere('vehicle_plate_number', 'LIKE', "%{$query}%")
                  ->orWhereHas('user', function ($uq) use ($query) {
                      $uq->where('name', 'LIKE', "%{$query}%")
                         ->orWhere('phone', 'LIKE', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function ($driver) {
                return [
                    'id'           => $driver->id,
                    'name'         => $driver->full_name ?: ($driver->user?->name ?? 'سائق #' . $driver->id),
                    'phone'        => $driver->phone_number ?: ($driver->user?->phone ?? ''),
                    'plate'        => $driver->vehicle_plate_number ?? '--',
                    'avatar'       => $driver->personal_photo ? asset('storage/' . $driver->personal_photo) : null,
                    'has_location' => !is_null($driver->currectLocation),
                    'is_available' => (bool) $driver->is_available,
                ];
            });

        return response()->json(['success' => true, 'drivers' => $drivers]);
    }

    /**
     * Filter drivers.
     */
    public function filter(Request $request)
    {
        $query = Driver::with([
            'user',
            'currectLocation',
            'orders' => function ($q) {
                $q->whereIn('order_status_id', [1, 2, 3]);
            }
        ]);

        // Filter by vehicle size
        if ($request->filled('vehicle_size')) {
            $query->where('vehicle_size', $request->vehicle_size);
        }

        // Filter by verification status
        if ($request->filled('is_verified') && $request->is_verified === 'true') {
            $query->where('is_verified', true);
        }

        // Filter by availability
        if ($request->filled('order_status')) {
            if ($request->order_status === 'has_order') {
                $query->whereHas('orders', function ($q) {
                    $q->whereIn('order_status_id', [1, 2, 3]);
                });
            } elseif ($request->order_status === 'available') {
                $query->where('is_available', true)
                      ->whereDoesntHave('orders', function ($q) {
                          $q->whereIn('order_status_id', [1, 2, 3]);
                      });
            }
        }

        // Filter only drivers with location
        if ($request->boolean('with_location_only')) {
            $query->whereHas('currectLocation');
        }

        $drivers = $query->get()->map(fn($d) => $this->formatDriver($d));

        return response()->json(['success' => true, 'drivers' => $drivers]);
    }

    /**
     * Get order status text.
     */
    private function getOrderStatusText($statusId): string
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
}