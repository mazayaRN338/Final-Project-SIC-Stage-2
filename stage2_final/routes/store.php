<?php

use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/store', [StoreController::class, 'index']);
Route::post('/store/buy/{id}', [StoreController::class, 'buyWithFace']);
