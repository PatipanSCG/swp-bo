<?php

namespace App\Http\Controllers;
use App\Helpers\MapHelper;
use App\Models\Work;
use App\Models\WorkEmployee;
use App\Models\WorkInspectionRecord;
use App\Models\WamVerifyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WorkController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized1'], 403);
        }

        // ผู้ใช้จาก sqlsrv_secondary ที่ login ผ่าน session
        $userSqlSrv = session('user_system');

        if (!$userSqlSrv) {
            return response()->json(['error' => 'Unauthorized from secondary DB'], 403);
        }

        $userId = $userSqlSrv['UserID'] ?? null;
        $roleId = $userSqlSrv['RoleID'] ?? null;

        // ถ้า role_id เป็น 1 หรือ 2 => แสดงทั้งหมด
        if (in_array($roleId, [1, 2])) {
            $works = Work::with([
                'station',
                'customer',
                'employees.team.members.user', // preload ทีมและสมาชิกของทีม
                'employees', // สำรองไว้
                'station.dispensers.brand',
                'station.dispensers.nozzles',
            ])->get();
        }
        // ถ้า role_id เป็น 3 => แสดงเฉพาะของตัวเอง
        elseif ($roleId == 3) {
            $workIds = WorkEmployee::where('UserID', $userId)->pluck('WorkID');

            $works = Work::with([
                'station',
                'customer',
                'employees.user'
            ])->whereIn('WorkID', $workIds)->get();
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $works
        ]);
    }
    public function detail($WorkID)
    {

        $works = Work::with([
            'station',
            'customer',
            'employees.team.members.user', // preload ทีมและสมาชิกของทีม
            'employees', // สำรองไว้
            'station.dispensers.brand',
            'station.dispensers.nozzles',
        ])->where('workID', $WorkID)
            ->get();
        return view('work.detail', compact('works'));
    }
    public function getdata($workID)
    {
        $work = Work::with([
            'station.dispensers.brand',
            'station.dispensers.nozzles.fuleType',
            'station.dispensers.nozzles.inspectionRecords' => function ($query) use ($workID) {
                $query->where('WorkID', $workID); // ดึงเฉพาะ record ของงานนี้
            }
        ])->where('WorkID', $workID)->first();

        if (!$work) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลงาน'
            ], 404);
        }

        $nozzles = [];

        foreach ($work->station->dispensers as $dispenser) {
            foreach ($dispenser->nozzles as $nozzle) {
                $record = $nozzle->inspectionRecords->first(); // ใช้ first() เพื่อดึง 1 รายการ

                $nozzles[] = [
                    'WorkID' => $workID,
                    'StationID' => $work->StationID,
                    'DispenserID' => $dispenser->DispenserID,
                    'NozzleID' => $nozzle->NozzleID ?? '-',
                    'NozzleNumber' => $nozzle->NozzleNumber ?? '-',
                    'Brand' => $dispenser->brand->BrandName ?? '-',
                    'Modal' => $dispenser->Model ?? '-',
                    'fuletype' => $nozzle->fuleType->FuleTypeName ?? '-',
                    'FlowRate' => $dispenser->FlowRate ?? '-',
                    'MitterBegin' => $record?->MitterBegin ?? '-',
                    'MitterEnd' => $record?->MitterEnd ?? '-',
                    'MMQ_1L' => is_numeric($record?->MMQ_1L) ? number_format($record->MMQ_1L, 0) : '-',
                    'MMQ_5L' => is_numeric($record?->MMQ_5L) ? number_format($record->MMQ_5L, 0) : '-',
                    'MPE_5L' => is_numeric($record?->MPE_5L) ? number_format($record->MPE_5L, 0) : '-',
                    'MPE_20L' => is_numeric($record?->MPE_20L) ? number_format($record->MPE_20L, 0) : '-',
                    'Repeat5L_1' => is_numeric($record?->Repeat5L_1) ? number_format($record->Repeat5L_1, 0) : '-',
                    'Repeat5L_2' => is_numeric($record?->Repeat5L_2) ? number_format($record->Repeat5L_2, 0) : '-',
                    'Repeat5L_3' => is_numeric($record?->Repeat5L_3) ? number_format($record->Repeat5L_3, 0) : '-',
                    'Repeat20L_1' => is_numeric($record?->Repeat20L_1) ? number_format($record->Repeat20L_1, 0) : '-',
                    'Repeat20L_2' => is_numeric($record?->Repeat20L_2) ? number_format($record->Repeat20L_2, 0) : '-',
                    'Repeat20L_3' => is_numeric($record?->Repeat20L_3) ? number_format($record->Repeat20L_3, 0) : '-',
                    'SealNumber' => $record?->SealNumber ?? '-',
                    'KFactor' => $record?->KFactor ?? '-',
                    'status' => $record?->status ?? '-',
                    'VR_1' => $record?->VR_1 ?? '-',
                    'VR_2' => $record?->VR_2 ?? '-',
                    'VR_3' => $record?->VR_3 ?? '-',
                    'VR_4' => $record?->VR_4 ?? '-',
                    'VR_5' => $record?->VR_5 ?? '-',
                    'SNS_True' => $record?->SNS_True ?? '-',
                    'SNS_False' => $record?->SNS_False ?? '-',
                    'SN' => $nozzle->SN ?? '-',     // กรอกเพิ่มถ้ามี
                    'MMQ' => $nozzle->MMQ ?? '-',    // คำนวณเพิ่มภายหลัง
                    'Qmax' => $nozzle->Qmax ?? '-',   // คำนวณเพิ่มภายหลัง
                    'Qmin' => $nozzle->Qmin ?? '-',   // คำนวณเพิ่มภายหลัง
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $nozzles
        ]);
    }
    public function getAvailableSheetNumbers(): JsonResponse
    {
        try {
            // ดึง sheet_no ทั้งหมดจาก WamVerifyRequest ที่ยังไม่ถูกใช้ใน Work
            $availableSheets = WamVerifyRequest::whereNotIn('sheet_no', function ($query) {
                $query->select('sheet_no')
                    ->from('work')
                    ->whereNotNull('sheet_no');
            })
                ->whereNotNull('sheet_no')
                ->where('sheet_no', '!=', '')
                ->select([
                    'id',
                    'sheet_no',
                    'merchan_name',
                    'customer_name',
                    'vb_prov_name',
                    'status',
                    'total_request',
                    'have_inspector',
                    'create_time'
                ])
                ->orderBy('create_time', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Available sheet numbers retrieved successfully',
                'data' => $availableSheets,
                'total' => $availableSheets->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get available sheet numbers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get available sheet numbers: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'StationID' => 'required|integer',
            'CustomerID' => 'nullable|integer',
            'AppointmentDate' => 'required|date',
        ]);

        // ดึงตำแหน่งปั๊ม (Station)
        $station = \App\Models\Station::findOrFail($validated['StationID']);

        // จุดเริ่มต้นอาจมาจากสำนักงานใหญ่ หรือ GPS เจ้าหน้าที่
        $originLat = env('ORIGIN_LAT', 13.7563);
        $originLng = env('ORIGIN_LNG', 100.5018);

        // คำนวณระยะทาง
        $distanceData = \App\Http\Controllers\LocationController::getDrivingDistance(
            $originLat,
            $originLng,
            $station->Latitude,
            $station->Longitude
        );

        // ดึง User จาก session หรือ Auth
        $userSqlSrv = session('user_sqlsrv');
        $userId = $userSqlSrv['UserID'] ?? auth()->id();

        // เพิ่มข้อมูล Work
        $work = Work::create([
            'StationID' => $validated['StationID'],
            'CustomerID' => $validated['CustomerID'] ?? null,
            'AppointmentDate' => $validated['AppointmentDate'],
            'distance' => $distanceData['distance_km'] ?? null,
            'Status' => 0,
            'UserCreate' => $userId,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'สร้างงานสำเร็จ',
            'data' => $work
        ]);
    }
}
