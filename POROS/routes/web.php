<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\LaporanMasalahController;

use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\PengumumanController;
use App\Http\Controllers\SuperAdmin\AnalyticsController;
use App\Http\Controllers\SuperAdmin\LaporanMasalahController as AdminLaporanMasalahController;
use App\Http\Controllers\Dapur\BahanBakusController;
use App\Http\Controllers\Dapur\MenuController;
use App\Http\Controllers\Dapur\ProduksiHarianController;
use App\Http\Controllers\Dapur\SuppliersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Sekolah\SiswaController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SuperAdmin\AnalyticsController;
use App\Http\Controllers\SuperAdmin\PengirimanController;
use App\Http\Controllers\SuperAdmin\PengumumanController;
use App\Http\Controllers\SuperAdmin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

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
        })->name('superadmin.suppliers.index');

        Route::get('/dashboard/superadmin/analytics', function () {
            return view('dashboards.superadmin.analytics');
        })->name('analytics.index');

        Route::get('/dashboard/superadmin/settings', function () {
            return view('dashboards.superadmin.settings');
        })->name('settings.index');

        // ===== PENGUMUMAN - Hanya Super Admin yang bisa buat, edit & hapus =====
        Route::post('/dashboard/superadmin/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::get('/dashboard/superadmin/pengumuman/{pengumuman}/edit', [PengumumanController::class, 'edit'])->name('pengumuman.edit');
        Route::put('/dashboard/superadmin/pengumuman/{pengumuman}', [PengumumanController::class, 'update'])->name('pengumuman.update');
        Route::delete('/dashboard/superadmin/pengumuman/{pengumuman}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');

        Route::get('/dashboard/superadmin/analytics', [AnalyticsController::class, 'index'])->name('superadmin.analytics');

        // ================= LAPORAN MASALAH =================
        Route::get('/dashboard/superadmin/laporan-masalah', [AdminLaporanMasalahController::class, 'index'])->name('superadmin.laporan-masalah.index');
        Route::patch('/dashboard/superadmin/laporan-masalah/{laporanMasalah}/status', [AdminLaporanMasalahController::class, 'updateStatus'])->name('superadmin.laporan-masalah.updateStatus');
        Route::delete('/dashboard/superadmin/laporan-masalah/{laporanMasalah}', [AdminLaporanMasalahController::class, 'destroy'])->name('superadmin.laporan-masalah.destroy');
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
        Route::post('/dashboard/schedule/{id}/update-status', [ProduksiHarianController::class, 'updateStatus'])->name('schedule.updateStatus');

        // ================= INVENTORY =================
        Route::get('/dashboard/dapur/inventory', [BahanBakusController::class, 'index'])->name('inventory.index');

        // ================= DELIVERIES =================
        Route::get('/dashboard/dapur/deliveries', [StokController::class, 'index'])->name('stocks.index');
        Route::post('/dashboard/dapur/deliveries/add-item', [StokController::class, 'addItem'])->name('stocks.addItem');
        Route::post('/dashboard/dapur/deliveries/{id}/incoming', [StokController::class, 'addIncoming'])->name('stocks.addIncoming');
        Route::post('/dashboard/dapur/deliveries/{id}/adjust', [StokController::class, 'adjustStock'])->name('stocks.adjust');
        Route::delete('/dashboard/dapur/deliveries/{id}', [StokController::class, 'destroy'])->name('stocks.destroy');
        Route::get('/dashboard/dapur/deliveries/{id}/history', [StokController::class, 'history'])->name('stocks.history');

        // ===== PENGIRIMAN (Logistics & Deliveries) =====
        Route::get('/dashboard/dapur/logistics-deliveries', [PengirimanController::class, 'index'])->name('dapur.deliveries.index');
        Route::post('/dashboard/dapur/logistics-deliveries/{id}/status', [PengirimanController::class, 'updateStatus'])->name('dapur.deliveries.updateStatus');
        Route::post('/dashboard/dapur/logistics-deliveries/{id}/handover', [PengirimanController::class, 'updateHandover'])->name('dapur.deliveries.updateHandover');

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

        // ================= LAPORAN MASALAH =================
        Route::get('/dashboard/dapur/laporan-masalah', [LaporanMasalahController::class, 'index'])->name('dapur.laporan-masalah.index');
        Route::post('/dashboard/dapur/laporan-masalah', [LaporanMasalahController::class, 'store'])->name('dapur.laporan-masalah.store');
        Route::delete('/dashboard/dapur/laporan-masalah/{laporanMasalah}', [LaporanMasalahController::class, 'destroy'])->name('dapur.laporan-masalah.destroy');
    });

    // =========================================================
    // SEKOLAH
    // =========================================================
    Route::middleware(['role:sekolah'])->prefix('dashboard/sekolah')->name('sekolah.')->group(function () {

        Route::get('/monitoring', function () {
            return view('dashboards.sekolah.monitoring');
        })->name('monitoring');

        // ================= SISWA =================
        Route::get('/siswas', [SiswaController::class, 'index'])->name('siswas.index');
    Route::middleware(['auth', 'role:sekolah'])->prefix('dashboard/sekolah')->name('sekolah.')->group(function () {

        // FIXED: Diarahkan ke SiswaController, bukan UserController
        Route::get('/siswas', [SiswaController::class, 'index'])->name('siswas.index');

        // Route pendukung lainnya (Store, Update, Delete)
        Route::post('/siswas', [SiswaController::class, 'store'])->name('siswas.store');
        Route::post('/siswas/import', [SiswaController::class, 'import'])->name('siswas.import');
        Route::put('/siswas/{siswa}', [SiswaController::class, 'update'])->name('siswas.update');
        Route::delete('/siswas/bulk-destroy', [SiswaController::class, 'bulkDestroy'])->name('siswas.bulk-destroy');
        Route::delete('/siswas/{siswa}', [SiswaController::class, 'destroy'])->name('siswas.destroy');
        Route::post('/siswas/{siswa}/antropometri', [SiswaController::class, 'storeAntropometri'])->name('siswas.antropometri');

        // ================= RIWAYAT KESEHATAN =================
        // Riwayat Kesehatan
        Route::get('/riwayat-kesehatan', [SiswaController::class, 'riwayatKesehatan'])->name('riwayat-kesehatan.index');
        Route::post('/riwayat-kesehatan/import', [SiswaController::class, 'importAntropometri'])->name('riwayat-kesehatan.import');
        Route::delete('/riwayat-kesehatan/bulk-destroy', [SiswaController::class, 'bulkDestroyAntropometri'])->name('riwayat-kesehatan.bulk-destroy');
        Route::delete('/riwayat-kesehatan/{antropometri}', [SiswaController::class, 'destroyAntropometri'])->name('riwayat-kesehatan.destroy');

        // ================= LAPORAN MASALAH =================
        Route::get('/laporan-masalah', [LaporanMasalahController::class, 'index'])->name('laporan-masalah.index');
        Route::post('/laporan-masalah', [LaporanMasalahController::class, 'store'])->name('laporan-masalah.store');
        Route::delete('/laporan-masalah/{laporanMasalah}', [LaporanMasalahController::class, 'destroy'])->name('laporan-masalah.destroy');
    });

});