<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StokController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/stok', function () {
    return view('stok');
});

Route::post('/stok', [StokController::class, 'store']);