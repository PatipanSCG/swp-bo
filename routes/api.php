<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FuelTypeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NozzleController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\WorkInspectionRecordController;
use App\Http\Controllers\NavCustomerController;

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
Route::post('/inspection-records', [WorkInspectionRecordController::class, 'store']);
Route::get('/export-nozzles/{stationid}', [WorkInspectionRecordController::class, 'exportLabelByStation']);
Route::get('/Navcustomers/{vatid}', [NavCustomerController::class, 'index'])->name('customers.index');
Route::get('/sales-quote', function () {
    return view('SQ.template');
});