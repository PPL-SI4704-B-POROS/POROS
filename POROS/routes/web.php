<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StokController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

// ================= LOGIN =================
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================= STOK (PUNYA LU) =================
Route::middleware(['auth'])->group(function () {
    Route::get('/stok', function () {
        return view('stok');
    });

    Route::post('/stok', [StokController::class, 'store']);
});

// ================= DASHBOARD =================
Route::middleware(['auth'])->group(function () {

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/dashboard/meal-planning', function () {
        return view('dashboards.meal-planning');
    })->name('dashboard.meal_planning');

    Route::middleware(['role:super admin'])->group(function () {
        Route::get('/dashboard/superadmin', [DashboardController::class, 'superadmin'])->name('dashboard.superadmin');

        Route::get('/dashboard/superadmin/users', [UserController::class, 'index'])->name('users.index');
    });

    Route::middleware(['role:dapur'])->group(function () {
        Route::get('/dashboard/dapur', [DashboardController::class, 'dapur'])->name('dashboard.dapur');
    });

    Route::middleware(['role:sekolah'])->group(function () {
        Route::get('/dashboard/sekolah', [DashboardController::class, 'sekolah'])->name('dashboard.sekolah');
    });
});

// ================= RESOURCE =================
Route::resource('resep', ResepController::class);
Route::resource('menu', MenuController::class);