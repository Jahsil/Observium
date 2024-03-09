<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ObserviumController;
use App\Http\Controllers\FakeStoreController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\ObserviumLogs; 


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// inventory lost devices 
Route::get('/get_lost_devices', [CsvController::class , 'parseLostDevicesCsv']);
Route::get('/get_null_tally', [CsvController::class , 'getNullTallyBatches']);



// Observium Device logs 

Route::get('/observium_device_logs', [ObserviumLogs::class, 'getLogs']);
Route::post('/observium_device_logs', [ObserviumLogs::class, 'storeLog']);
Route::get('/observium_device_logs/{id}', [ObserviumLogs::class, 'showLog']);
Route::put('/observium_device_logs/{id}', [ObserviumLogs::class, 'updateLog']);
Route::delete('/observium_device_logs/{id}', [ObserviumLogs::class, 'destroyLog']);
Route::get('/get_devices/{device_name}', [ObserviumLogs::class , 'getLogWithDeviceName']);



Route::get('/index' , [ObserviumController::class , 'index']);
Route::post('/add_device' , [ObserviumController::class , 'store']);
Route::get('/show_device/{name}' , [ObserviumController::class , 'show']);
Route::put('/update_device/{name}', [ObserviumController::class , 'update']);
Route::delete('/delete_device/{name}' , [ObserviumController::class , 'destroy']);




Route::get('/parseCsv' , [CsvController::class , 'parseCsv']);
Route::get('/get_devices' , [ObserviumController::class , 'getDevices']);
Route::post("/sign_up", [AuthController::class , 'signUp']);
Route::post("/sign_in", [AuthController::class , 'signIn']);
Route::get('/get_items', [FakeStoreController::class , 'index']);
Route::get('/users_list', [AuthController::class , 'usersList']);
Route::post('/shelf' , [ShelfController::class , 'getShelf']);
Route::get('/get_products' , [ShelfController::class , 'getProducts']);

Route::post('/add_site', [ObserviumController::class , 'addSite']);
Route::get('/get_site', [ObserviumController::class , 'getSites']);
Route::delete('/delete_site/{id}', [ObserviumController::class , 'destroySite']);
Route::put('/update_site/{id}', [ObserviumController::class , 'updateSite']);


Route::prefix('v1')->group(function (){ 
    Route::middleware(['jwt.verify'])->group(function () {
        
        Route::post('/sign_out', [AuthController::class , 'signOut']);

        
    });    
});


// Route::prefix('v1')->group(function () {
//     Route::middleware(['jwt.verify'])->group(function () {
//         Route::get('/index', [ObserviumController::class, 'index']);
//         Route::post('/add_device', [ObserviumController::class, 'store']);
//         Route::get('/show_device/{name}', [ObserviumController::class, 'show']);
//         Route::put('/update_device/{name}', [ObserviumController::class, 'update']);
//         Route::delete('/delete_device/{name}', [ObserviumController::class, 'destroy']);
//     });
// });
