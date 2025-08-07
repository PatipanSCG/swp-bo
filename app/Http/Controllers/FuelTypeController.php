<?php

namespace App\Http\Controllers;

use App\Models\FuelType;
use Illuminate\Http\Request;

class FuelTypeController extends Controller
{
    public function list()
    {
        $fuelTypes = FuelType::where('Status', 1)->select('FuleTypeID', 'FuleTypeName')->get();

        return response()->json($fuelTypes);
    }
}
