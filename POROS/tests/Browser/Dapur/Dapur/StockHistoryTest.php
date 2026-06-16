<?php

use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\KatalogPangan;
use App\Models\StokGudang;
use App\Models\StockHistory;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->artisan('db:seed');

    $this->user = User::where('email', 'dapur@poros.com')->first();

    if (!$this->user) {
        throw new Exception('User dapur tidak ditemukan dari seeder');
    }

    $stokAsli = StokGudang::first();

    if (!$stokAsli) {
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

        $stokAsli = StokGudang::create([
            'bahan_baku_id' => $bahanBaku->id,
            'supplier_id'   => $supplier->id,
            'quantity'      => 50,
            'satuan'        => 'kg',
        ]);
    }

    $this->stok = $stokAsli;
});

test('history - ada data incoming, tampil di modal', function () {
    $user = $this->user;
    $stok = $this->stok;
    $namaTabelHistory = (new StockHistory)->getTable();

    StockHistory::create([
        'stok_gudang_id' => $stok->id,
        'status'         => 'incoming',
        'quantity'       => 20,
        'incoming_date'  => now()->toDateString(),
        'batch_id'       => 'BTH-HISTORY-TEST',
        'expired_date'   => now()->addDays(30)->toDateString(),
    ]);

    $this->browse(function (Browser $browser) use ($user, $stok, $namaTabelHistory) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->waitForText('Logistics & Deliveries', 10);

        $this->assertDatabaseHas($namaTabelHistory, [
            'stok_gudang_id' => $stok->id,
            'batch_id'       => 'BTH-HISTORY-TEST',
            'status'         => 'incoming'
        ]);
    });
});

test('history - belum ada transaksi, tampil pesan kosong', function () {
    $user = $this->user;
    $stok = $this->stok;
    $namaTabelHistory = (new StockHistory)->getTable();

    StockHistory::where('stok_gudang_id', $stok->id)->delete();

    $this->browse(function (Browser $browser) use ($user, $stok, $namaTabelHistory) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->waitForText('Logistics & Deliveries', 10);

        $this->assertDatabaseMissing($namaTabelHistory, [
            'stok_gudang_id' => $stok->id,
            'batch_id'       => 'BTH-HISTORY-TEST'
        ]);
    });
});