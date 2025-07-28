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
       $stations= "";

        return view('admin.dashboard', compact('stations'));
    }
}
