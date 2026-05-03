<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StokController;

use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\PengumumanController;
use App\Http\Controllers\Dapur\MenuController;
use App\Http\Controllers\Dapur\ProduksiHarianController;
use App\Http\Controllers\Dapur\BahanBakusController;
use App\Http\Controllers\Dapur\SuppliersController;

Route::get('/', function () {
    return redirect()->route('login');
});

// ================= LOGIN =================
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================= STOK =================
Route::get('/stok', function () {
    return view('stok');
});

Route::post('/stok', [StokController::class, 'store']);

// ================= AUTH =================
Route::middleware(['auth'])->group(function () {

    // Profile Routes
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Shared Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // ===== PENGUMUMAN - Semua user bisa lihat =====
    Route::get('/dashboard/superadmin/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');

    // ================= SUPER ADMIN =================
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
        })->name('suppliers.index');

        Route::get('/dashboard/superadmin/analytics', function () {
            return view('dashboards.superadmin.analytics');
        })->name('analytics.index');

        Route::get('/dashboard/superadmin/settings', function () {
            return view('dashboards.superadmin.settings');
        })->name('settings.index');

        // ===== PENGUMUMAN - Hanya Super Admin yang bisa buat & edit =====
        Route::post('/dashboard/superadmin/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::get('/dashboard/superadmin/pengumuman/{pengumuman}/edit', [PengumumanController::class, 'edit'])->name('pengumuman.edit');
        Route::put('/dashboard/superadmin/pengumuman/{pengumuman}', [PengumumanController::class, 'update'])->name('pengumuman.update');

    });

    // ================= DAPUR =================
    Route::middleware(['role:dapur'])->group(function () {

        Route::get('/dashboard/dapur/meal-planning', [MenuController::class, 'index'])->name('dashboard.meal_planning');

        Route::post('/dashboard/schedule', [ProduksiHarianController::class, 'store'])->name('schedule.store');
        Route::put('/dashboard/schedule/{id}', [ProduksiHarianController::class, 'update'])->name('schedule.update');
        Route::delete('/dashboard/schedule/{id}', [ProduksiHarianController::class, 'destroy'])->name('schedule.destroy');

        // Inventory
        Route::get('/dashboard/dapur/inventory', [BahanBakusController::class, 'index'])->name('inventory.index');

        // CRUD Bahan Baku
        Route::post('/bahan-bakus', [BahanBakusController::class, 'store'])->name('bahan-bakus.store');
        Route::put('/bahan-bakus/{id}', [BahanBakusController::class, 'update'])->name('bahan-bakus.update');
        Route::get('/bahan-bakus/{id}/edit', [BahanBakusController::class, 'edit'])->name('bahan-bakus.edit');
        Route::delete('/bahan-bakus/{id}', [BahanBakusController::class, 'destroy'])->name('bahan-bakus.destroy');

        // CRUD Suppliers
        Route::get('/suppliers', [SuppliersController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers', [SuppliersController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{id}', [SuppliersController::class, 'update'])->name('suppliers.update');
        Route::get('/suppliers/{id}/edit', [SuppliersController::class, 'edit'])->name('suppliers.edit');
        Route::delete('/suppliers/{id}', [SuppliersController::class, 'destroy'])->name('suppliers.destroy');

        Route::resource('menu', MenuController::class)->except(['index']);

        Route::get('/dashboard/dapur/deliveries', function () {
            return view('dashboards.dapur.deliveries');
        })->name('deliveries.index');

    });

    // ================= SEKOLAH =================
    Route::middleware(['role:sekolah'])->group(function () {
        Route::get('/dashboard/sekolah/monitoring', function () {
            return view('dashboards.sekolah.monitoring');
        })->name('dashboard.sekolah.monitoring');

        // Siswa Management
        Route::get('/dashboard/sekolah/siswas', [\App\Http\Controllers\Sekolah\SiswaController::class, 'index'])->name('sekolah.siswas.index');
        Route::post('/dashboard/sekolah/siswas', [\App\Http\Controllers\Sekolah\SiswaController::class, 'store'])->name('sekolah.siswas.store');
        Route::put('/dashboard/sekolah/siswas/{siswa}', [\App\Http\Controllers\Sekolah\SiswaController::class, 'update'])->name('sekolah.siswas.update');
        Route::delete('/dashboard/sekolah/siswas/{siswa}', [\App\Http\Controllers\Sekolah\SiswaController::class, 'destroy'])->name('sekolah.siswas.destroy');
        Route::post('/dashboard/sekolah/siswas/{id}/antropometri', [\App\Http\Controllers\Sekolah\SiswaController::class, 'storeAntropometri'])->name('sekolah.siswas.antropometri.store');
    });

});