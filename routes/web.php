<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    ProfileController,
    EmployeeController,
    StationController,
    DispenserController,
    AdminController,
    LocationController,
    CustomerController,
    ServiceRateController,
    TravelController,
    SysTechnicianTeamController,
    ComunicataeController,
    ContactController,
    WorkController
};

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
    });

    Route::view('/about', 'about')->name('about');

    // 👨‍💼 Employees
    Route::prefix('employees')->controller(EmployeeController::class)->group(function () {
        Route::get('/', 'index')->name('employees.index');
        Route::get('/data', 'getData')->name('employees.data');
        Route::get('/getDatainsystem', 'getDatainsystem')->name('employees.getDatainsystem');
        Route::post('/addemployee/{empCode}', 'add')->name('employees.add');
        Route::get('/teams', 'teams')->name('employees.teams');
        Route::get('/getdataforen', 'getdataforen')->name('employees.getdataforen');
        
    });

    // 🏪 Stations
    Route::prefix('stations')->controller(StationController::class)->group(function () {
        Route::get('/', 'index')->name('stations.index');
        Route::get('/data', 'getData')->name('stations.data');
        Route::get('/{station}/detail', 'detail')->name('stations.detail');
        Route::post('/store', 'store')->name('stations.store');
        Route::put('/{id}', 'update')->name('stations.update');
        Route::get('/model', 'model')->name('stations.model');
        Route::get('/countNozzles/{stationid}', 'countNozzles')->name('stations.countNozzles');
        Route::post('/updatelatlong', 'updatelatlong')->name('stations.updatelatlong');

    });

    // 🧯 Dispensers
    Route::prefix('stations')->controller(DispenserController::class)->group(function () {
        Route::get('/{station}/dispensers', 'index')->name('stations.dispensers');
        Route::get('/{station}/dispensers/data', 'getData')->name('stations.dispensers.data');
        Route::get('/dispensers/{dispensers}/nozzle/data', 'getDataNozzles');
    });

    // 🧭 Admin Overview
    Route::prefix('admin/stations')->controller(AdminController::class)->group(function () {
        Route::get('/', 'stationOverview')->name('admin.stations');
        Route::get('/data', 'getdata_stations')->name('admin.stations.data');
    });

    // 🌍 Location API
    Route::prefix('api')->controller(LocationController::class)->group(function () {
        Route::get('/provinces/{province}/districts', 'getDistricts');
        Route::get('/districts/{district}/subdistricts', 'getSubdistricts');
    });

    // 👤 Contacts
    Route::prefix('contacts')->controller(ContactController::class)->group(function () {
        Route::post('/store', 'store')->name('contacts.store');
        Route::get('/getdata', 'getData');
        Route::delete('/delete/{id}', 'destroy');
    });

    // 👥 Customers
    Route::prefix('customers')->controller(CustomerController::class)->group(function () {
        Route::post('/', 'store')->name('customers.store');
        Route::put('/{id}', 'update')->name('customers.update');
    });

    // 💰 คำนวณต่าง ๆ
    Route::get('/calculate-distance/{lat}/{lng}', [StationController::class, 'calculateDistance']);
    Route::get('/calculate-Locationdetail/{lat}/{lng}', [LocationController::class, 'getLocationDetail']);
    Route::get('/calculate-charge/{nozzleCount}', [ServiceRateController::class, 'calculateCharge']);
    Route::get('/calculate-travel/{distance}', [TravelController::class, 'calculate']);

    // 🛠️ ทีมช่าง
    Route::prefix('teams')->controller(SysTechnicianTeamController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::post('/add-member', 'addMember');
        Route::delete('/remove-member/{id}', 'removeMember');
        Route::get('/{id}/members', 'teamMembers');
        Route::get('/{id}/teamData', 'teamData');
        Route::delete('/{id}', 'destroy');
    });

    // 📝 งานที่ได้รับมอบหมาย
    Route::prefix('works')->controller(WorkController::class)->group(function () {
        Route::get('/', 'index')->name('works.index');
    });

    // 📞 บันทึกการติดต่อ
    Route::prefix('comunicatae')->controller(ComunicataeController::class)->group(function () {
        Route::get('/{stationID}', 'index');
        Route::get('/show/{id}', 'show');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
        Route::get('/types', 'getTypes');
        
    });

    Route::prefix('works')->controller(WorkController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{WorkID}/detail', 'detail');
        Route::view('/', 'work.index');
    });
    Route::get('/api/works', [WorkController::class, 'index'])->name('works.index');
});
