<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SysServiceRate;

class ServiceRateController extends Controller
{
    public function calculateCharge($nozzleCount)
    {
        $rates = SysServiceRate::orderBy('start_nozzle')->get();

        $total = 0;
        $breakdown = [];
        $currentNozzle = 1;

        foreach ($rates as $rate) {
            // Flat rate เหมาจ่าย ไม่คำนวณต่อ
            if ($rate->rate_type === 'flat') {
                $total = $rate->flat_rate;
                $breakdown[] = [
                    'range' => 'เหมาจ่าย',
                    'quantity' => $nozzleCount,
                    'rate' => $rate->flat_rate,
                    'amount' => $rate->flat_rate,
                    'note' => "หัวจ่ายทั้งหมดคิดราคาเหมาจ่าย"
                ];
                break;
            }

            $start = $rate->start_nozzle;
            $end = $rate->end_nozzle ?? $nozzleCount;

            if ($nozzleCount < $start) {
                continue; // ข้ามช่วงที่ยังไม่ถึง
            }

            $rangeStart = max($start, $currentNozzle);
            $rangeEnd = min($end, $nozzleCount);

            if ($rangeEnd < $rangeStart) {
                continue; // ไม่มีหัวจ่ายในช่วงนี้
            }

            $qty = $rangeEnd - $rangeStart + 1;
            $amount = $qty * $rate->rate_per_nozzle;

            $total += $amount;

            $breakdown[] = [
                'range' => "หัวจ่ายที่ {$rangeStart} - {$rangeEnd}",
                'quantity' => $qty,
                'rate' => number_format($rate->rate_per_nozzle, 2),
                'amount' => number_format($amount, 2),
                'note' => "{$qty} หัวจ่าย x {$rate->rate_per_nozzle} บาท"
            ];

            $currentNozzle = $rangeEnd + 1;

            if ($currentNozzle > $nozzleCount) {
                break;
            }
        }

        return response()->json([
            'nozzle_count' => $nozzleCount,
            'total_charge' => number_format($total, 2),
            'breakdown' => $breakdown,
        ]);
    }
}
