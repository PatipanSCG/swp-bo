<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nozzle;

class NozzleController extends Controller
{
    // POST /api/nozzles
      public function store(Request $request)
    {
        $validated = $request->validate([
            // 'NozzleNumber' => ['required', 'regex:/^\d-\d{6}-\d-\d{4}-\d{6}-\d{2}$/'],
            'NozzleNumber' => 'required|string',
            'DispenserID' => 'required|integer',
            'FuleTypeID' => 'required|integer',
            'FlowRate' => 'nullable|numeric',
            'LastCalibrationDate' => 'nullable|date',
            'MMQ' => 'required|integer',
            'Qmax' => 'required|integer',
            'Qmin' => 'required|integer',
            'SN' => 'required|string',
        ]);

        $nozzle = Nozzle::create($validated);
        $Nozzles = Nozzle::with(['FuleType:FuleTypeID,FuleTypeName'])
        ->where('NozzleID', $nozzle->NozzleID)
            ->get();
        return response()->json([
            'message' => 'เพิ่มหัวจ่ายสำเร็จ',
            'data' => $Nozzles
        ], 201);
    }

    // GET /api/nozzles/{id}
    public function show($id)
    {
        $nozzle = Nozzle::findOrFail($id);
        return response()->json($nozzle);
    }

    // PUT /api/nozzles/{id}
    public function update(Request $request, $id)
    {
        $nozzle = Nozzle::findOrFail($id);

        $validated = $request->validate([
            'NozzleNumber' => ['required', 'regex:/^\d-\d{6}-\d-\d{4}-\d{6}-\d{2}$/'],
            'DispenserID' => 'required|integer',
            'FuelTypeID' => 'required|integer',
            'FlowRate' => 'nullable|numeric',
            'LastCalibrationDate' => 'nullable|date',
            'MMQ' => 'required|integer',
            'Qmax' => 'required|integer',
            'Qmin' => 'required|integer',
            'SN' => 'required|string',
        ]);

        $nozzle->update($validated);

        return response()->json([
            'message' => 'อัปเดตหัวจ่ายสำเร็จ',
            'data' => $nozzle
        ]);
    }

    // DELETE /api/nozzles/{id}
    public function destroy($id)
    {
        $nozzle = Nozzle::findOrFail($id);
        $nozzle->delete();

        return response()->json(['message' => 'ลบหัวจ่ายสำเร็จ']);
    }
}
