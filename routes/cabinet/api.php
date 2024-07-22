<?php

use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\Reviews\ReviewsController;
use App\Http\Controllers\Cabinet\AuksionController;
use App\Http\Controllers\Cabinet\CarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('cabinet')->group(function () {
    Route::prefix('auksion')->group(function () {
        Route::get('',[AuksionController::class, 'auksion']);
        Route::post('',[AuksionController::class, 'auksionPost']);
        Route::post('edit/{id}',[AuksionController::class, 'auksionEdit']);
        Route::post('delete/{id}',[AuksionController::class, 'auksionDelete']);
    });
    Route::prefix('car')->middleware('auth:sanctum')->group(function () {
        Route::get('',[CarController::class, 'car']);
        Route::post('',[CarController::class, 'carPost']);
        Route::post('edit/{id}',[CarController::class, 'carEdit']);
        Route::post('delete/{id}',[CarController::class, 'carDelete']);
        Route::post('image/add/{id}',[CarController::class, 'carImageAdd']);
        Route::post('image/delete/{id}',[CarController::class, 'carImageDelete']);
    });
    
});