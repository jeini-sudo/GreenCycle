<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CareController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\TreeController;
use Illuminate\Support\Facades\Route;

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

    Route::get('inventory', [InventoryController::class, 'index'])
        ->name('inventory.index');

    Route::post('inventory/{item}/use', [InventoryController::class, 'useItem'])
        ->name('inventory.use');

});
