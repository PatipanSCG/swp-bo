<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Station;
use App\Models\Province;
use App\Models\Brand;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index()
    {
        // $stations = Station::where('Status', 1)->get();
        $brands = Brand::all();
        $provinces = Province::all();
        // dd($provinces);exit;
        return view('station.index', compact('brands', 'provinces'));
        // return view('station.index', compact('stations'));
    }
    public function getData()
    {
        $stations = Station::with([
            'brand:BrandID,BrandName',
            'province:code,NameInThai,Id',
            'district:code,NameInThai,ProvinceId,Id',
            'subdistrict:code,NameInThai,ZipCode,DistrictId,Id',
            'dispensers:DispenserID,StationID,LastCalibationDate'
        ])
            ->withCount('nozzle')    // รวมหัวจ่ายทั้งหมดของสถานี
            ->withCount('dispensers') // จำนวนตู้จ่ายทั้งหมดของสถานี
            ->where('Status', 1)
            ->get()
            ->map(function ($station) {
                $station->latest_calibration_date = optional(
                    $station->dispensers->sortByDesc('LastCalibationDate')->first()
                )->LastCalibationDate;
                return $station;
            });
        // Response เป็น JSON:
        return response()->json(['data' => $stations]);
    }
    public function detail($stationId)
    {
        // ดึงข้อมูลสถานีตาม ID
        $station = Station::with([
            'brand:BrandID,BrandName',
            'province:code,NameInThai,Id',
            'district:code,NameInThai,ProvinceId,Id',
            'subdistrict:code,NameInThai,ZipCode,DistrictId,Id',
            'dispensers:DispenserID,StationID,LastCalibationDate'
        ])
            ->withCount([
                'nozzle',
                'dispensers'
            ])
            ->where('StationID', $stationId)
            ->where('Status', 1)
            ->firstOrFail(); // ✅ ได้ model เดียว

        // หาวันที่ calibration ล่าสุดจาก dispenser
        $station->latest_calibration_date = optional(
            $station->dispensers->sortByDesc('LastCalibationDate')->first()
        )->LastCalibationDate;

        // ดึงข้อมูลลูกค้า
        $customer = Customer::where('TaxID', $station->TaxID)->first();
        return view('station.detail', compact('station', 'customer'));
    }
    public function store(Request $request)
    {
        $station = new Station();
        $station->StationName = $request->StationName;
        $station->TaxID = $request->TaxID;
        $station->BrandID = $request->BrandID;
        $station->Address = $request->Address;
        $station->Province = $request->Province;
        $station->Distric = $request->Distric;
        $station->Subdistric = $request->Subdistric;
        $station->Postcode = $request->Postcode;
        $station->Status = 1;
        $station->save();

        return response()->json(['message' => 'success']);
    }
}
