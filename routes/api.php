<?php

use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\Reviews\ReviewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register',[UserAuthController::class, 'register']);
Route::post('/register/send-sms',[UserAuthController::class, 'registerSendSms']);
Route::post('/login',[UserAuthController::class, 'login']);
Route::post('/reset-password',[UserAuthController::class, 'resetPassword']);



Route::get('/reviews',[ReviewsController::class, 'reviews']);
Route::post('/reviews',[ReviewsController::class, 'reviewsPost']);
Route::post('/reviews/delete/{id}',[ReviewsController::class, 'reviewsDelete']);

Route::get('/filters',[IndexController::class, 'filters']);
Route::get('/auksion',[IndexController::class, 'auksion']);
Route::post('/auksion-bet',[IndexController::class, 'auksionBet']);