<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Dispenser;

class DispenserController extends Controller
{
    public function index($stationId)
    {
        $station = Station::find($stationId);
       

        return view('dispensers.index', compact('station'));
    }
     public function getData($stationId)
    {
        
        $dispensers = Dispenser::where('StationID', $stationId)->get();
        return response()->json(['data' => $dispensers]);
    }
}
