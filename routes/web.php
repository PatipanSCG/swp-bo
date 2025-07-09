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

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/profile', 'ProfileController@index')->name('profile');
Route::put('/profile', 'ProfileController@update')->name('profile.update');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/data', [EmployeeController::class, 'getData'])->name('employees.data');

Route::get('/stations', [StationController::class, 'index'])->name('stations.index');
Route::get('/stations/data', [StationController::class, 'getData'])->name('stations.data');
// เพิ่ม route สำหรับการจัดการหัวจ่ายของสถานี
Route::get('stations/{station}/dispensers', [DispenserController::class, 'index'])->name('stations.dispensers');
Route::get('stations/{station}/dispensers/data', [DispenserController::class, 'getData'])->name('stations.dispensers');
