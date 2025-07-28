<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\DispenserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LocationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
// ->middleware('check.menu:home');

Route::get('/profile', 'ProfileController@index')->name('profile');
// ->middleware('check.menu:profile');
Route::put('/profile', 'ProfileController@update')->name('profile.update');
// ->middleware('check.menu:profile.update');

Route::get('/about', function () {
    return view('about');
})->name('about');
// ->middleware('check.menu:about');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
// ->middleware('check.menu:employees.index');
Route::get('/employees/data', [EmployeeController::class, 'getData'])->name('employees.data');
// ->middleware('check.menu:employees.data');
Route::post('/employees/addemployee/{empCode}', [EmployeeController::class, 'add'])->name('employees.add');
// ->middleware('check.menu:employees.add');


Route::get('/stations', [StationController::class, 'index'])->name('stations.index');
// ->middleware('check.menu:stations.index');
Route::get('/stations/data', [StationController::class, 'getData'])->name('stations.data');
// ->middleware('check.menu:stations.data');
Route::get('/stations/{station}/detail', [StationController::class, 'detail'])->name('stations.detail');
// ->middleware('check.menu:stations.data');
Route::post('/stations/store', [StationController::class, 'store'])->name('stations.store');



// เพิ่ม route สำหรับการจัดการหัวจ่ายของสถานี
Route::get('stations/{station}/dispensers', [DispenserController::class, 'index'])->name('stations.dispensers');
// ->middleware('check.menu:stations.dispensers');
Route::get('stations/{station}/dispensers/data', [DispenserController::class, 'getData'])->name('stations.dispensers.data');
// ->middleware('check.menu:stations.dispensers.data');

Route::get('/admin/stations', [AdminController::class, 'stationOverview'])->name('admin.stations');
Route::get('/admin/stations/data', [AdminController::class, 'getdata_stations'])->name('admin.stations.data');

Route::get('/api/provinces/{province}/districts', [LocationController::class, 'getDistricts']);
Route::get('/api/districts/{district}/subdistricts', [LocationController::class, 'getSubdistricts']);
