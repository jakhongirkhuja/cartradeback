<?php

use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\Cabinet\BookingController;
use App\Http\Controllers\Api\Cabinet\UserController;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\Reviews\ReviewsController;
use App\Http\Controllers\Cabinet\AuksionController;
use App\Http\Controllers\Cabinet\CarController;
use App\Http\Middleware\adminRoleMiddleware;
use App\Http\Middleware\ownerRoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('cabinet')->group(function () {
    Route::prefix('auksion')->group(function () {
        Route::get('', [AuksionController::class, 'auksion']);

        Route::post('bet', [AuksionController::class, 'auksionBet'])->middleware('auth:sanctum');
        Route::post('buy', [AuksionController::class, 'auksionBuy'])->middleware('auth:sanctum');
        Route::post('change-status', [AuksionController::class, 'auksionChangeStatus'])->middleware([adminRoleMiddleware::class, 'auth:sanctum']);
        Route::get('lastPrice/{id}', [AuksionController::class, 'auksionlastPrice']);
        // Route::post('',[AuksionController::class, 'auksionPost']);
        // Route::post('edit/{id}',[AuksionController::class, 'auksionEdit']);
        // Route::post('delete/{id}',[AuksionController::class, 'auksionDelete']);
    });
    Route::prefix('car')->middleware('auth:sanctum')->group(function () {
        Route::get('', [CarController::class, 'car']);

        Route::post('change-status', [CarController::class, 'carChangeStatus'])->middleware([adminRoleMiddleware::class]);

        Route::post('', [CarController::class, 'carPost']);
        Route::get('bet', [CarController::class, 'carBet']);
        Route::post('edit/{id}', [CarController::class, 'carEdit']);
        Route::post('delete/{id}', [CarController::class, 'carDelete']);
        Route::post('image/add/{id}', [CarController::class, 'carImageAdd']);
        Route::post('image/delete/{id}', [CarController::class, 'carImageDelete']);
        Route::post('/checks/{id}/type/{type}', [CarController::class, 'checksSave']);
        Route::post('bookings', [BookingController::class, 'store']);
        Route::post('bookings/changeStatus', [BookingController::class, 'changeStatus'])->middleware([ownerRoleMiddleware::class]);
        Route::post('bookings/uploadImageSignature', [BookingController::class, 'uploadImageSignature']);
        Route::get('bookings', [BookingController::class, 'userBookings']);
        Route::post('bookings/create', [BookingController::class, 'createBooking']);
        Route::post('bookings/steps', [BookingController::class, 'bookingSteps']);
    });
    Route::post('change-tarif/{id}', [IndexController::class, 'changeTarif'])->middleware(adminRoleMiddleware::class);
    Route::get('tarifs', [IndexController::class, 'tarifs']);
    Route::prefix('reviews')->middleware('auth:sanctum')->group(function () {
        Route::post('change-status/{id}', [ReviewsController::class, 'reviewsChangeStatus'])->middleware(adminRoleMiddleware::class);
        Route::post('delete/{id}', [ReviewsController::class, 'reviewsDelete'])->middleware(adminRoleMiddleware::class);
        Route::get('list', [ReviewsController::class, 'reviewsList'])->middleware(adminRoleMiddleware::class);
    });

    Route::prefix('user')->controller(UserController::class)->middleware('auth:sanctum')->group(function () {
        Route::post('password-change', 'passwordChange');
        Route::post('info-change', 'infoChange');
        Route::post('info-change-passport', 'infoChangePassport');

        Route::get('user-transactions', 'userTransactions');
        Route::post('user-fill-balance', 'userFillBalance');
        Route::post('user-tarif-choose', 'userTarifChoose');

        Route::middleware(adminRoleMiddleware::class)->group(function () {
            // Route::post('phoneNumber-change','phoneNumberChange');
            Route::post('user-change-info', 'userInfoChangeAdmin');
            Route::get('user-list', 'userListChange');

            Route::post('user-remove/{id}', 'userRemove');
        });

        // Route::get('',[CarController::class, 'car']);
        // Route::post('',[CarController::class, 'carPost']);
        // Route::post('edit/{id}',[CarController::class, 'carEdit']);
        // Route::post('delete/{id}',[CarController::class, 'carDelete']);

    });
});
