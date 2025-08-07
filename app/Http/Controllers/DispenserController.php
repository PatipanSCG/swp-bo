<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Dispenser;
use App\Models\Nozzle;

class DispenserController extends Controller
{
    public function index($stationId)
    {
        $station = Station::find($stationId);


        return view('dispensers.index', compact('station'));
    }
    public function getData($stationId)
    {

        $dispensers = Dispenser::with([
            'brand:BrandID,BrandName'
        ])
            ->withCount('nozzles')  // หรือ ->withCount('nozzles') ถ้าคุณตั้งชื่อความสัมพันธ์แบบพหูพจน์
            ->where('StationID', $stationId)
            ->get();

        return response()->json(['data' => $dispensers]);
    
    }
    public function getDataNozzles($DispenserID)
    {

        $Nozzles = Nozzle::with(['FuleType:FuleTypeID,FuleTypeName'])
        ->where('DispenserID', $DispenserID)
            ->get();

        return response()->json(['data' => $Nozzles]);
    
    }
}
