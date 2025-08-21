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
    WorkController,
    QuotationController,
    PromotionController,
    WamVerifyRequestController
};

Route::get('/', function () {
    // return view('welcome');
     return redirect()->route('login');
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
        Route::get('/', 'index')->name('works.index');
        Route::get('/{WorkID}/detail', 'detail');
        Route::view('/', 'work.index');
    });

    Route::prefix('quotations')->controller(QuotationController::class)->group(function () {
        Route::get('{quotation}/pdf', 'downloadPDF');
        Route::post('/store', 'store');
    });
    Route::prefix('promotions')->name('promotions.')->middleware(['auth'])->group(function () {
    
    // หน้าแสดงรายการหลัก (Web)
    Route::get('/', [PromotionController::class, 'index'])->name('index');
    
    // API Routes สำหรับ AJAX Operations
    Route::get('/datatable', [PromotionController::class, 'datatable'])->name('datatable');
    Route::post('/store', [PromotionController::class, 'store'])->name('store');
    Route::get('/{id}/show', [PromotionController::class, 'show'])->name('show');
    Route::put('/{id}/update', [PromotionController::class, 'update'])->name('update');
    Route::delete('/{id}/destroy', [PromotionController::class, 'destroy'])->name('destroy');
    
    // Bulk Operations
    Route::post('/bulk-delete', [PromotionController::class, 'bulkDelete'])->name('bulk-delete');
    
    // Status Operations
    Route::patch('/{id}/toggle-status', [PromotionController::class, 'toggleStatus'])->name('toggle-status');
});

    Route::get('/api/works', [WorkController::class, 'index'])->name('works.apiindex');
});
Route::get('/test/wam/login', [\App\Http\Controllers\WamAuthTestController::class, 'index']);
Route::get('/test-pdf', [App\Http\Controllers\QuotationController::class, 'testPDF']);
Route::prefix('wam-verify-requests')->group(function () {
    Route::get('/', [WamVerifyRequestController::class, 'index']); // Sync + Get data
    Route::get('/database', [WamVerifyRequestController::class, 'getFromDatabase']); // Get from DB only
    Route::get('/statistics', [WamVerifyRequestController::class, 'getStatistics']); // Get stats
    Route::post('/sync', [WamVerifyRequestController::class, 'syncData']); // Manual sync
    Route::get('/{id}', [WamVerifyRequestController::class, 'show']); // Get single record
    Route::patch('/{id}/status', [WamVerifyRequestController::class, 'updateStatus']); // Update status
});
Route::get('/dispenser-check/{workId}/{stationId}', 'DispenserCheckController@index')
     ->where(['workId' => '[0-9]+', 'stationId' => '[0-9]+']);