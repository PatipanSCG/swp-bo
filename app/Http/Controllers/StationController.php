<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Station;
use App\Models\Province;
use App\Models\Brand;
use App\Models\District;
use App\Models\Subdistrict;
use App\Models\Nozzle;
use App\Models\ComunicataeType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\LocationController;
use Illuminate\Validation\Rule;
use App\Models\NavCustomer;
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
    public function getData(Request $request)
    {
        $query = Station::with([
            'brand:BrandID,BrandName',
            'province:code,NameInThai,Id',
            'district:code,NameInThai,ProvinceId,Id',
            'subdistrict:code,NameInThai,ZipCode,DistrictId,Id',
            'dispensers:DispenserID,StationID,LastCalibationDate'
        ])
            ->withCount(['nozzle', 'dispensers'])
            ->where('Status', 1);

        // ✅ Filter จากฟอร์ม
        if ($request->filled('name')) {
            $query->where('StationName', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('taxid')) {
            $query->where('TaxID', 'like', '%' . $request->taxid . '%');
        }
        if ($request->filled('province')) {
            $query->whereHas('province', function ($q) use ($request) {
                $q->where('NameInThai', 'like', '%' . $request->province . '%');
            });
        }
        if ($request->filled('district')) {
            $query->whereHas('district', function ($q) use ($request) {
                $q->where('NameInThai', 'like', '%' . $request->district . '%');
            });
        }
        if ($request->filled('subdistrict')) {
            $query->whereHas('subdistrict', function ($q) use ($request) {
                $q->where('NameInThai', 'like', '%' . $request->subdistrict . '%');
            });
        }

        return DataTables::of($query)
            ->addColumn('latest_calibration_date', function ($station) {
                return optional(
                    $station->dispensers->sortByDesc('LastCalibationDate')->first()
                )->LastCalibationDate;
            })
            ->make(true);
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
        $communicationTypeList = ComunicataeType::where('Status', 1)
            ->orderBy('ComunicataeName')
            ->get();
        $brands = Brand::all();

        // ดึงข้อมูลลูกค้า
        $customer = Customer::with([ 'province:code,NameInThai,Id',
            'district:code,NameInThai,ProvinceId,Id',
            'subdistrict:code,NameInThai,ZipCode,DistrictId,Id'])->where('TaxID', $station->TaxID)->first();
        $provinces = Province::all();
        $District = District::all();
        $Subdistrict = Subdistrict::all();
        return view('station.detail', compact('station', 'customer', 'provinces', 'District', 'Subdistrict', 'brands', 'communicationTypeList'));
    }
    public function model()
    {
        return view('station.model');
    }
    public function store(Request $request)
    {
        $station = new Station();
        $station->StationName = $request->StationName;
        $station->TaxID = $request->TaxID;
        $station->BrandID = $request->BrandID;
        $station->Address = $request->Address;
        $station->Province = $request->Province;
        $station->Distric = District::find($request->District)?->Code ?? '-';
        $station->Subdistric = Subdistrict::find($request->Subdistrict)?->Code ?? '-';
        $station->Postcode = $request->Postcode;
        $station->Status = 1;
        $station->save();

        return response()->json(['message' => 'success']);
    }
    public function update(Request $request, $id)
{
    $station = Station::findOrFail($id);

    // 1. บันทึกข้อมูลสถานี
    $station->StationName = $request->input('StationName');
    $station->TaxID = $request->input('TaxID');
    $station->BrandID = $request->input('BrandID');
    $station->Address = $request->input('Address');
    $station->Province = $request->input('Province');
    $station->Distric = $request->input('District');
    $station->Subdistric = $request->input('Subdistrict');
    $station->Postcode = $request->input('Postcode');
    $station->Status = 1;

    $station->save();

    // 2. ตรวจสอบว่ามี Customer จาก TaxID นี้หรือยัง
    $hasCustomer = Customer::where('TaxID', $station->TaxID)->exists();

    if (!$hasCustomer && $station->TaxID) {
        // 3. ดึงข้อมูลจาก Nav
        $navCustomer = NavCustomer::whereRaw("[VAT Registration No_] = ?", [$station->TaxID])->first();

        if ($navCustomer) {
            // 4. ถ้ามีข้อมูลใน NAV ➜ ใช้ข้อมูลจาก NAV
            Customer::create([
                'CustomerType' => 3, // หรือใช้ Mapping ถ้ามี
                'CustomerName' => $navCustomer->{'Name'}, // หรือ 'Name' แล้วแต่คอลัมน์
                'TaxID' => $station->TaxID,
                'Address' => $navCustomer->{'Address'}, // ปรับชื่อตามจริง
                'Province' => null, // ถ้าไม่มีให้เป็น null หรือ map ได้
                'Distric' => null,
                'Subdistric' => null,
                'Postcode' => $navCustomer->{'Postcode'} ?? null,
                'Telphone' => $navCustomer->{'Phone No_'} ?? null,
                'Email' => null,
                'Phone' => null,
                'Status' => 1,
     
            ]);
        } else {
            // 5. ถ้าไม่มีข้อมูลใน NAV ➜ ใช้ข้อมูลจากสถานี
            Customer::create([
                'CustomerType' => 3,
                'CustomerName' => $station->StationName,
                'TaxID' => $station->TaxID,
                'Address' => $station->Address,
                'Province' => $station->Province,
                'Distric' => $station->Distric,
                'Subdistric' => $station->Subdistric,
                'Postcode' => $station->Postcode,
                'Telphone' => null,
                'Email' => null,
                'Phone' => null,
                'Status' => 1,
        
            ]);
        }
    }

    return redirect()->back()->with('success', 'อัปเดตข้อมูลสถานีเรียบร้อยแล้ว');
}
    public function calculateDistance($last, $long)
    {
        $result = LocationController::getDrivingDistance(13.8297578, 100.5573418, (float) $last, (float) $long);

        return $result;
    }
    public function updatelatlong(Request $request)
    {
        $request->validate([
            'map_url' => ['required', 'url'],
            'station_id' => [
                'required',
                'integer',
                Rule::exists('sqlsrv_secondary.stations', 'StationID'), // 👈 ชี้ไปยัง SQL Server
            ],
        ]);

        // ดึง lat/lng จาก Google Maps URL
        preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $request->map_url, $matches);

        if (count($matches) !== 3) {
            return response()->json(['error' => 'ไม่พบพิกัดใน URL'], 422);
        }

        $lat = $matches[1];
        $lng = $matches[2];

        $station = Station::find($request->station_id);
        $station->last = $lat;
        $station->long = $lng;
        $station->save();

        return response()->json([
            'message' => 'อัปเดตพิกัดสำเร็จ',
            'data' => [
                'station_id' => $station->StationID,
                'last' => $lat,
                'long' => $lng,
            ]
        ]);
    }
    public function countNozzles($stationid)
    {
        $count = Station::withCount('nozzles')->find($stationid)?->nozzles_count ?? 0;

        return $count;
    }
}
