<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('store.index');
});

require __DIR__.'/user.php';
require __DIR__.'/store.php';
require __DIR__.'/admin.php';