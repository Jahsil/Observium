<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ObserviumController;
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


Route::get('/parseCsv' , [CsvController::class , 'parseCsv']);
Route::get('/get_devices' , [ObserviumController::class , 'getDevices']);
Route::post("/sign_up", [AuthController::class , 'signUp']);
Route::post("/sign_in", [AuthController::class , 'signIn']);


Route::prefix('v1')->group(function (){
    Route::middleware(['jwt.verify'])->group(function () {
        Route::get('/index' , [ObserviumController::class , 'index']);
        Route::post('/add_device' , [ObserviumController::class , 'store']);
        Route::get('/show_device/{name}' , [ObserviumController::class , 'show']);
        Route::put('/update_device/{name}', [ObserviumController::class , 'update']);
        Route::delete('/delete_device/{name}' , [ObserviumController::class , 'destroy']);
        Route::post('/sign_out', [ObserviumController::class , 'signOut']);

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
