<?php

// app/Http/Controllers/TravelController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SysTravelRate;

class TravelController extends Controller
{
    public function calculate($distance)
    {
         $rates = SysTravelRate::orderBy('start_km')->get();
    $remaining_km = $distance;
    $total_price = 0;
    $details = [];

    foreach ($rates as $rate) {
        $start = $rate->start_km;
        $end = $rate->end_km ?? $distance;

        // ถ้า start > ระยะทางผู้ใช้ → ไม่เกี่ยวแล้ว
        if ($start > $distance) break;

        // คำนวณระยะทางในช่วงนี้
        $range_end = min($end, $distance);
        $km_in_range = $range_end - $start + 1;

        if ($km_in_range <= 0) continue;

        if (!is_null($rate->flat_rate)) {
            $total_price += $rate->flat_rate;
            $details[] = [
                'range' => "$start-$range_end กม.",
                'type' => 'flat',
                'price' => $rate->flat_rate
            ];
        } elseif (!is_null($rate->rate_per_km)) {
            $step_price = $km_in_range * $rate->rate_per_km;
            $total_price += $step_price;
            $details[] = [
                'range' => "$start-$range_end กม.",
                'type' => 'per_km',
                'price' => $step_price,
                'price_km' =>$rate->rate_per_km
            ];
        }
    }

    return response()->json([
        'distance' => $distance,
        'details' => $details,
        'total_price' => number_format($total_price, 2)
    ]);
    }
}
