<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/users', [AdminController::class, 'users']);
Route::get('/admin/transactions', [AdminController::class, 'transactions']);
