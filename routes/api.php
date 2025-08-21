<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FuelTypeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NozzleController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\WorkInspectionRecordController;
use App\Http\Controllers\NavCustomerController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\WorkImageController;
use App\Http\Controllers\Api\DispenserCheckController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/fuletype/list', [FuelTypeController::class, 'list']);
Route::get('/provinces/{province}/districts', [LocationController::class, 'getDistricts']);
Route::get('/districts/{district}/subdistricts', [LocationController::class, 'getSubdistricts']);
Route::prefix('nozzles')->controller(NozzleController::class)->group(function () {
    Route::post('/', 'store');               // สร้าง
    Route::get('/{id}', 'show');            // อ่าน
    Route::put('/{id}', 'update');          // อัปเดต
    Route::delete('/{id}', 'destroy');      // ลบ
});
Route::prefix('works')->controller(WorkController::class)->group(function () {
    // Route::post('/', 'store');               // สร้าง
    // Route::get('/', 'index');            // อ่าน
    Route::get('/{id}', 'getdata');            // อ่าน
    // Route::put('/{id}', 'update');          // อัปเดต
    // Route::delete('/{id}', 'destroy');      // ลบ
});
Route::prefix('promotions')->controller(PromotionController::class)->group(function () {
    Route::get('/', 'index')->name('works.index');
    Route::post('/', 'store');
    Route::get('/{id}', 'show');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});
Route::prefix('quotations')->controller(QuotationController::class)->group(function () {
    Route::get('/', 'apiIndex');
    
});
Route::post('/inspection-records', [WorkInspectionRecordController::class, 'store']);
Route::get('/export-nozzles/{stationid}', [WorkInspectionRecordController::class, 'exportLabelByStation']);
Route::get('/Navcustomers/{vatid}', [NavCustomerController::class, 'index'])->name('customers.index');
Route::get('/sales-quote', function () {
    return view('SQ.template');
});
Route::prefix('work-images')->group(function () {
    Route::post('/upload', [WorkImageController::class, 'uploadSingleImage']);
    Route::get('/', [WorkImageController::class, 'getImagesList']);
    Route::get('/{id}', [WorkImageController::class, 'getSingleImage'])->where('id', '[0-9]+');
    Route::delete('/{id}', [WorkImageController::class, 'deleteSingleImage'])->where('id', '[0-9]+');
    Route::get('/{id}/download', [WorkImageController::class, 'downloadImage'])->where('id', '[0-9]+');
});
Route::prefix('work-images-stats')->group(function () {
    // สถิติรูปภาพตาม work
    Route::get('/by-work/{workId}', [WorkImageController::class, 'getImageStatsByWork'])
         ->where('workId', '[0-9]+');
    
    // สถิติรูปภาพตาม nozzle
    Route::get('/by-nozzle/{nozzleId}', [WorkImageController::class, 'getImageStatsByNozzle'])
         ->where('nozzleId', '[0-9]+');
    
    // สถิติรูปภาพสถานี (ใหม่)
    Route::get('/station/{workId}', [WorkImageController::class, 'getStationImageStats'])
         ->where('workId', '[0-9]+');
});
Route::prefix('dispensers')->group(function () {
    // ดูรายการตู้จ่ายของ station
    Route::get('/{stationId}', [DispenserCheckController::class, 'getDispensers'])
         ->where('stationId', '[0-9]+');
});

Route::prefix('dispenser-checks')->group(function () {
    // ดูรายการการตรวจเช็คของ work และ station
    Route::get('/{workId}/{stationId}', [DispenserCheckController::class, 'getDispenserChecks'])
         ->where(['workId' => '[0-9]+', 'stationId' => '[0-9]+']);
    
    // เริ่มการตรวจเช็คใหม่
    Route::post('/start', [DispenserCheckController::class, 'startCheck']);
    
    // ดูรายละเอียดการตรวจเช็ค
    Route::get('/{id}', [DispenserCheckController::class, 'getCheckDetail'])
         ->where('id', '[0-9]+');
    
    // บันทึกผลการตรวจเช็คแต่ละข้อ
    Route::post('/{id}/save-item', [DispenserCheckController::class, 'saveCheckItem'])
         ->where('id', '[0-9]+');
    
    // เสร็จสิ้นการตรวจเช็ค
    Route::post('/{id}/complete', [DispenserCheckController::class, 'completeCheck'])
         ->where('id', '[0-9]+');
    
    // อัพโหลดรูปภาพการตรวจเช็ค
    Route::post('/upload-image', [DispenserCheckController::class, 'uploadImage']);
    
    // ลบรูปภาพ
    Route::delete('/images/{id}', [DispenserCheckController::class, 'deleteImage'])
         ->where('id', '[0-9]+');
    
    // Export PDF รายงานการตรวจเช็ค
    Route::get('/{workId}/{stationId}/export-pdf', [DispenserCheckController::class, 'exportPDF'])
         ->where(['workId' => '[0-9]+', 'stationId' => '[0-9]+']);
});