<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\DashboardController;

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

    // Meal Planning Shared Route
    Route::get('/dashboard/meal-planning', function () {
        return view('dashboards.meal-planning');
    })->name('dashboard.meal_planning');

    // Dashboard Super Admin
    Route::middleware(['role:super admin'])->group(function () {
        Route::get('/dashboard/superadmin', [DashboardController::class, 'superadmin'])->name('dashboard.superadmin');

        Route::get('/dashboard/superadmin/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/dashboard/superadmin/suppliers', function () { return view('dashboards.placeholder'); })->name('suppliers.index');
        Route::get('/dashboard/superadmin/analytics', function () { return view('dashboards.placeholder'); })->name('analytics.index');
        Route::get('/dashboard/superadmin/settings', function () { return view('dashboards.placeholder'); })->name('settings.index');
    });

    // Dashboard Dapur
    Route::middleware(['role:dapur'])->group(function () {
        Route::get('/dashboard/dapur', [DashboardController::class, 'dapur'])->name('dashboard.dapur');

        Route::get('/dashboard/dapur/inventory', function () { return view('dashboards.placeholder'); })->name('inventory.index');
        Route::get('/dashboard/dapur/deliveries', function () { return view('dashboards.placeholder'); })->name('deliveries.index');
    });

    // Dashboard Sekolah
    Route::middleware(['role:sekolah'])->group(function () {
        Route::get('/dashboard/sekolah', [DashboardController::class, 'sekolah'])->name('dashboard.sekolah');

        Route::get('/dashboard/sekolah/monitoring', function () {
            return view('dashboards.sekolah.monitoring');
        })->name('dashboard.sekolah.monitoring');
    });
});

Route::resource('resep', ResepController::class);
Route::resource('menu', MenuController::class);