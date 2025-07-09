<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index()
    {
        $stations = Station::where('Status', 1)->get();
        return view('station.index', compact('stations'));
    }
    public function getData()
    {
        $stations = Station::where('Status', 1)->get();
        return response()->json(['data' => $stations]);
    }
}