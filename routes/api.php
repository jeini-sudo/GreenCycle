<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CareController;
use App\Http\Controllers\Api\V1\TreeController;





Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register');
Route::post('login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login');


Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('user', [AuthController::class, 'me'])->name('user');


    Route::apiResource('trees', TreeController::class)
        ->only(['index', 'store', 'show']);


    Route::post('trees/{tree}/care', [CareController::class, 'store'])
        ->name('trees.care');




});
