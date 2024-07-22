<?php

use App\Http\Controllers\IndexController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/',[IndexController::class, 'index']);
