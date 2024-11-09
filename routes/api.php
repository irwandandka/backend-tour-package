<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\NotificationController;
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

    Route::prefix('/country')->group(function () {
        Route::get('/list', [CountryController::class, 'list']);
        Route::get('/{country}', [CountryController::class, 'show']);
    });

    Route::prefix('/region')->group(function () {
        Route::get('/list', [RegionController::class, 'list']);
        Route::get('/{region}', [RegionController::class, 'show']);
    });

    Route::prefix('/city')->group(function () {
        Route::get('/deleted', [CityController::class, 'getDeleted']);
        Route::get('/{city}', [CityController::class, 'show']);
        Route::get('/list', [CityController::class, 'list']);
        Route::delete('/{city}', [CityController::class, 'delete']);
    });

    Route::prefix('/notification')->group(function () {
        Route::post('/telegram', [NotificationController::class, 'notifyUser']);
    });
});
