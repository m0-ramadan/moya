<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleMapsService
{
    protected $apiKey;
    protected $geocodingUrl;
    protected $distanceUrl;

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
        $this->geocodingUrl = config('services.google_maps.geocoding_url');
        $this->distanceUrl = config('services.google_maps.distance_url');
    }

    /**
     * حساب المسافة والوقت بين نقطتين
     */
    public function calculateDistanceAndTime($originLat, $originLng, $destinationLat, $destinationLng, $mode = 'driving')
    {
        try {
            $response = Http::get($this->distanceUrl, [
                'origins' => "{$originLat},{$originLng}",
                'destinations' => "{$destinationLat},{$destinationLng}",
                'mode' => $mode,
                'key' => $this->apiKey,
                'language' => 'ar',
                'units' => 'metric',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] == 'OK' && !empty($data['rows'][0]['elements'][0])) {
                    $element = $data['rows'][0]['elements'][0];

                    if ($element['status'] == 'OK') {
                        return [
                            'distance' => [
                                'text' => $element['distance']['text'],
                                'value' => $element['distance']['value'], // بالمتر
                            ],
                            'duration' => [
                                'text' => $element['duration']['text'],
                                'value' => $element['duration']['value'], // بالثواني
                            ],
                            'origin_address' => $data['origin_addresses'][0] ?? null,
                            'destination_address' => $data['destination_addresses'][0] ?? null,
                        ];
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Google Maps API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * الحصول على عنوان من إحداثيات
     */
    public function reverseGeocode($lat, $lng)
    {
        try {
            $response = Http::get($this->geocodingUrl, [
                'latlng' => "{$lat},{$lng}",
                'key' => $this->apiKey,
                'language' => 'ar',
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] == 'OK' && !empty($data['results'][0])) {
                    return [
                        'formatted_address' => $data['results'][0]['formatted_address'],
                        'components' => $this->extractAddressComponents($data['results'][0]['address_components']),
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Reverse Geocode Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * استخراج مكونات العنوان
     */
    private function extractAddressComponents($components)
    {
        $extracted = [];

        foreach ($components as $component) {
            foreach ($component['types'] as $type) {
                switch ($type) {
                    case 'street_number':
                        $extracted['street_number'] = $component['long_name'];
                        break;
                    case 'route':
                        $extracted['street'] = $component['long_name'];
                        break;
                    case 'locality':
                        $extracted['city'] = $component['long_name'];
                        break;
                    case 'administrative_area_level_1':
                        $extracted['region'] = $component['long_name'];
                        break;
                    case 'country':
                        $extracted['country'] = $component['long_name'];
                        break;
                }
            }
        }

        return $extracted;
    }

    /**
     * حساب المسافة الدائرية (بدون مراعاة الطرق)
     */
    public function calculateHaversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // نصف قطر الأرض بالمتر

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $latDelta = $lat2 - $lat1;
        $lngDelta = $lng2 - $lng1;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($lat1) * cos($lat2) * pow(sin($lngDelta / 2), 2)));

        $distance = $angle * $earthRadius;

        // حساب الوقت التقريبي (بافتراض سرعة 40 كم/ساعة)
        $speedKmH = 40;
        $speedMs = $speedKmH * 1000 / 3600;
        $estimatedTime = $distance / $speedMs;

        return [
            'distance_meters' => round($distance),
            'distance_km' => round($distance / 1000, 2),
            'estimated_time_seconds' => round($estimatedTime),
            'estimated_time_minutes' => round($estimatedTime / 60),
        ];
    }
}
