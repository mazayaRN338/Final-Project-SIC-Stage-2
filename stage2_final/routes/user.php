<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/user/register', [UserController::class, 'registerForm']);
Route::post('/user/register', [UserController::class, 'register']);
Route::get('/user/scan', [UserController::class, 'scanFace']);
