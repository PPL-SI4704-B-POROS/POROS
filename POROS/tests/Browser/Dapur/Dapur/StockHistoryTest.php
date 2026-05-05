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

    // 🔥 reset DB + seed (biar konsisten semua laptop)
    $this->artisan('db:seed');

    // ✅ pastikan user dapur ada
    $this->user = User::where('email', 'dapur@poros.com')->first();

    if (!$this->user) {
        throw new Exception('User dapur tidak ditemukan dari seeder');
    }

    // ✅ ambil supplier dari seeder (fallback kalau gak ada)
    $supplier = Supplier::first();

    if (!$supplier) {
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Test',
            'alamat'        => 'Jl. Test No. 1',
            'kontak'        => '08123456789',
        ]);
    }

    // ✅ buat data test
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

    $this->stok = StokGudang::create([
        'bahan_baku_id' => $bahanBaku->id,
        'supplier_id'   => $supplier->id,
        'quantity'      => 50,
        'satuan'        => 'kg',
    ]);
});

test('history - ada data incoming, tampil di modal', function () {

    $user = $this->user;
    $stok = $this->stok;

    // seed history
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

    $user = $this->user;
    $stok = $this->stok;

    $this->browse(function (Browser $browser) use ($user, $stok) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->assertSee('Logistics & Deliveries')
            ->click('@history-btn-' . $stok->id)
            ->waitFor('#historyModal', 3)
            ->waitForText('Belum ada history untuk item ini.', 5);
    });
});