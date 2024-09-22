<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('/region')->group(function () {
            Route::get('/', [RegionController::class, 'list']);
            Route::get('/{region}', [RegionController::class, 'show']);
        });
    });
});
