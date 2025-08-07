<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Subdistrict;
use App\Models\Province;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function getDistricts($provinceId)
    {   
        $province = Province::where('Code', $provinceId)->select('Id')->first();

        if (!$province) {
            return response()->json([], 404); // หรือส่ง [] กลับ
        }

        return District::where('provinceId', $province->Id)->get(['Code', 'NameInThai']);
    }

    public function getSubdistricts($districtId)
    {
        $district = District::where('Code', $districtId)->select('Id')->first();

        if (!$district) {
            return response()->json([], 404); // หรือส่ง [] กลับ
        }
        return Subdistrict::where('DistrictId', $district->Id)->get(['Code', 'NameInThai', 'ZipCode']);
    }
    public function getLocationDetail($lat,$lng){
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/geocode/json";

        $response = Http::get($url, [
            'latlng' => "$lat,$lng",
            'key' => $apiKey
        ]);
            $data = $response->json();
        dd($data);
         if ($data['status'] === 'OK') {
        $formattedAddress = $data['results'][0]['formatted_address'] ?? 'ไม่พบที่อยู่';
        return response()->json(['address' => $formattedAddress]);
    }

    return response()->json(['error' => 'ไม่สามารถดึงข้อมูลตำแหน่งได้'], 400);
    }
    public static  function getDrivingDistance($lat1, $lng1, $lat2, $lng2)
    {
        $lat2 = (float) $lat2;
        $lng2 = (float) $lng2;

        $apiKey = env('GOOGLE_MAPS_API_KEY');

        $url = "https://routes.googleapis.com/directions/v2:computeRoutes";

        $payload = [
            'origin' => [
                'location' => [
                    'latLng' => ['latitude' => $lat1, 'longitude' => $lng1]
                ]
            ],
            'destination' => [
                'location' => [
                    'latLng' => ['latitude' => $lat2, 'longitude' => $lng2]
                ]
            ],
            'travelMode' => 'DRIVE'
        ];

        $response = Http::withHeaders([
            'X-Goog-Api-Key' => $apiKey,
            'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters'
        ])->post($url, $payload);

        if ($response->successful()) {
            $route = $response->json()['routes'][0];

            $distanceMeters = isset($route['distanceMeters']) ? floatval($route['distanceMeters']) : 0;

            // ✅ แปลง ISO 8601 duration เช่น "3660s" เป็นวินาที
            $durationStr = $route['duration'] ?? '0s';
            $durationSeconds = (int) str_replace('s', '', $durationStr);

            $distanceKm = round($distanceMeters / 1000, 2);
            $hours = floor($durationSeconds / 3600);
            $minutes = floor(($durationSeconds % 3600) / 60);

            $distanceText = $distanceKm . ' กม.';
            $durationText = ($hours > 0 ? $hours . ' ชม. ' : '') . $minutes . ' นาที';

            return [
                'distance_km' => ceil($distanceKm),
                'duration_hr_min' => $durationText,
                'distance_text' => $distanceText,
                'raw' => [
                    'distance_meters' => $distanceMeters,
                    'duration_seconds' => $durationSeconds
                ]
            ];
        }
    }
}
