<?php

use App\Http\Controllers\api\FarmController;
use App\Http\Controllers\api\FarmProductController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\SalesController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// register & login
Route::prefix('v1')->middleware(['throttle:10,1'])->group(function (){
Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
});

Route::prefix('v1')->middleware(['auth:sanctum','throttle:10,1'])->group(function (){
Route::post('logout',[AuthController::class,'logout']);
Route::get('get_me',[AuthController::class,'getMe']);
});

// farm management
Route::prefix('v1')->middleware(['auth:sanctum','throttle:10,1'])->group(function (){
Route::get('farm',[FarmController::class,'show']);
Route::put('farm/{farm}',[FarmController::class,'unsuspendFarm']);
Route::get('farm/admins',[FarmController::class,'index']);
Route::post('farm',[FarmController::class,'create']);
Route::patch('farm/{farm}',[FarmController::class,'update']);
Route::delete('farm/{farm}',[FarmController::class,'destroy']);
});

// product management
Route::prefix('v1') ->middleware(['auth:sanctum', 'throttle:10,1']) ->group(function () {
Route::post('products', [ProductController::class, 'create']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);
Route::put('products/{product}', [ProductController::class, 'update']);
Route::delete('products/{product}', [ProductController::class, 'destroy']);
});

// farm product management
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {
Route::post('farm-products', [FarmProductController::class, 'create']);
Route::get('farm-products', [FarmProductController::class, 'index']);
Route::get('farm-products/{id}', [FarmProductController::class, 'show']);
Route::put('farm-products/{id}', [FarmProductController::class, 'update']);
Route::delete('farm-products/{id}', [FarmProductController::class, 'destroy']);
});


// user  management
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {
Route::post('users', [UserController::class, 'create']);
Route::get('users',[UserController::class,'index']);
Route::put('users/{user}',[UserController::class,'updateUserRole']);
Route::delete('users/{user}',[UserController::class,'destroy']);
});

// sales management
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {
Route::post('sales',[SalesController::class,'create']);
Route::get('sales',[SalesController::class,'index']);

});