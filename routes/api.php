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
    Route::controller(AuthController::class)->group(function(){
        
        Route::post("logout", "logout");
    });
    

    Route::get("roles", [RoleController::class, "index"]);

    //Users
    Route::controller(UserController::class)->prefix("users")->group(function(){
        Route::post("create","createUser");
        Route::post("upload", "uploadUsers");
        Route::put("update/{user_id}", "updateUser");
        Route::delete("delete/{user_id}", "deleteUser");
        Route::get("role/{type}/records/{records}", "index");
        Route::get("role/{type}/search/{query}/records/{records}", "searchIndex");
        Route::get("role/{type}", "list");
        Route::patch("change-username/{user_id}", "changeUsername");
        Route::patch("change-email/{user_id}", "changeEmail");
        Route::patch("change-phone/{user_id}", "changePhone");
    });

    //Departments
    Route::controller(DepartmentController::class)->prefix("departments")->group(function(){
        Route::post("create", "createDepartment");
        Route::put("update/{depart_id}", "update");
        Route::delete("delete/{depart_id}", "destroy");
        Route::get("", "index");
        Route::get("search/{query}", "searchDepartments");
    });

    //Stores
    Route::controller(StoreController::class)->prefix("stores")->group(function(){
        Route::post("create", "store");
        Route::get("{records}", "index");
        Route::get("", "list");
        Route::get("search/{query}", "searchList");
        Route::put("update/{store_id}", "update");
        
    });

    //Instruments
    Route::controller(InstrumentController::class)->prefix("instruments")->group(function(){
        Route::post("create", "store");
        Route::get("store/{store_id}/records/{records}", "index");
        Route::get("store/{store_id}/search/{query}/records/{records}", "searchIndex");
        Route::get("store/{store_id}", "list");
        Route::get("store/{store_id}/search/{query}", "searchList");
        Route::put("update/{instrument_id}", "update");
        Route::delete("delete/{instrument_id}", "destroy");
    });

    //Instruments requests
    Route::controller(InstrumentsRequestController::class)->prefix("requests")->group(function(){
        Route::post("place", "store");
        Route::get("records/{records}", "index");
        Route::put("update/{request_id}", "update");
        Route::patch("allocate/{request_id}", "allocate");
        Route::patch("deallocate/{request_id}", "deallocate");
        Route::delete("delete/{request_id}", "destroy");
    });
});
