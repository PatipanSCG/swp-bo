<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PromotionController extends Controller
{
    /**
     * แสดงหน้ารายการโปรโมชั่น
     */
    public function index(): View
    {
        return view('promotions.index');
    }

    /**
     * DataTable API สำหรับแสดงข้อมูลโปรโมชั่น
     */
    public function datatable(Request $request): JsonResponse
    {
        $query = Promotion::select([
            'id', 'type', 'detail', 'quantity', 'unit_price', 'status', 'created_at'
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->get('type') . '%');
        }

        if ($request->filled('search')) {
            $query->where('detail', 'like', '%' . $request->get('search') . '%');
        }

        return DataTables::of($query)
            ->addColumn('actions', function ($promotion) {
                return '
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-info btn-view-promotion" data-id="' . $promotion->id . '" title="ดูรายละเอียด">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning btn-edit-promotion" data-id="' . $promotion->id . '" title="แก้ไข">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete-promotion" data-id="' . $promotion->id . '" data-type="' . htmlspecialchars($promotion->type) . '" title="ลบ">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->addColumn('status_badge', function ($promotion) {
                $badges = [
                    'active' => 'badge-success',
                    'inactive' => 'badge-secondary',
                    'expired' => 'badge-danger',
                ];
                $texts = [
                    'active' => 'เปิดใช้งาน',
                    'inactive' => 'ปิดใช้งาน',
                    'expired' => 'หมดอายุ',
                ];
                $badgeClass = $badges[$promotion->status] ?? 'badge-secondary';
                $badgeText = $texts[$promotion->status] ?? $promotion->status;
                
                return '<span class="badge ' . $badgeClass . '">' . $badgeText . '</span>';
            })
            ->addColumn('total_value', function ($promotion) {
                return '<strong>฿' . number_format($promotion->total_value, 2) . '</strong>';
            })
            ->addColumn('unit_price_formatted', function ($promotion) {
                return '฿' . number_format($promotion->unit_price, 2);
            })
            ->editColumn('quantity', function ($promotion) {
                return number_format($promotion->quantity);
            })
            ->editColumn('detail', function ($promotion) {
                return strlen($promotion->detail) > 50 
                    ? substr($promotion->detail, 0, 50) . '...' 
                    : $promotion->detail;
            })
            ->editColumn('created_at', function ($promotion) {
                return $promotion->created_at->format('d/m/Y H:i');
            })
            ->rawColumns(['actions', 'status_badge', 'total_value', 'unit_price_formatted'])
            ->make(true);
    }

    /**
     * เพิ่มโปรโมชั่นใหม่
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|max:50',
                'detail' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0',
                'status' => 'required|string|in:active,inactive,expired',
            ], [
                'type.required' => 'กรุณาระบุประเภทโปรโมชั่น',
                'detail.required' => 'กรุณาระบุรายละเอียดโปรโมชั่น',
                'quantity.required' => 'กรุณาระบุจำนวน',
                'quantity.min' => 'จำนวนต้องมากกว่า 0',
                'unit_price.required' => 'กรุณาระบุราคาต่อหน่วย',
                'unit_price.min' => 'ราคาต้องมากกว่าหรือเท่ากับ 0',
                'status.required' => 'กรุณาระบุสถานะ',
                'status.in' => 'สถานะต้องเป็น active, inactive หรือ expired',
            ]);
            
            $promotion = Promotion::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'โปรโมชั่นถูกสร้างเรียบร้อยแล้ว',
                'data' => $promotion
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating promotion: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการสร้างโปรโมชั่น'
            ], 500);
        }
    }

    /**
     * แสดงรายละเอียดโปรโมชั่น
     */
    public function show($id): JsonResponse
    {
        try {
            $promotion = Promotion::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $promotion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลโปรโมชั่น'
            ], 404);
        }
    }

    /**
     * อัปเดตโปรโมชั่น
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $promotion = Promotion::findOrFail($id);

            $validated = $request->validate([
                'type' => 'required|string|max:50',
                'detail' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0',
                'status' => 'required|string|in:active,inactive,expired',
            ], [
                'type.required' => 'กรุณาระบุประเภทโปรโมชั่น',
                'detail.required' => 'กรุณาระบุรายละเอียดโปรโมชั่น',
                'quantity.required' => 'กรุณาระบุจำนวน',
                'quantity.min' => 'จำนวนต้องมากกว่า 0',
                'unit_price.required' => 'กรุณาระบุราคาต่อหน่วย',
                'unit_price.min' => 'ราคาต้องมากกว่าหรือเท่ากับ 0',
                'status.required' => 'กรุณาระบุสถานะ',
                'status.in' => 'สถานะต้องเป็น active, inactive หรือ expired',
            ]);

            $promotion->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'โปรโมชั่นถูกอัปเดตเรียบร้อยแล้ว',
                'data' => $promotion
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating promotion: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัปเดตโปรโมชั่น'
            ], 500);
        }
    }

    /**
     * ลบโปรโมชั่น
     */
    public function destroy($id): JsonResponse
    {
        try {
            $promotion = Promotion::findOrFail($id);
            $promotion->delete();

            return response()->json([
                'success' => true,
                'message' => 'โปรโมชั่นถูกลบเรียบร้อยแล้ว'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting promotion: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบโปรโมชั่น'
            ], 500);
        }
    }

    /**
     * ลบหลายรายการ
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids');
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาเลือกรายการที่ต้องการลบ'
                ], 400);
            }

            Promotion::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'ลบโปรโมชั่น ' . count($ids) . ' รายการเรียบร้อยแล้ว'
            ]);

        } catch (\Exception $e) {
            Log::error('Error bulk deleting promotions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบโปรโมชั่น'
            ], 500);
        }
    }

    /**
     * เปลี่ยนสถานะโปรโมชั่น
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $promotion = Promotion::findOrFail($id);
            
            $newStatus = $promotion->status === 'active' ? 'inactive' : 'active';
            $promotion->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'สถานะโปรโมชั่นถูกเปลี่ยนเรียบร้อยแล้ว',
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling promotion status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ'
            ], 500);
        }
    }
}