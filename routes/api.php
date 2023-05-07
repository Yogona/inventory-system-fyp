<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InstrumentController;
use App\Http\Controllers\InstrumentsRequestController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post("login", [AuthController::class, "login"]);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get("roles", [RoleController::class, "index"]);

    //Users
    Route::controller(UserController::class)->prefix("users")->group(function(){
        Route::post("create","createUser");
        Route::post("upload", "uploadUsers");
        Route::put("update/{user_id}", "updateUser");
        Route::delete("delete/{user+id}", "deleteUser");
        Route::get("get-crs/{records}", "listClassRepresentatives");
    });

    //Departments
    Route::controller(DepartmentController::class)->prefix("departments")->group(function(){
        Route::post("create", "createDepartment");
        Route::put("update/{depart_id}", "udpate");
        Route::delete("delete/{depart_id}", "destroy");
        Route::get("", "index");
    });

    //Stores
    Route::controller(StoreController::class)->prefix("stores")->group(function(){
        Route::post("create", "store");
        Route::get("{records}", "index");
        Route::put("update/{store_id}", "update");
    });

    //Instruments
    Route::controller(InstrumentController::class)->prefix("instruments")->group(function(){
        Route::post("create", "create");
        Route::get("", "index");
        Route::put("update/{instrument_id}", "update");
        Route::delete("delete/{instrument_id}", "destroy");
    });

    //Instruments requests
    Route::controller(InstrumentsRequestController::class)->prefix("requests")->group(function(){
        Route::post("place", "store");
        Route::get("list", "index");
    });
});
