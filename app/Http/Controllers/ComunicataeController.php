<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comunicatae;
use App\Models\UserSystem;

class ComunicataeController extends Controller
{
    // 🔹 แสดงรายการทั้งหมด
    public function index($id)
    {
        $comunicataes = Comunicatae::with(['station', 'customer', 'user', 'type','contact'])->where('StationID',$id)->get();
        return response()->json($comunicataes);
    }

    // 🔹 แสดงรายการเดียว
    public function show($id)
    {
        $comunicatae = Comunicatae::with(['station', 'customer', 'user', 'type','contact'])->findOrFail($id);
        return response()->json($comunicatae);
    }

    // 🔹 สร้างข้อมูลใหม่
    public function store(Request $request)
{
    $validated = $request->validate([
        'StationID' => 'required|integer',
        'CustomerID' => 'nullable|integer',
        'UserID' => 'nullable|integer',
        'ComunicataeTypeID' => 'required|integer',
        'ComunicataeDetail' => 'required|string',
        'Status' => 'nullable|integer',
        'created_by' => 'nullable|integer'
    ]);

    $userSqlSrv = session('user_system');

    if (!$userSqlSrv || !isset($userSqlSrv['UserID'])) {
        return response()->json([
            'success' => false,
            'message' => 'Session หมดอายุหรือไม่พบข้อมูลผู้ใช้'
        ], 401);
    }

    $userid = $userSqlSrv['UserID'];
    $validated['UserID'] = $userid;
    $validated['created_by'] = $userid;

    $comunicatae = Comunicatae::create($validated);

    return response()->json(['success' => true, 'data' => $comunicatae]);
}

    // 🔹 แก้ไขข้อมูล
    public function update(Request $request, $id)
    {
        $comunicatae = Comunicatae::findOrFail($id);

        $validated = $request->validate([
            'StationID' => 'required|integer',
            'CustomerID' => 'nullable|integer',
            'UserID' => 'required|integer',
            'ComunicataeTypeID' => 'required|integer',
            'ComunicataeDetail' => 'required|string',
            'Status' => 'nullable|integer',
            'updated_by' => 'nullable|integer'
        ]);

        $comunicatae->update($validated);

        return response()->json(['success' => true, 'data' => $comunicatae]);
    }

    // 🔹 ลบข้อมูล
    public function destroy($id)
    {
        $comunicatae = Comunicatae::findOrFail($id);
        $comunicatae->delete();

        return response()->json(['success' => true]);
    }
    
}
