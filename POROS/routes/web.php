<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Dapur\MenuController;
use App\Http\Controllers\Dapur\ProduksiHarianController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Profile Routes
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Shared Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::middleware(['role:super admin'])->group(function () {
        Route::get('/dashboard/superadmin/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/dashboard/superadmin/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/dashboard/superadmin/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/dashboard/superadmin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/dashboard/superadmin/siswas', [UserController::class, 'storeSiswa'])->name('siswas.store');
        Route::put('/dashboard/superadmin/siswas/{siswa}', [UserController::class, 'updateSiswa'])->name('siswas.update');
        Route::delete('/dashboard/superadmin/siswas/{siswa}', [UserController::class, 'destroySiswa'])->name('siswas.destroy');
        Route::get('/dashboard/superadmin/suppliers', function () { return view('dashboards.superadmin.suppliers'); })->name('suppliers.index');
        Route::get('/dashboard/superadmin/analytics', function () { return view('dashboards.superadmin.analytics'); })->name('analytics.index');
        Route::get('/dashboard/superadmin/settings', function () { return view('dashboards.superadmin.settings'); })->name('settings.index');
    });

    Route::middleware(['role:dapur'])->group(function () {
        Route::get('/dashboard/dapur/meal-planning', [MenuController::class, 'index'])->name('dashboard.meal_planning');
        Route::post('/dashboard/schedule', [ProduksiHarianController::class, 'store'])->name('schedule.store');
        Route::put('/dashboard/schedule/{id}', [ProduksiHarianController::class, 'update'])->name('schedule.update');
        Route::delete('/dashboard/schedule/{id}', [ProduksiHarianController::class, 'destroy'])->name('schedule.destroy');
        
        Route::resource('menu', MenuController::class)->except(['index']);

        Route::get('/dashboard/dapur/inventory', function () { return view('dashboards.dapur.inventory'); })->name('inventory.index');
        Route::get('/dashboard/dapur/deliveries', function () { return view('dashboards.dapur.deliveries'); })->name('deliveries.index');
    });

    Route::middleware(['role:sekolah'])->group(function () {
        Route::get('/dashboard/sekolah/monitoring', function () {
            return view('dashboards.sekolah.monitoring');
        })->name('dashboard.sekolah.monitoring');
    });
});