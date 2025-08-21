<?php
// app/Http/Controllers/Api/DispenserCheckController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispenser;
use App\Models\DispenserCheck;
use App\Models\DispenserCheckDetail;
use App\Models\DispenserCheckImage;
use App\Models\CheckItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PDF;

class DispenserCheckController extends Controller
{
    /**
     * ดูรายการตู้จ่ายของ station
     * GET /api/dispensers/{stationId}
     */
    public function getDispensers($stationId)
    {
        try {
            // ดึงข้อมูลตู้จ่ายและตรวจสอบ relationship
            $dispensers = Dispenser::where('StationID', $stationId)
                ->where('Status', 1)
                ->get();

            $formattedDispensers = $dispensers->map(function ($dispenser) {
                // ดึงการตรวจเช็คล่าสุด
                $latestCheck = DispenserCheck::where('DispenserID', $dispenser->DispenserID)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // ดึงข้อมูล brand และ model
                $brand = $dispenser->brand->BrandName ?? 'N/A';
                $model = $dispenser->model->ModelName ?? $dispenser->Model ?? 'N/A';
                $stationName = $dispenser->station->StationName ?? '';

                return [
                    'DispenserID' => $dispenser->DispenserID,
                    'DispenserNumber' => $dispenser->DispenserID,
                    'Brand' => $brand,
                    'Model' => $model,
                    'SerialNumber' => $dispenser->SN ?? 'N/A',
                    'StationName' => $stationName,
                    'latest_check' => $latestCheck ? [
                        'id' => $latestCheck->id,
                        'check_date' => $latestCheck->check_date,
                        'status' => $latestCheck->status,
                        'completion_percentage' => round(($latestCheck->completed_items / max($latestCheck->total_items, 1)) * 100, 2)
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedDispensers
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading dispensers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ดูรายการตรวจเช็คของ work และ station
     * GET /api/dispenser-checks/{workId}/{stationId}
     */
    public function getDispenserChecks($workId, $stationId)
    {
        try {
            $checks = DispenserCheck::where('WorkID', $workId)
                ->where('StationID', $stationId)
                ->with(['dispenser'])
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedChecks = $checks->map(function ($check) {
                return [
                    'id' => $check->id,
                    'dispenser' => [
                        'DispenserID' => $check->dispenser->DispenserID,
                        'DispenserNumber' => $check->dispenser->DispenserID,
                        'Brand' => $check->dispenser->brand->BrandName ?? 'N/A',
                        'Model' => $check->dispenser->model->ModelName ?? $check->dispenser->Model ?? 'N/A'
                    ],
                    'inspector_name' => $check->inspector_name,
                    'check_date' => $check->check_date,
                    'status' => $check->status,
                    'completion_percentage' => round(($check->completed_items / max($check->total_items, 1)) * 100, 2),
                    'total_items' => $check->total_items,
                    'completed_items' => $check->completed_items,
                    'normal_items' => $check->normal_items,
                    'problem_items' => $check->problem_items,
                    'remarks' => $check->remarks
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedChecks
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading dispenser checks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * เริ่มการตรวจเช็คตู้ใหม่
     * POST /api/dispenser-checks/start
     */
    public function startCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'work_id' => 'required|integer',
            'station_id' => 'required|integer',
            'dispenser_id' => 'required|integer',
            'inspector_name' => 'required|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // ตรวจสอบว่ามีการตรวจเช็คอยู่แล้วหรือไม่
            $existingCheck = DispenserCheck::where('WorkID', $request->work_id)
                ->where('DispenserID', $request->dispenser_id)
                ->first();

            if ($existingCheck) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'พบการตรวจเช็คที่มีอยู่แล้ว',
                    'data' => $existingCheck
                ]);
            }

            // นับจำนวนรายการตรวจเช็ค - ถ้าไม่มี table CheckItem ให้ใช้ค่าคงที่
            $totalItems = 10; // หรือ CheckItem::where('active', true)->count();

            $check = DispenserCheck::create([
                'WorkID' => $request->work_id,
                'StationID' => $request->station_id,
                'DispenserID' => $request->dispenser_id,
                'inspector_name' => $request->inspector_name,
                'check_date' => now(),
                'total_items' => $totalItems,
                'completed_items' => 0,
                'normal_items' => 0,
                'problem_items' => 0,
                'status' => 'draft'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'เริ่มการตรวจเช็คเรียบร้อย',
                'data' => $check
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error starting check: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ดูรายละเอียดการตรวจเช็ค
     * GET /api/dispenser-checks/{id}
     */
    public function getCheckDetail($id)
    {
        try {
            $check = DispenserCheck::find($id);
            
            if (!$check) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลการตรวจเช็ค'
                ], 404);
            }

            // ดึงข้อมูล dispenser
            $dispenser = Dispenser::find($check->DispenserID);

            // สร้างรายการตรวจเช็คแบบ mock ถ้าไม่มี CheckItem table
            $checkItems = $this->getCheckItemsData();
            
            // ดึงรายละเอียดการตรวจที่มีอยู่
            $details = DispenserCheckDetail::where('dispenser_check_id', $id)->get();

            // รวมข้อมูล
            $itemsWithDetails = collect($checkItems)->map(function ($item) use ($details) {
                $detail = $details->firstWhere('check_item_id', $item['id']);
                
                $result = [
                    'check_item' => $item,
                    'detail' => $detail
                ];

                // ถ้ามี detail ให้ดึงรูปภาพด้วย
                if ($detail) {
                    $images = DispenserCheckImage::where('dispenser_check_detail_id', $detail->id)->get();
                    $result['detail']->images = $images->map(function ($img) {
                        return [
                            'id' => $img->id,
                            'image_type' => $img->image_type,
                            'imagename' => $img->imagename,
                            'image_url' => Storage::url('dispenser_checks/' . $img->imagename),
                            'thumbnail_url' => Storage::url('dispenser_checks/thumbnails/' . $img->imagename),
                            'image_description' => $img->image_description
                        ];
                    });
                }

                return $result;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'check' => $check,
                    'dispenser' => [
                        'DispenserID' => $dispenser->DispenserID,
                        'DispenserNumber' => $dispenser->DispenserID,
                        'Brand' => $dispenser->brand->BrandName ?? 'N/A',
                        'Model' => $dispenser->model->ModelName ?? $dispenser->Model ?? 'N/A',
                        'SerialNumber' => $dispenser->SN ?? 'N/A'
                    ],
                    'items' => $itemsWithDetails
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading check detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * บันทึกผลการตรวจเช็คแต่ละข้อ
     * POST /api/dispenser-checks/{id}/save-item
     */
    public function saveCheckItem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'check_item_id' => 'required|integer',
            'result' => 'required|in:normal,problem',
            'problem_description' => 'nullable|string',
            'inspector_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $check = DispenserCheck::find($id);
            if (!$check) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลการตรวจเช็ค'
                ], 404);
            }

            // บันทึกหรืออัพเดทรายละเอียด
            $detail = DispenserCheckDetail::updateOrCreate(
                [
                    'dispenser_check_id' => $id,
                    'check_item_id' => $request->check_item_id
                ],
                [
                    'result' => $request->result,
                    'problem_description' => $request->problem_description,
                    'inspector_notes' => $request->inspector_notes,
                    'checked_at' => now()
                ]
            );

            // อัพเดทสถิติการตรวจเช็ค
            $this->updateCheckStatistics($check);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'บันทึกข้อมูลเรียบร้อย',
                'data' => $detail
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving check item: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * อัพโหลดรูปภาพการตรวจเช็ค
     * POST /api/dispenser-checks/upload-image
     */
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'detail_id' => 'required|integer',
            'image_type' => 'required|in:normal,problem',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'image_description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $detail = DispenserCheckDetail::find($request->detail_id);
            if (!$detail) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลรายละเอียดการตรวจเช็ค'
                ], 404);
            }

            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $timestamp = now()->format('YmdHis');
            $randomString = Str::random(6);
            $filename = "dispenser_check_{$request->detail_id}_{$request->image_type}_{$timestamp}_{$randomString}.{$extension}";

            // สร้างโฟลเดอร์ถ้าไม่มี
            if (!Storage::disk('public')->exists('dispenser_checks')) {
                Storage::disk('public')->makeDirectory('dispenser_checks');
            }
            if (!Storage::disk('public')->exists('dispenser_checks/thumbnails')) {
                Storage::disk('public')->makeDirectory('dispenser_checks/thumbnails');
            }

            // บันทึกไฟล์
            $path = $image->storeAs('dispenser_checks', $filename, 'public');

            if (!$path) {
                throw new \Exception('ไม่สามารถบันทึกไฟล์ได้');
            }

            // สร้าง thumbnail
            $this->createThumbnail(storage_path("app/public/{$path}"), $filename);

            // บันทึกข้อมูลลงฐานข้อมูล
            $checkImage = DispenserCheckImage::create([
                'dispenser_check_detail_id' => $request->detail_id,
                'image_type' => $request->image_type,
                'imagename' => $filename,
                'image_description' => $request->image_description
            ]);

            return response()->json([
                'success' => true,
                'message' => 'อัพโหลดรูปภาพเรียบร้อย',
                'data' => [
                    'id' => $checkImage->id,
                    'image_type' => $checkImage->image_type,
                    'imagename' => $checkImage->imagename,
                    'image_url' => Storage::url('dispenser_checks/' . $filename),
                    'thumbnail_url' => Storage::url('dispenser_checks/thumbnails/' . $filename),
                    'image_description' => $checkImage->image_description
                ]
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error uploading image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัพโหลด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ลบรูปภาพ
     * DELETE /api/dispenser-checks/images/{id}
     */
    public function deleteImage($id)
    {
        try {
            $image = DispenserCheckImage::find($id);

            if (!$image) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบรูปภาพที่ระบุ'
                ], 404);
            }

            // ลบไฟล์
            Storage::disk('public')->delete('dispenser_checks/' . $image->imagename);
            Storage::disk('public')->delete('dispenser_checks/thumbnails/' . $image->imagename);

            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'ลบรูปภาพเรียบร้อย'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * เสร็จสิ้นการตรวจเช็ค
     * POST /api/dispenser-checks/{id}/complete
     */
    public function completeCheck(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $check = DispenserCheck::find($id);

            if (!$check) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลการตรวจเช็ค'
                ], 404);
            }

            // ตรวจสอบว่าตรวจเช็คครบทุกข้อหรือไม่
            if ($check->completed_items < $check->total_items) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณาตรวจเช็คให้ครบทุกข้อก่อนเสร็จสิ้น'
                ], 400);
            }

            $check->update([
                'status' => 'completed',
                'remarks' => $request->remarks,
                'completed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'เสร็จสิ้นการตรวจเช็คเรียบร้อย',
                'data' => $check
            ]);
        } catch (\Exception $e) {
            \Log::error('Error completing check: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export PDF รายงานการตรวจเช็ค
     * GET /api/dispenser-checks/{workId}/{stationId}/export-pdf
     */
    public function exportPDF($workId, $stationId)
    {
        try {
            $checks = DispenserCheck::where('WorkID', $workId)
                ->where('StationID', $stationId)
                ->where('status', 'completed')
                ->with(['dispenser'])
                ->orderBy('created_at')
                ->get();

            if ($checks->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลการตรวจเช็คที่เสร็จสิ้นแล้ว'
                ], 404);
            }

            $data = [
                'checks' => $checks,
                'work_id' => $workId,
                'station_id' => $stationId,
                'export_date' => now()->format('d/m/Y H:i:s'),
                'total_dispensers' => $checks->count(),
                'total_problems' => $checks->sum('problem_items')
            ];

            // ถ้าไม่มี PDF library ให้ return JSON แทน
            try {
                $pdf = PDF::loadView('reports.dispenser-check', $data);
                $filename = "dispenser_check_report_work_{$workId}_station_{$stationId}_" . now()->format('YmdHis') . ".pdf";
                return $pdf->download($filename);
            } catch (\Exception $e) {
                // Fallback to JSON response
                return response()->json([
                    'success' => true,
                    'message' => 'ข้อมูลรายงาน (PDF library ไม่พร้อมใช้งาน)',
                    'data' => $data
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการสร้าง PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * สร้าง thumbnail
     */
    private function createThumbnail($originalPath, $filename)
    {
        try {
            $thumbnailDir = storage_path('app/public/dispenser_checks/thumbnails');
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $thumbnailPath = $thumbnailDir . '/' . $filename;

            if (!file_exists($originalPath)) {
                return false;
            }

            $imageInfo = @getimagesize($originalPath);
            if (!$imageInfo) {
                return false;
            }

            list($width, $height, $type) = $imageInfo;
            $maxWidth = 300;
            $maxHeight = 300;

            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);

            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            switch ($type) {
                case IMAGETYPE_JPEG:
                    $sourceImage = @imagecreatefromjpeg($originalPath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = @imagecreatefrompng($originalPath);
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = @imagecreatefromgif($originalPath);
                    break;
                case IMAGETYPE_WEBP:
                    $sourceImage = @imagecreatefromwebp($originalPath);
                    break;
                default:
                    return false;
            }

            if (!$sourceImage) {
                imagedestroy($newImage);
                return false;
            }

            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            switch ($type) {
                case IMAGETYPE_JPEG:
                    $result = @imagejpeg($newImage, $thumbnailPath, 85);
                    break;
                case IMAGETYPE_PNG:
                    $result = @imagepng($newImage, $thumbnailPath, 8);
                    break;
                case IMAGETYPE_GIF:
                    $result = @imagegif($newImage, $thumbnailPath);
                    break;
                case IMAGETYPE_WEBP:
                    $result = @imagewebp($newImage, $thumbnailPath, 85);
                    break;
            }

            imagedestroy($sourceImage);
            imagedestroy($newImage);

            return $result;
        } catch (\Exception $e) {
            \Log::warning("Thumbnail creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * อัพเดทสถิติการตรวจเช็ค
     */
    private function updateCheckStatistics($check)
    {
        $details = DispenserCheckDetail::where('dispenser_check_id', $check->id)->get();
        
        $completedItems = $details->count();
        $normalItems = $details->where('result', 'normal')->count();
        $problemItems = $details->where('result', 'problem')->count();

        $check->update([
            'completed_items' => $completedItems,
            'normal_items' => $normalItems,
            'problem_items' => $problemItems
        ]);
    }

    /**
     * ดึงข้อมูลรายการตรวจเช็ค (Mock Data)
     */
    private function getCheckItemsData()
    {
        return [
            [
                'id' => 1,
                'item_number' => '1',
                'title' => 'ตรวจสอบสายพานหัวจ่าย',
                'equipment' => 'สายพาน'
            ],
            [
                'id' => 2,
                'item_number' => '2',
                'title' => 'ตรวจสอบหัวจ่ายน้ำมัน',
                'equipment' => 'หัวจ่าย'
            ],
            [
                'id' => 3,
                'item_number' => '3',
                'title' => 'ตรวจสอบระบบมิเตอร์',
                'equipment' => 'มิเตอร์'
            ],
            [
                'id' => 4,
                'item_number' => '4',
                'title' => 'ตรวจสอบท่อจ่ายน้ำมัน',
                'equipment' => 'ท่อจ่าย'
            ],
            [
                'id' => 5,
                'item_number' => '5',
                'title' => 'ตรวจสอบจอแสดงผล',
                'equipment' => 'จอแสดงผล'
            ],
            [
                'id' => 6,
                'item_number' => '6',
                'title' => 'ตรวจสอบระบบควบคุม',
                'equipment' => 'ระบบควบคุม'
            ],
            [
                'id' => 7,
                'item_number' => '7',
                'title' => 'ตรวจสอบความปลอดภัยใต้ตู้',
                'equipment' => 'ความปลอดภัย'
            ],
            [
                'id' => 8,
                'item_number' => '8',
                'title' => 'ตรวจสอบการรั่วซึม',
                'equipment' => 'ระบบป้องกันรั่ว'
            ],
            [
                'id' => 9,
                'item_number' => '9',
                'title' => 'ตรวจสอบระบบกราวด์',
                'equipment' => 'ระบบกราวด์'
            ],
            [
                'id' => 10,
                'item_number' => '10',
                'title' => 'ตรวจสอบฐานตู้และโครงสร้าง',
                'equipment' => 'โครงสร้าง'
            ]
        ];
    }
}