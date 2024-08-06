<?php

use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\Reviews\ReviewsController;
use App\Http\Controllers\Payment\PaymeController;
use App\Http\Middleware\PaymeMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/user/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json('success');
})->middleware('auth:sanctum');

Route::post('/register',[UserAuthController::class, 'register']);
Route::post('/register/send-sms',[UserAuthController::class, 'registerSendSms']);
Route::post('/login',[UserAuthController::class, 'login']);
Route::post('/reset-password',[UserAuthController::class, 'resetPassword']);



Route::get('/reviews',[ReviewsController::class, 'reviews']);
Route::post('/reviews',[ReviewsController::class, 'reviewsPost']);
Route::post('/reviews/delete/{id}',[ReviewsController::class, 'reviewsDelete']);

Route::get('/loadMark',[IndexController::class, 'loadMark']);
Route::get('/loadModel/{mark_id}',[IndexController::class, 'loadModel']);
Route::get('/filters',[IndexController::class, 'filters']);
Route::get('/auksion',[IndexController::class, 'auksion']);
Route::post('/auksion-bet',[IndexController::class, 'auksionBet']);
Route::post('/enquery/{type}',[IndexController::class, 'enquery']);


Route::post('/payme', [PaymeController::class, 'index'])->middleware(PaymeMiddleware::class);