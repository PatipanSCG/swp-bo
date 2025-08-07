<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SysTechnicianTeam;
use App\Models\SysTechnicianTeamMember;
use App\Models\User;

class SysTechnicianTeamController extends Controller
{
    // 📌 แสดงรายชื่อทีมช่างทั้งหมด
    public function index()
    {
        $teams = SysTechnicianTeam::with('members.user')->get();

        return response()->json($teams);
    }
    // 📌 สร้างทีมใหม่
    public function store(Request $request)
    {
        $team = SysTechnicianTeam::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'team' => $team]);
    }

    // 📌 เพิ่มสมาชิกเข้าทีม
    public function addMember(Request $request)
{
    // ตรวจสอบว่ามีสมาชิกคนนี้ในทีมอยู่แล้วหรือยัง
    $exists = SysTechnicianTeamMember::where('user_id', $request->user_id)
        ->where('team_id', $request->team_id)
        ->exists();

    if ($exists) {
        return response()->json([
            'success' => false,
            'message' => 'สมาชิกนี้อยู่ในทีมแล้ว'
        ], 409); // 409 Conflict
    }

    // เพิ่มสมาชิกใหม่
    SysTechnicianTeamMember::create([
        'user_id' => $request->user_id,
        'team_id' => $request->team_id,
    ]);

    // ส่งสมาชิกทั้งหมดของทีมกลับไป
    $members = SysTechnicianTeamMember::with('user')
        ->where('team_id', $request->team_id)
        ->get();

    return response()->json([
        'success' => true,
        'members' => $members
    ]);
}


    // 📌 ลบสมาชิกออกจากทีม
    public function removeMember($memberId)
    {
        $member = SysTechnicianTeamMember::findOrFail($memberId);
        $teamId = $member->team_id; // เก็บไว้ก่อนลบ
        $member->delete();

        // โหลดสมาชิกที่เหลือ
        $members = SysTechnicianTeamMember::with('user')
            ->where('team_id', $teamId)
            ->get();

        return response()->json([
            'success' => true,
            'members' => $members
        ]);
    }

    // 📌 ดูสมาชิกของทีม
    public function teamMembers($teamId)
    {
        $team = SysTechnicianTeam::with('members.user')->findOrFail($teamId);
        return response()->json($team);
    }
    public function teamData($teamId)
    {
        $team = SysTechnicianTeam::where('id', $teamId)->first();
        return response()->json($team);
    }

    // 📌 ลบทีม
    public function destroy($id)
    {
        $team = SysTechnicianTeam::findOrFail($id);
        $team->members()->delete(); // ลบสมาชิกก่อน
        $team->delete();

        return response()->json(['success' => true]);
    }
}
