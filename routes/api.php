<?php

use App\Http\Controllers\{AuthController, BaseController, BookingController, CityController, CountryController, CrawlingController, NotificationController, PaymentController, ProductController, RegionController, SearchController, TestingController, UserController};
use App\Http\Middleware\CheckAPIKey;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware(CheckAPIKey::class)->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('check.token.expiration');

        // Google OAuth
        Route::get('/google', [AuthController::class, 'redirectToGoogle']);
        Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);

        Route::post('/save-profile', [AuthController::class, 'saveProfile'])->middleware('check.token.expiration');
    });

    Route::prefix('/city')->group(function () {
        Route::get('/list', [CityController::class, 'list']);
        Route::get('/{city}', [CityController::class, 'show']);
        Route::delete('/{city}', [CityController::class, 'delete']);
        Route::get('/deleted', [CityController::class, 'getDeleted']);
    });

    Route::prefix('/region')->group(function () {
        Route::get('/list', [RegionController::class, 'list']);
        Route::get('/{region}', [RegionController::class, 'show']);
    });

    Route::prefix('/country')->group(function () {
        Route::get('/list', [CountryController::class, 'list']);
        Route::get('/{country}', [CountryController::class, 'show']);
    });

    Route::prefix('/product')->group(function () {
        Route::get('/test-redis', [ProductController::class, 'testRedis']);
        Route::get('/list', [ProductController::class, 'list']);
        Route::get('/popular-destination', [ProductController::class, 'popularDestination']);
        Route::get('/explore-now', [ProductController::class, 'exploreNow']);
        Route::get('/{slug}', [ProductController::class, 'show']);
        Route::get('/{slug}/available-date', [ProductController::class, 'availableDate']);
        Route::get('/{slug}/available-period', [ProductController::class, 'availablePeriod']);
        Route::get('/{slug}/room-type', [ProductController::class, 'roomType']);
    });

    Route::prefix('/search')->group(function () {
        Route::get('/', [SearchController::class, 'globalSearch']);
    });

    Route::prefix('/base')->group(function () {
        Route::get('/languages', [BaseController::class, 'languages']);
        Route::get('/currencies', [BaseController::class, 'currencies']);
    });

    Route::prefix('/crawling')->group(function () {
        Route::get('/currency-rates', [CrawlingController::class, 'getCurrencyRates']);
    });

    Route::prefix('/debug')->group(function () {
        Route::get('/test-invoice/{id}', [TestingController::class, 'testInvoice']);
    });

    Route::middleware(['check.token.expiration'])->group(function () {
        Route::prefix('/user')->group(function () {
            Route::get('/profile', [UserController::class, 'profile']);
            Route::post('/upload-profile-picture', [UserController::class, 'uploadProfilePicture']);
            Route::post('/review-product/{slug}', [ProductController::class]);
            Route::post('/save-profile', [UserController::class, 'saveProfile']);
        });

        Route::prefix('/booking')->group(function () {
            Route::post('/', [BookingController::class, 'store']);
            Route::get('/history', [BookingController::class, 'history']);
            Route::get('/{id}', [BookingController::class, 'show']);
            Route::post('{id}/cancel', [BookingController::class, 'cancel']);
            Route::post('/{id}/update', [BookingController::class, 'update']);
            Route::post('/{id}/submit-review', [BookingController::class, 'submitReview']);
        });

        Route::prefix('/payment')->group(function () {
            // List Payment Methods
            Route::get('/list', [PaymentController::class, 'list']);

            Route::get('/{transaction}/status', [PaymentController::class, 'getTransactionStatus']);

            Route::post('/set-payment-method', [PaymentController::class, 'setPaymentMethod']);

            // Gopay Payment
            Route::post('/gopay/{transaction}', [PaymentController::class, 'payWithGopay']);
            Route::match(['get', 'post'], '/midtrans/callback', [PaymentController::class, 'handleCallbackGopay'])->name('midtrans.callback')
                ->withoutMiddleware(['check.token.expiration', CheckAPIKey::class]);

            Route::post('/{transaction}', [PaymentController::class, 'pay']);
            Route::post('/midtrans/notification', [PaymentController::class, 'handleNotification']);
        });
    });
});
