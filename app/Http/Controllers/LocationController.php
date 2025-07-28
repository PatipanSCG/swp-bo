<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Subdistrict;

class LocationController extends Controller
{
    public function getDistricts($provinceId)
    {
        return District::where('provinceId', $provinceId)->get(['Id', 'NameInThai']);
    }

    public function getSubdistricts($districtId)
    {
        return Subdistrict::where('DistrictId', $districtId)->get(['Id', 'NameInThai','ZipCode']);
    }
}
