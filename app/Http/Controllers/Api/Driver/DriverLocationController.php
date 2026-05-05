<?php

namespace App\Http\Controllers\Api\Driver;

use App\Events\DriverLocationReceived;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DriverLocationController extends Controller
{
    /**
     * تحديث الموقع الحالي للسائق
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'accuracy' => 'nullable|numeric|min:0',
            //'battery_level' => 'nullable|numeric|between:0,100',
            'is_moving' => 'nullable|boolean',
        ]);

        $driver = Auth::user()->driver;

        if (!$driver) {
            return response()->json([
                'status' => false,
                'message' => 'Driver not found'
            ], 404);
        }

        try {
            // تحديث الموقع مباشرة
            $locationData = [
                'driver_id' => $driver->id,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'speed' => $request->speed ?? 0,
                'heading' => $request->heading ?? 0,
                'last_updated_at' => now(),
            ];

            // تحديث أو إنشاء السجل
            $driver->currectLocation()->updateOrCreate(
                ['driver_id' => $driver->id],
                $locationData
            );

            // بث الموقع عبر Pusher
            $this->broadcastLocation($driver, $locationData);

            return response()->json([
                'status' => true,
                'message' => 'Location updated successfully',
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            Log::error('Location update failed', [
                'driver_id' => $driver->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to update location'
            ], 500);
        }
    }

    /**
     * بث الموقع عبر Pusher
     */
    private function broadcastLocation($driver, $locationData)
    {
        try {
            // بث الموقع إلى قناة السائق
            broadcast(new \App\Events\DriverLocationBroadcast(
                $driver->id,
                $locationData['lat'],
                $locationData['lng'],
                $locationData['speed'],
                $locationData['heading'],
                now()
            ));

            // إذا كان السائق في طلب، بث الموقع إلى مستخدم الطلب
            $activeOrder = $driver->orders()
                ->whereIn('order_status_id', [1])
                ->latest()
                ->first();

            if ($activeOrder) {
                broadcast(new \App\Events\DriverLocationUpdated(
                    $driver,
                    $activeOrder,
                    (object) $locationData
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to broadcast location', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * جلب الموقع الحالي للسائق
     */
    public function getCurrentLocation()
    {
        $driver = Auth::user()->driver;

        if (!$driver) {
            return response()->json([
                'status' => false,
                'message' => 'Driver not found'
            ], 404);
        }

        $location = $driver->currectLocation;

        if (!$location) {
            return response()->json([
                'status' => false,
                'message' => 'No location data available'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'location' => [
                'lat' => $location->lat,
                'lng' => $location->lng,
                'speed' => $location->speed,
                'heading' => $location->heading,
                'last_updated_at' => $location->last_updated_at,
                'updated_at' => $location->updated_at,
            ]
        ]);
    }
}
