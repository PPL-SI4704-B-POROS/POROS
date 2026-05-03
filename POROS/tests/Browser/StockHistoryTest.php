<?php

use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\KatalogPangan;
use App\Models\StokGudang;
use App\Models\StockHistory;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    StockHistory::whereHas('stokGudang', fn($q) => $q->whereHas('bahanBaku', fn($q2) => $q2->where('nama_bahan', 'Bahan Test')))->forceDelete();
    StokGudang::whereHas('bahanBaku', fn($q) => $q->where('nama_bahan', 'Bahan Test'))->forceDelete();
    BahanBaku::withTrashed()->where('nama_bahan', 'Bahan Test')->forceDelete();
    KatalogPangan::withTrashed()->where('kode_tkpi', 'TEST-001')->forceDelete();
    Supplier::withTrashed()->where('nama_supplier', 'Supplier Test')->forceDelete();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $supplier = Supplier::create([
        'nama_supplier' => 'Supplier Test',
        'alamat'        => 'Jl. Test No. 1',
        'kontak'        => '08123456789',
    ]);

    $katalog = KatalogPangan::create([
        'kode_tkpi'       => 'TEST-001',
        'nama_pangan'     => 'Bahan Test',
        'kategori'        => 'Test Kategori',
        'energi_per_100g' => 100,
    ]);

    $bahanBaku = BahanBaku::create([
        'nama_bahan'        => 'Bahan Test',
        'katalog_pangan_id' => $katalog->id,
        'supplier_id'       => $supplier->id,
        'satuan'            => 'kg',
        'stok'              => 100,
        'stok_minimal'      => 10,
    ]);

    StokGudang::create([
        'bahan_baku_id' => $bahanBaku->id,
        'supplier_id'   => $supplier->id,
        'quantity'      => 50,
        'satuan'        => 'kg',
    ]);
});

test('history - ada data incoming, tampil di modal', function () {
    $user   = User::where('email', 'dapur@poros.com')->first();
    $stok   = StokGudang::latest()->first();

    // Seed history langsung
    StockHistory::create([
        'stok_gudang_id' => $stok->id,
        'status'         => 'incoming',
        'quantity'       => 20,
        'incoming_date'  => now()->toDateString(),
        'batch_id'       => 'BTH-HISTORY-TEST',
        'expired_date'   => now()->addDays(30)->toDateString(),
    ]);

    $this->browse(function (Browser $browser) use ($user, $stok) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->assertSee('Logistics & Deliveries')
            ->click('@history-btn-' . $stok->id)
            ->waitFor('#historyModal', 3)
            ->waitForText('incoming', 5)
            ->assertSee('BTH-HISTORY-TEST');
    });
});

test('history - belum ada transaksi, tampil pesan kosong', function () {
    $user = User::where('email', 'dapur@poros.com')->first();
    $stok = StokGudang::latest()->first();

    $this->browse(function (Browser $browser) use ($user, $stok) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->assertSee('Logistics & Deliveries')
            ->click('@history-btn-' . $stok->id)
            ->waitFor('#historyModal', 3)
            ->waitForText('Belum ada history untuk item ini.', 5);
    });
});