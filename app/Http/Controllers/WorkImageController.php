<?php
// app/Http/Controllers/Api/WorkImageController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WorkImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

class WorkImageController extends Controller
{
    /**
     * อัพโหลดรูปภาพทีละภาพ
     * POST /api/work-images/upload
     */
    public function uploadSingleImage(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'workid' => 'required|integer|min:1',
            'type' => 'required|integer|in:1,2,3,4,5,6,7,8,9', // เพิ่ม type 1
            'nozzle_id' => 'required|integer|min:0', // เปลี่ยนเป็น min:0 เพื่อรองรับ type 1
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240' // 10MB
        ], [
            'workid.required' => 'กรุณาระบุ Work ID',
            'workid.integer' => 'Work ID ต้องเป็นตัวเลข',
            'type.required' => 'กรุณาระบุ Type',
            'type.integer' => 'Type ต้องเป็นตัวเลข',
            'type.in' => 'Type ต้องเป็น 1-9 เท่านั้น',
            'nozzle_id.required' => 'กรุณาระบุ Nozzle ID',
            'nozzle_id.integer' => 'Nozzle ID ต้องเป็นตัวเลข',
            'nozzle_id.min' => 'Nozzle ID ต้องเป็น 0 หรือมากกว่า',
            'image.required' => 'กรุณาเลือกรูปภาพ',
            'image.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'image.mimes' => 'รองรับเฉพาะไฟล์ jpeg, png, jpg, gif, webp',
            'image.max' => 'ขนาดไฟล์ต้องไม่เกิน 10MB'
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

            $image = $request->file('image');
            $workId = $request->workid;
            $type = $request->type;
            $nozzleId = $request->nozzle_id;

            // เก็บข้อมูลไฟล์ก่อนที่จะถูกลบ
            $originalName = $image->getClientOriginalName();
            $mimeType = $image->getMimeType();
            $extension = $image->getClientOriginalExtension();
            $fileSize = $image->getSize();

            // ตรวจสอบขนาดรูปภาพ
            $imageInfo = @getimagesize($image->getPathname());
            $width = $imageInfo ? $imageInfo[0] : 0;
            $height = $imageInfo ? $imageInfo[1] : 0;

            // ตรวจสอบว่ามีรูปภาพชุดนี้อยู่แล้วหรือไม่
            if ($type == 1) {
                // ภาพสถานี - ตรวจสอบเฉพาะ workid และ type
                $existingCount = WorkImage::where('workid', $workId)
                    ->where('type', $type)
                    ->count();
            } else {
                // ภาพ nozzle - ตรวจสอบ workid, type, และ nozzle_id
                $existingCount = WorkImage::where('workid', $workId)
                    ->where('type', $type)
                    ->where('NozzleID', $nozzleId)
                    ->count();
            }
            // สร้างชื่อไฟล์ที่ไม่ซ้ำ
            $timestamp = now()->format('YmdHis');
            $randomString = Str::random(6);
            if ($type == 1) {
                // สำหรับภาพสถานี
                $filename = "station_{$workId}_type_{$type}_{$timestamp}_{$randomString}.{$extension}";
            } else {
                // สำหรับภาพ nozzle
                $filename = "work_{$workId}_type_{$type}_nozzle_{$nozzleId}_{$timestamp}_{$randomString}.{$extension}";
            }
            // บันทึกไฟล์โดยใช้ Laravel Storage
            $path = $image->storeAs('work_images', $filename, 'public');

            if (!$path) {
                throw new \Exception('ไม่สามารถบันทึกไฟล์ได้');
            }

            // สร้าง thumbnail หลังจากบันทึกไฟล์แล้ว
            $this->createThumbnailFromPath(storage_path("app/public/{$path}"), $filename);

            // บันทึกข้อมูลลงฐานข้อมูล
            $workImage = WorkImage::create([
                'workid' => $workId,
                'type' => $type,
                'NozzleID' => $nozzleId,
                'imagename' => $filename
            ]);

            // Log การอัพโหลด
            \Log::info("Image uploaded successfully", [
                'id' => $workImage->id,
                'workid' => $workId,
                'type' => $type,
                'nozzle_id' => $nozzleId,
                'filename' => $filename,
                'size' => $fileSize,
                'dimensions' => "{$width}x{$height}"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'อัพโหลดรูปภาพสำเร็จ',
                'data' => [
                    'id' => $workImage->id,
                    'workid' => $workImage->workid,
                    'type' => $workImage->type,
                    'nozzle_id' => $workImage->NozzleID,
                    'imagename' => $workImage->imagename,
                    'image_url' => $this->getImageUrl($filename),
                    'file_size' => $this->formatFileSize($fileSize),
                    'dimensions' => "{$width}x{$height}",
                    'sequence_number' => $existingCount + 1,
                    'created_at' => $workImage->created_at,
                    'upload_info' => [
                        'original_name' => $originalName,
                        'mime_type' => $mimeType,
                        'extension' => $extension
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // ลบไฟล์ที่อัพโหลดไปแล้ว (ถ้ามี)
            if (isset($filename) && Storage::disk('public')->exists("work_images/{$filename}")) {
                Storage::disk('public')->delete("work_images/{$filename}");
            }

            \Log::error("Image upload failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการอัพโหลด: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getStationImageStats($workId)
    {
        try {
            $stationImages = WorkImage::where('workid', $workId)
                ->where('type', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalImages = $stationImages->count();
            $latestImage = $stationImages->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'work_id' => $workId,
                    'total_station_images' => $totalImages,
                    'latest_upload' => $latestImage ? $latestImage->created_at : null,
                    'images' => $stationImages->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'imagename' => $image->imagename,
                            'image_url' => $this->getImageUrl($image->imagename),
                            'created_at' => $image->created_at
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ดูรูปภาพเดี่ยวตาม ID
     * GET /api/work-images/{id}
     */
    public function getSingleImage($id)
    {
        try {
            $workImage = WorkImage::find($id);

            if (!$workImage) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบรูปภาพที่ระบุ'
                ], 404);
            }

            // ตรวจสอบว่าไฟล์จริงยังอยู่หรือไม่
            $fileExists = Storage::disk('public')->exists("work_images/{$workImage->imagename}");

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $workImage->id,
                    'workid' => $workImage->workid,
                    'type' => $workImage->type,
                    'nozzle_id' => $workImage->NozzleID,
                    'imagename' => $workImage->imagename,
                    'image_url' => $this->getImageUrl($workImage->imagename),
                    'file_exists' => $fileExists,
                    'file_size' => $fileExists ? $this->getFileSize($workImage->imagename) : null,
                    'created_at' => $workImage->created_at,
                    'updated_at' => $workImage->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ดูรายการรูปภาพตามเงื่อนไข
     * GET /api/work-images
     */
    public function getImagesList(Request $request)
    {
        try {
            $query = WorkImage::query();

            // Filter by workid
            if ($request->has('workid') && $request->workid !== '') {
                $query->where('workid', $request->workid);
            }

            // Filter by type
            if ($request->has('type') && $request->type !== '') {
                $query->where('type', $request->type);
            }

            // Filter by nozzle_id
            if ($request->has('nozzle_id') && $request->nozzle_id !== '') {
                $query->where('NozzleID', $request->nozzle_id);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            if (in_array($sortBy, ['id', 'workid', 'type', 'NozzleID', 'created_at', 'updated_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100); // สูงสุด 100 รายการ
            $images = $query->paginate($perPage);

            $formattedImages = $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'workid' => $image->workid,
                    'type' => $image->type,
                    'nozzle_id' => $image->NozzleID,
                    'imagename' => $image->imagename,
                    'image_url' => $this->getImageUrl($image->imagename),
                    'thumbnail_url' => $this->getThumbnailUrl($image->imagename),
                    'created_at' => $image->created_at,
                    'updated_at' => $image->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedImages,
                'pagination' => [
                    'current_page' => $images->currentPage(),
                    'per_page' => $images->perPage(),
                    'total' => $images->total(),
                    'last_page' => $images->lastPage(),
                    'from' => $images->firstItem(),
                    'to' => $images->lastItem()
                ],
                'filters' => [
                    'workid' => $request->workid,
                    'type' => $request->type,
                    'nozzle_id' => $request->nozzle_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ลบรูปภาพเดี่ยว
     * DELETE /api/work-images/{id}
     */
    public function deleteSingleImage($id)
    {
        try {
            $workImage = WorkImage::find($id);

            if (!$workImage) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบรูปภาพที่ระบุ'
                ], 404);
            }

            DB::beginTransaction();

            // เก็บข้อมูลสำหรับ log
            $imageData = [
                'id' => $workImage->id,
                'workid' => $workImage->workid,
                'type' => $workImage->type,
                'nozzle_id' => $workImage->NozzleID,
                'imagename' => $workImage->imagename
            ];

            // ลบไฟล์
            $this->deleteImageFiles($workImage->imagename);

            // ลบ record จากฐานข้อมูล
            $workImage->delete();

            DB::commit();

            \Log::info("Image deleted successfully", $imageData);

            return response()->json([
                'success' => true,
                'message' => 'ลบรูปภาพสำเร็จ',
                'deleted_image' => $imageData
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error("Image deletion failed", [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ดาวน์โหลดรูปภาพ
     * GET /api/work-images/{id}/download
     */
    public function downloadImage($id)
    {
        try {
            $workImage = WorkImage::find($id);

            if (!$workImage) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบรูปภาพที่ระบุ'
                ], 404);
            }

            $filePath = storage_path("app/public/work_images/{$workImage->imagename}");

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบไฟล์รูปภาพ'
                ], 404);
            }

            return response()->download($filePath, $workImage->imagename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ประมวลผลรูปภาพ (resize, optimize)
     */
    private function processImage($imageFile, $filename)
    {
        try {
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            $directory = storage_path('app/public/work_images');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // บันทึกไฟล์ต้นฉบับ
            $originalPath = $directory . '/' . $filename;
            move_uploaded_file($imageFile->getPathname(), $originalPath);

            // สร้าง thumbnail (optional)
            $this->createThumbnail($originalPath, $filename);

            return true;
        } catch (\Exception $e) {
            \Log::error("Image processing failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้าง thumbnail
     */
    private function createThumbnail($originalPath, $filename)
    {
        try {
            $thumbnailDir = storage_path('app/public/work_images/thumbnails');
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $thumbnailPath = $thumbnailDir . '/' . $filename;

            // ใช้ GD library สร้าง thumbnail
            $this->resizeImage($originalPath, $thumbnailPath, 300, 300);
        } catch (\Exception $e) {
            \Log::warning("Thumbnail creation failed: " . $e->getMessage());
        }
    }

    private function createThumbnailFromPath($originalPath, $filename)
    {
        try {
            $thumbnailDir = storage_path('app/public/work_images/thumbnails');
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $thumbnailPath = $thumbnailDir . '/' . $filename;

            // ตรวจสอบว่าไฟล์ต้นฉบับมีอยู่จริง
            if (!file_exists($originalPath)) {
                \Log::warning("Original file not found for thumbnail: {$originalPath}");
                return false;
            }

            // ใช้ GD library สร้าง thumbnail
            return $this->resizeImage($originalPath, $thumbnailPath, 300, 300);
        } catch (\Exception $e) {
            \Log::warning("Thumbnail creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบ method processImage เดิม และใช้ createThumbnailFromPath แทน
     */

    /**
     * Resize รูปภาพ (ปรับปรุงแล้ว)
     */
    private function resizeImage($source, $destination, $maxWidth, $maxHeight)
    {
        try {
            if (!file_exists($source)) {
                return false;
            }

            $imageInfo = @getimagesize($source);
            if (!$imageInfo) {
                return false;
            }

            list($width, $height, $type) = $imageInfo;

            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);

            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            if (!$newImage) {
                return false;
            }

            switch ($type) {
                case IMAGETYPE_JPEG:
                    $sourceImage = @imagecreatefromjpeg($source);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = @imagecreatefrompng($source);
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                    imagefill($newImage, 0, 0, $transparent);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = @imagecreatefromgif($source);
                    break;
                case IMAGETYPE_WEBP:
                    $sourceImage = @imagecreatefromwebp($source);
                    break;
                default:
                    return false;
            }

            if (!$sourceImage) {
                imagedestroy($newImage);
                return false;
            }

            $result = imagecopyresampled(
                $newImage,
                $sourceImage,
                0,
                0,
                0,
                0,
                $newWidth,
                $newHeight,
                $width,
                $height
            );

            if (!$result) {
                imagedestroy($sourceImage);
                imagedestroy($newImage);
                return false;
            }

            // บันทึกไฟล์ thumbnail
            $saveResult = false;
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $saveResult = @imagejpeg($newImage, $destination, 85);
                    break;
                case IMAGETYPE_PNG:
                    $saveResult = @imagepng($newImage, $destination, 8);
                    break;
                case IMAGETYPE_GIF:
                    $saveResult = @imagegif($newImage, $destination);
                    break;
                case IMAGETYPE_WEBP:
                    $saveResult = @imagewebp($newImage, $destination, 85);
                    break;
            }

            imagedestroy($sourceImage);
            imagedestroy($newImage);

            return $saveResult;
        } catch (\Exception $e) {
            \Log::error("Resize image failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ลบไฟล์รูปภาพและ thumbnail
     */
    private function deleteImageFiles($filename)
    {
        // ลบไฟล์หลัก
        if (Storage::disk('public')->exists("work_images/{$filename}")) {
            Storage::disk('public')->delete("work_images/{$filename}");
        }

        // ลบ thumbnail
        if (Storage::disk('public')->exists("work_images/thumbnails/{$filename}")) {
            Storage::disk('public')->delete("work_images/thumbnails/{$filename}");
        }
    }

    /**
     * สร้าง URL รูปภาพ
     */
    private function getImageUrl($filename)
    {
        return asset("storage/work_images/{$filename}");
    }

    /**
     * สร้าง URL thumbnail
     */
    private function getThumbnailUrl($filename)
    {
        $thumbnailPath = "work_images/thumbnails/{$filename}";
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return asset("storage/{$thumbnailPath}");
        }
        return $this->getImageUrl($filename); // fallback to original
    }

    /**
     * ดูขนาดไฟล์
     */
    private function getFileSize($filename)
    {
        $path = storage_path("app/public/work_images/{$filename}");
        if (file_exists($path)) {
            return $this->formatFileSize(filesize($path));
        }
        return null;
    }

    /**
     * แปลงขนาดไฟล์เป็นรูปแบบที่อ่านง่าย
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
