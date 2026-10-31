<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CareController;
use App\Http\Controllers\Api\V1\TreeController;
use App\Http\Controllers\Api\V1\TreeHealthController;

/* 
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/


Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register');
Route::post('login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login');


Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('user', [AuthController::class, 'me'])->name('user');

    //Aquí abajo ir agregando el resto de rutas
Route::apiResource('trees', TreeController::class);

Route::post('trees/{tree}/care', [CareController::class, 'store'])->name('trees.care');



});
