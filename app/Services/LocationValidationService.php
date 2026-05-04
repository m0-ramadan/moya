<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationValidationService
{
    /**
     * التحقق من أن الإحداثيات داخل مدينة معينة
     */
    public function isWithinCity(float $latitude, float $longitude, string $city = 'الرياض'): bool
    {
        try {
            // استخدام Google Maps Geocoding API لعكس الإحداثيات
            $apiKey = config('services.google_maps.api_key');
            
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$latitude},{$longitude}",
                'key' => $apiKey,
                'language' => 'ar',
                'result_type' => 'locality|administrative_area_level_1',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK') {
                    foreach ($data['results'] as $result) {
                        // البحث عن اسم المدينة في components
                        foreach ($result['address_components'] as $component) {
                            if (in_array('administrative_area_level_1', $component['types']) || 
                                in_array('locality', $component['types'])) {
                                
                                $cityName = $component['long_name'];
                                $cityNameShort = $component['short_name'];
                                
                                Log::info("Location check - Found city: {$cityName}", [
                                    'lat' => $latitude,
                                    'lng' => $longitude,
                                    'city' => $cityName,
                                ]);

                                // التحقق من أن المدينة هي الرياض
                                return $this->isRiyadh($cityName) || $this->isRiyadh($cityNameShort);
                            }
                        }
                    }
                }
            }

            // إذا فشل API، نستخدم طريقة بديلة
            return $this->fallbackCheck($latitude, $longitude);
            
        } catch (\Exception $e) {
            Log::error('Location validation error: ' . $e->getMessage());
            
            // في حالة الخطأ، نستخدم الطريقة البديلة
            return $this->fallbackCheck($latitude, $longitude);
        }
    }

    /**
     * التحقق مما إذا كان النص يشير إلى الرياض
     */
    private function isRiyadh(string $cityName): bool
    {
        $riyadhVariations = [
            'الرياض',
            'Riyadh',
            'Ar Riyad',
            'Ar Riyāḑ',
            'الرياض منطقة',
            'Riyadh Region',
        ];

        $cityName = trim($cityName);
        
        foreach ($riyadhVariations as $variation) {
            if (mb_stripos($cityName, $variation) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * طريقة احتياطية للتحقق (نطاق تقريبي للرياض)
     */
    private function fallbackCheck(float $latitude, float $longitude): bool
    {
        // النطاق التقريبي لمدينة الرياض
        $riyadhBounds = [
            'north' => 25.0000,  // الحد الشمالي
            'south' => 24.2000,  // الحد الجنوبي
            'east' => 47.2000,   // الحد الشرقي
            'west' => 46.3000,   // الحد الغربي
        ];

        $isInside = ($latitude >= $riyadhBounds['south'] && 
                     $latitude <= $riyadhBounds['north'] && 
                     $longitude >= $riyadhBounds['west'] && 
                     $longitude <= $riyadhBounds['east']);

        Log::info('Fallback location check', [
            'lat' => $latitude,
            'lng' => $longitude,
            'is_inside_riyadh' => $isInside,
        ]);

        return $isInside;
    }

    /**
     * الحصول على اسم المدينة من الإحداثيات
     */
    public function getCityName(float $latitude, float $longitude): ?string
    {
        try {
            $apiKey = config('services.google_maps.api_key');
            
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$latitude},{$longitude}",
                'key' => $apiKey,
                'language' => 'ar',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK') {
                    foreach ($data['results'][0]['address_components'] ?? [] as $component) {
                        if (in_array('administrative_area_level_1', $component['types'])) {
                            return $component['long_name'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Get city name error: ' . $e->getMessage());
        }

        return null;
    }
}