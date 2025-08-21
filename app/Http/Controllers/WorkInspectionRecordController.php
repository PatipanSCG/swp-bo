<?php

namespace App\Http\Controllers;
use App\Helpers\InspectionCalculator;
use App\Models\WorkInspectionRecord;
use App\Exports\NozzleExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class WorkInspectionRecordController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'WorkID' => 'required|integer',
            'StationID' => 'required|integer',
            'DispenserID' => 'required|integer',
            'NozzleID' => 'required|integer',
            'NozzleNumber' => 'required|string',
            'MitterBegin' => 'nullable|numeric',
            'MitterEnd' => 'nullable|numeric',
            'MMQ_1L' => 'nullable|numeric',
            'MMQ_5L' => 'nullable|numeric',
            'MPE_5L' => 'nullable|numeric',
            'MPE_20L' => 'nullable|numeric',
            'Repeat5L_1' => 'nullable|numeric',
            'Repeat5L_2' => 'nullable|numeric',
            'Repeat5L_3' => 'nullable|numeric',
            'KFactor' => 'nullable|numeric',
            'ExpirationDate' => 'nullable|date',
            'KarudaNumber' => 'nullable|string',
            'SealNumber' => 'nullable|string',
             'KFactor' => 'nullable|numeric'
        ]);

        // MMQ base value for R14
        $r14 = 5;

        // คำนวณค่า VR และ SNS
        $vr1 = $this->vr1($validated['MMQ_1L'] ?? null, $validated['MMQ_5L'] ?? null);
        $vr2 = $this->vr2($validated['MPE_5L'] ?? null, $validated['Repeat5L_1'] ?? null);
        $vr3 = $this->vr3($r14, $validated['Repeat5L_1'] ?? null, $validated['Repeat5L_2'] ?? null, $validated['Repeat5L_3'] ?? null);
        $vr4 = $this->vr4($r14, $validated['Repeat5L_1'] ?? null, $validated['Repeat5L_2'] ?? null, $validated['Repeat5L_3'] ?? null);
        $vr5 = $this->vr5($r14, $validated['Repeat5L_1'] ?? null, $validated['Repeat5L_2'] ?? null, $validated['Repeat5L_3'] ?? null);

        $sns = $this->sns([$vr1, $vr2, $vr3, $vr4, $vr5]);

        $validated = array_merge($validated, [
            'VR_1' => $vr1,
            'VR_2' => $vr2,
            'VR_3' => $vr3,
            'VR_4' => $vr4,
            'VR_5' => $vr5,
            'SNS_True' => $sns['SNS_True'],
            'SNS_False' => $sns['SNS_False'],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $record = WorkInspectionRecord::updateOrCreate(
            [
                'WorkID' => $validated['WorkID'],
                'StationID' => $validated['StationID'],
                'DispenserID' => $validated['DispenserID'],
                'NozzleID' => $validated['NozzleID'],
            ],
            $validated
        );
                return response()->json([
            'success' => true,
            'message' => 'บันทึกข้อมูลตรวจสอบสำเร็จ',
            'data' => $record
        ]);
    }
    public function exportLabelByStation($stationid)
    {
       return Excel::download(new NozzleExport($stationid), 'nozzles_station_' . $stationid .now(). '.xlsx');
    }
    private  static function vr1($mmq_1l, $mmq_5l): ?bool
    {
        if (is_null($mmq_1l) && is_null($mmq_5l)) return null;
        if (abs($mmq_1l) > 12 || abs($mmq_5l) > 12) return false;
        return true;
    }

    private  static function vr2($mpe_5l, $repeat1): ?bool
    {
        if (!is_null($mpe_5l) && !is_null($repeat1)) {
            return (abs($mpe_5l) > 15 || abs($repeat1) > 60) ? false : true;
        } elseif (!is_null($mpe_5l)) {
            return abs($mpe_5l) > 15 ? false : true;
        } elseif (!is_null($repeat1)) {
            return abs($repeat1) > 60 ? false : true;
        }
        return null;
    }

    private  static function vr3($r14, $r, $s, $t): ?bool
    {
        if (is_null($r14)) return null;

        $threshold = 0.0015 * $r14 * 1000;
        $values = array_filter([$r, $s, $t], fn($v) => is_numeric($v));

        if (count($values) === 0) return null;

        foreach ($values as $v) {
            if ($v <= $threshold) return true;
        }
        return false;
    }

    private  static function vr4($r14, $r, $s, $t): ?bool
    {
        if (is_null($r14)) return null;

        $threshold = 0.03 * $r14 * 1000;
        $values = array_filter([$r, $s, $t], fn($v) => is_numeric($v));

        if (count($values) === 0) return null;

        $max = max($values);
        $min = min($values);
        return ($max - $min) <= $threshold;
    }

    private  static function vr5($r14, $r, $s, $t): ?bool
    {
        if (is_null($r14)) return null;

        $values = array_filter([$r, $s, $t], fn($v) => is_numeric($v));
        if (count($values) < 1) return null;

        $diff = max($values) - min($values);

        return match ($r14) {
            5 => $diff <= 6,
            10 => $diff <= 12,
            20 => $diff <= 24,
            default => false
        };
    }

    private  static function sns(array $vrs): array
    {
        if (in_array(null, $vrs, true)) {
            return ['SNS_True' => null, 'SNS_False' => null];
        }

        $anyFail = in_array(false, $vrs, true);
        $sns_true = $anyFail ? 0 : 1;
        $sns_false = $sns_true == 1 ? 0 : 1;

        return ['SNS_True' => $sns_true, 'SNS_False' => $sns_false];
    }
}