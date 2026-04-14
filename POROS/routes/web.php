<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
Route::get('/', function () {
    return view('welcome');
});


Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
