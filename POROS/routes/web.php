<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\StudentController;
=======

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StokController;

use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Dapur\MenuController;
use App\Http\Controllers\Dapur\ProduksiHarianController;
use App\Http\Controllers\Dapur\BahanBakusController;
use App\Http\Controllers\Dapur\SuppliersController;

>>>>>>> 678912f6c89aed1657d54a4d08c7e5d71ce204db
Route::get('/', function () {
    return redirect()->route('login');
});

<<<<<<< HEAD

Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
=======
// ================= LOGIN =================
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================= AUTH =================
Route::middleware(['auth'])->group(function () {

    // ================= PROFILE =================
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // ================= DASHBOARD =================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // =========================================================
    // SUPER ADMIN
    // =========================================================
    Route::middleware(['role:super admin'])->group(function () {

        Route::get('/dashboard/superadmin/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/dashboard/superadmin/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/dashboard/superadmin/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/dashboard/superadmin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::post('/dashboard/superadmin/siswas', [UserController::class, 'storeSiswa'])->name('siswas.store');
        Route::put('/dashboard/superadmin/siswas/{siswa}', [UserController::class, 'updateSiswa'])->name('siswas.update');
        Route::delete('/dashboard/superadmin/siswas/{siswa}', [UserController::class, 'destroySiswa'])->name('siswas.destroy');

        Route::get('/dashboard/superadmin/suppliers', function () {
            return view('dashboards.superadmin.suppliers');
        })->name('superadmin.suppliers.index');

        Route::get('/dashboard/superadmin/analytics', function () {
            return view('dashboards.superadmin.analytics');
        })->name('analytics.index');

        Route::get('/dashboard/superadmin/settings', function () {
            return view('dashboards.superadmin.settings');
        })->name('settings.index');

    });

    // =========================================================
    // DAPUR
    // =========================================================
    Route::middleware(['role:dapur'])->group(function () {

        // ================= MEAL PLANNING =================
        Route::get('/dashboard/dapur/meal-planning', [MenuController::class, 'index'])->name('dashboard.meal_planning');

        // ================= PRODUKSI =================
        Route::post('/dashboard/schedule', [ProduksiHarianController::class, 'store'])->name('schedule.store');
        Route::put('/dashboard/schedule/{id}', [ProduksiHarianController::class, 'update'])->name('schedule.update');
        Route::delete('/dashboard/schedule/{id}', [ProduksiHarianController::class, 'destroy'])->name('schedule.destroy');

        // ================= INVENTORY =================
        Route::get('/dashboard/dapur/inventory', [BahanBakusController::class, 'index'])->name('inventory.index');

        // ================= DELIVERIES =================
        Route::get('/dashboard/dapur/deliveries',                      [StokController::class, 'index'])->name('stocks.index');
        Route::post('/dashboard/dapur/deliveries/add-item',            [StokController::class, 'addItem'])->name('stocks.addItem');
        Route::post('/dashboard/dapur/deliveries/{id}/incoming',       [StokController::class, 'addIncoming'])->name('stocks.addIncoming');
        Route::post('/dashboard/dapur/deliveries/{id}/adjust',         [StokController::class, 'adjustStock'])->name('stocks.adjust');
        Route::delete('/dashboard/dapur/deliveries/{id}',              [StokController::class, 'destroy'])->name('stocks.destroy');
        Route::get('/dashboard/dapur/deliveries/{id}/history',         [StokController::class, 'history'])->name('stocks.history');

        // ================= CRUD BAHAN BAKU =================
        Route::post('/bahan-bakus', [BahanBakusController::class, 'store'])->name('bahan-bakus.store');
        Route::put('/bahan-bakus/{id}', [BahanBakusController::class, 'update'])->name('bahan-bakus.update');
        Route::get('/bahan-bakus/{id}/edit', [BahanBakusController::class, 'edit'])->name('bahan-bakus.edit');
        Route::delete('/bahan-bakus/{id}', [BahanBakusController::class, 'destroy'])->name('bahan-bakus.destroy');

        // ================= CRUD SUPPLIERS =================
        Route::get('/suppliers', [SuppliersController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers', [SuppliersController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{id}', [SuppliersController::class, 'update'])->name('suppliers.update');
        Route::get('/suppliers/{id}/edit', [SuppliersController::class, 'edit'])->name('suppliers.edit');
        Route::delete('/suppliers/{id}', [SuppliersController::class, 'destroy'])->name('suppliers.destroy');

        // ================= MENU =================
        Route::resource('menu', MenuController::class)->except(['index']);

    });

    // =========================================================
    // SEKOLAH
    // =========================================================
    Route::middleware(['role:sekolah'])->group(function () {
        Route::get('/dashboard/sekolah/monitoring', function () {
            return view('dashboards.sekolah.monitoring');
        })->name('dashboard.sekolah.monitoring');
    });

});
>>>>>>> 678912f6c89aed1657d54a4d08c7e5d71ce204db
