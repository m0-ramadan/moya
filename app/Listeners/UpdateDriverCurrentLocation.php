<?php

namespace App\Listeners;

use App\Models\DriverCurrentLocation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateDriverCurrentLocation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event)
    {
        try {
            $data = $event->data;

            // التحقق من البيانات الأساسية
            if (!isset($data['driver_id']) || !isset($data['lat']) || !isset($data['lng'])) {
                Log::warning('Invalid location data received', ['data' => $data]);
                return;
            }

            $driverId = $data['driver_id'];
            $lat = $data['lat'];
            $lng = $data['lng'];

            // تحديث أو إنشاء سجل الموقع الحالي
            DriverCurrentLocation::updateOrCreate(
                ['driver_id' => $driverId],
                [
                    'lat' => $lat,
                    'lng' => $lng,
                    'speed' => $data['speed'] ?? 0,
                    'heading' => $data['heading'] ?? 0,
                    'last_updated_at' => now(),
                ]
            );

            Log::info('Driver location updated successfully', [
                'driver_id' => $driverId,
                'lat' => $lat,
                'lng' => $lng,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update driver location', [
                'error' => $e->getMessage(),
                'data' => $event->data ?? null,
            ]);
        }
    }
}
