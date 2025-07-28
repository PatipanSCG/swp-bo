<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function stationOverview()
    {
        // $stations = DB::table('stations')
        //     ->leftJoin('sys_provinces', 'stations.Province', '=', 'sys_provinces.Id')
        //     ->leftJoin('sys_districts', 'stations.District', '=', 'sys_districts.Id')
        //     ->leftJoin('sys_subdistricts', 'stations.SubDistrict', '=', 'sys_subdistricts.Id')
        //     ->select(
        //         'stations.StationName',
        //         'stations.license_expired_date',
        //         'stations.DispenserCount',
        //         'sys_provinces.NameInThai as Province',
        //         'sys_districts.NameInThai as District',
        //         'sys_subdistricts.NameInThai as SubDistrict'
        //     )
        //     ->get();
        


        return view('admin.dashboard');
    }
    public function getdata_stations()
    {
        $stations = [
            [
                "StationName" => "ปตท สาขารังสิตคลอง 2",
                "DispenserCount" => 8,
                "license_expired_date" => "2025-08-14",
                "SubDistrict" => "ประชาธิปัตย์",
                "District" => "ธัญบุรี",
                "Province" => "ปทุมธานี"
            ],
            [
                "StationName" => "บางจาก ลาดพร้าว 101",
                "DispenserCount" => 6,
                "license_expired_date" => "2024-12-02",
                "SubDistrict" => "คลองจั่น",
                "District" => "บางกะปิ",
                "Province" => "กรุงเทพมหานคร"
            ],
            [
                "StationName" => "ซัสโก้ แจ้งวัฒนะ",
                "DispenserCount" => 4,
                "license_expired_date" => "2025-09-25",
                "SubDistrict" => "ตลาดขวัญ",
                "District" => "เมืองนนทบุรี",
                "Province" => "นนทบุรี"
            ],
            [
                "StationName" => "เชลล์ สาขาศรีนครินทร์",
                "DispenserCount" => 10,
                "license_expired_date" => "2025-01-12",
                "SubDistrict" => "บางเมือง",
                "District" => "เมืองสมุทรปราการ",
                "Province" => "สมุทรปราการ"
            ],
            [
                "StationName" => "เอสโซ่ พระราม 2",
                "DispenserCount" => 6,
                "license_expired_date" => "2025-10-07",
                "SubDistrict" => "แสมดำ",
                "District" => "บางขุนเทียน",
                "Province" => "กรุงเทพมหานคร"
            ],
            [
                "StationName" => "ปตท บางปะอิน",
                "DispenserCount" => 7,
                "license_expired_date" => "2026-01-28",
                "SubDistrict" => "บ้านกรด",
                "District" => "บางปะอิน",
                "Province" => "พระนครศรีอยุธยา"
            ],
            [
                "StationName" => "บางจาก วังน้อย",
                "DispenserCount" => 9,
                "license_expired_date" => "2025-11-17",
                "SubDistrict" => "ลำตาเสา",
                "District" => "วังน้อย",
                "Province" => "พระนครศรีอยุธยา"
            ],
            [
                "StationName" => "เชลล์ ดอนเมือง",
                "DispenserCount" => 5,
                "license_expired_date" => "2024-11-30",
                "SubDistrict" => "สนามบิน",
                "District" => "ดอนเมือง",
                "Province" => "กรุงเทพมหานคร"
            ],
            [
                "StationName" => "ซัสโก้ นครชัยศรี",
                "DispenserCount" => 6,
                "license_expired_date" => "2025-06-18",
                "SubDistrict" => "ท่าตำหนัก",
                "District" => "นครชัยศรี",
                "Province" => "นครปฐม"
            ],
            [
                "StationName" => "เอสโซ่ บางแสน",
                "DispenserCount" => 8,
                "license_expired_date" => "2025-03-21",
                "SubDistrict" => "แสนสุข",
                "District" => "เมืองชลบุรี",
                "Province" => "ชลบุรี"
            ]
        ];
 return response()->json(['data' => $stations]);
    }
}
