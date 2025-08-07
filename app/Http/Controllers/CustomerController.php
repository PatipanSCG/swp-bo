<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function addfromnav(Request $request, $stationid)
    {
        // ตรวจสอบว่ามีอยู่แล้วหรือยัง
        $exists = UserSystem::where('UserName', $empCode)->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'พนักงานคนนี้ถูกเพิ่มเข้าระบบแล้ว');
        }

        // เพิ่มข้อมูลใหม่
        $user = new UserSystem();
        $user->UserName = $request->input('Username');
        $user->Position = $request->input('Position');
        $user->NameTH   = $request->input('NameTH');
        $user->NameEN   = $request->input('NameEN');
        $user->RoleID = 1;
        $user->CreatedAt = now();
        $user->UpdatedAt = now();

        $user->save();

        return redirect()->back()->with('success', 'เพิ่มพนักงานเข้าสู่ระบบสำเร็จ');
    }
}
