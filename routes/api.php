<?php

use App\Http\Controllers\{AuthController, CityController, CountryController, NotificationController, ProductController, RegionController, TestingController};
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

        // Google OAuth
        Route::get('/google', [AuthController::class, 'redirectToGoogle']);
        Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);
    });

    Route::prefix('/city')->group(function () {
        Route::get('/list', [CityController::class, 'list']);
        Route::get('/{city}', [CityController::class, 'show']);
        Route::delete('/{city}', [CityController::class, 'delete']);
        Route::get('/deleted', [CityController::class, 'getDeleted']);
    });

    // Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/region')->group(function () {
        Route::get('/list', [RegionController::class, 'list']);
        Route::get('/{region}', [RegionController::class, 'show']);
    });
    // });

    Route::prefix('/country')->group(function () {
        Route::get('/list', [CountryController::class, 'list']);
        Route::get('/{country}', [CountryController::class, 'show']);
    });

    Route::prefix('/product')->group(function () {
        Route::get('/list', [ProductController::class, 'list']);
        Route::get('/{slug}', [ProductController::class, 'show']);
    });
});
