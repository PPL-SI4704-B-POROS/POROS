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

test('adjust - tambah stok berhasil', function () {
    $user   = User::where('email', 'dapur@poros.com')->first();
    $stokId = StokGudang::latest()->first()->id;

    $this->browse(function (Browser $browser) use ($user, $stokId) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->assertSee('Logistics & Deliveries')
            ->click('@adjust-btn-' . $stokId)
            ->waitFor('#adjustModal', 3)
            ->radio('adjustment_type', 'add')
            ->type('input[name="adjustment_amount"]', '10')
            ->type('input[name="reason"]', 'Test tambah stok')
            ->press('Simpan Koreksi')
            ->waitForText('Stok berhasil dikoreksi', 5)
            ->assertSee('Stok berhasil dikoreksi');
    });
});

test('adjust - kurangi stok berhasil', function () {
    $user   = User::where('email', 'dapur@poros.com')->first();
    $stokId = StokGudang::latest()->first()->id;

    $this->browse(function (Browser $browser) use ($user, $stokId) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->assertSee('Logistics & Deliveries')
            ->click('@adjust-btn-' . $stokId)
            ->waitFor('#adjustModal', 3)
            ->radio('adjustment_type', 'subtract')
            ->type('input[name="adjustment_amount"]', '5')
            ->type('input[name="reason"]', 'Test kurangi stok')
            ->press('Simpan Koreksi')
            ->waitForText('Stok berhasil dikoreksi', 5)
            ->assertSee('Stok berhasil dikoreksi');
    });
});

test('adjust - reason kosong tidak bisa submit', function () {
    $user   = User::where('email', 'dapur@poros.com')->first();
    $stokId = StokGudang::latest()->first()->id;

    $this->browse(function (Browser $browser) use ($user, $stokId) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->assertSee('Logistics & Deliveries')
            ->click('@adjust-btn-' . $stokId)
            ->waitFor('#adjustModal', 3)
            ->radio('adjustment_type', 'add')
            ->type('input[name="adjustment_amount"]', '10')
            ->press('Simpan Koreksi')
            ->pause(1000)
            ->assertSee('Logistics & Deliveries')
            ->assertDontSee('Stok berhasil dikoreksi');
    });
});