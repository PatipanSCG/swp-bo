<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class EmployeeController extends Controller
{
    public function index()
    {
        // ดึงเฉพาะ RowStatus = 1
        $employees = User::where('RowStatus', 1)->get();

        return view('employee.index', compact('employees'));
    }
   
    public function getData()
    {
        $employees = User::where('RowStatus', 1)->get();
        return response()->json(['data' => $employees]);
    }
}

