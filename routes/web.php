<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\DispenserController;

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

Route::get('/home', 'HomeController@index')->name('home')->middleware('check.menu:home');

Route::get('/profile', 'ProfileController@index')->name('profile')->middleware('check.menu:profile');
Route::put('/profile', 'ProfileController@update')->name('profile.update')->middleware('check.menu:profile.update');

Route::get('/about', function () {
    return view('about');
})->name('about')->middleware('check.menu:about');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index')->middleware('check.menu:employees.index');
Route::get('/employees/data', [EmployeeController::class, 'getData'])->name('employees.data')->middleware('check.menu:employees.data');

Route::get('/stations', [StationController::class, 'index'])->name('stations.index')->middleware('check.menu:stations.index');
Route::get('/stations/data', [StationController::class, 'getData'])->name('stations.data')->middleware('check.menu:stations.data');

// เพิ่ม route สำหรับการจัดการหัวจ่ายของสถานี
Route::get('stations/{station}/dispensers', [DispenserController::class, 'index'])->name('stations.dispensers')->middleware('check.menu:stations.dispensers');
Route::get('stations/{station}/dispensers/data', [DispenserController::class, 'getData'])->name('stations.dispensers.data')->middleware('check.menu:stations.dispensers.data');
