<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSystem;

class EmployeeController extends Controller
{
    public function index()
    {
        // ดึงเฉพาะ RowStatus = 1
        $employees = User::where('RowStatus', 1)->get();

        return view('employee.index', compact('employees'));
    }
    public function getdataforen(){
        $employees = User::where('RowStatus', 1)
        ->where('DeptKey','43f90294-6000-11e8-a1fc-8cec4b307d55')
        ->select('EmpCode', 'NameTH', 'NameEN','Email','Position')
        ->get();
         return response()->json(['data' => $employees]);
        
    }
    public function getData()
    {
        $employees = User::where('RowStatus', 1)
        
        ->get();

        // ดึง UserName จากระบบ SWP (UserSystem)
        $system_usernames = UserSystem::pluck('UserName')->toArray();
        // Map เพิ่มสถานะเข้าไปในแต่ละพนักงาน
        $employees = $employees->map(function ($employee) use ($system_usernames) {
            $employee->SWPis = in_array($employee->UserName, $system_usernames) ? 1 : 0;
            return $employee;
        });
        return response()->json(['data' => $employees]);
    }
    public function getDatainsystem()
    {
        $employees = UserSystem::get();

        // ดึง UserName จากระบบ SWP (UserSystem)
        $system_usernames = UserSystem::pluck('UserName')->toArray();
        // Map เพิ่มสถานะเข้าไปในแต่ละพนักงาน
        
        return response()->json(['data' => $employees]);
    }
    public function add(Request $request, $empCode)
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
    
    public function teams(){
      
        return view('employee.team');
    }
}
