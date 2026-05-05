<?php

use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\KatalogPangan;
use App\Models\StokGudang;

uses(DatabaseMigrations::class);

beforeEach(function () {

    // 🔥 Seed database (INI KUNCI BIAR SAMA KAYAK TEMEN LU)
    $this->artisan('db:seed');

    // ✅ ambil user dari seeder
    $this->user = User::where('email', 'dapur@poros.com')->first();

    if (!$this->user) {
        throw new Exception('User dapur tidak ditemukan dari seeder');
    }

    // ✅ ambil supplier (kalau ada dari seed)
    $supplier = Supplier::first();

    if (!$supplier) {
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Test',
            'alamat'        => 'Jl. Test No. 1',
            'kontak'        => '08123456789',
        ]);
    }

    // ✅ buat katalog test
    $katalog = KatalogPangan::create([
        'kode_tkpi'       => 'TEST-001',
        'nama_pangan'     => 'Bahan Test',
        'kategori'        => 'Test Kategori',
        'energi_per_100g' => 100,
    ]);

    // ✅ bahan baku
    $bahanBaku = BahanBaku::create([
        'nama_bahan'        => 'Bahan Test',
        'katalog_pangan_id' => $katalog->id,
        'supplier_id'       => $supplier->id,
        'satuan'            => 'kg',
        'stok'              => 100,
        'stok_minimal'      => 10,
    ]);

    // ✅ stok gudang
    $this->stok = StokGudang::create([
        'bahan_baku_id' => $bahanBaku->id,
        'supplier_id'   => $supplier->id,
        'quantity'      => 50,
        'satuan'        => 'kg',
    ]);
});

test('adjust - tambah stok berhasil', function () {

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->user)
            ->visit('/dashboard/dapur/deliveries')
            ->assertSee('Logistics & Deliveries')
            ->click('@adjust-btn-' . $this->stok->id)
            ->waitFor('#adjustModal', 5)
            ->radio('adjustment_type', 'add')
            ->type('input[name="adjustment_amount"]', '10')
            ->type('input[name="reason"]', 'Test tambah stok')
            ->press('Simpan Koreksi')
            ->waitForText('Stok berhasil dikoreksi', 5)
            ->assertSee('Stok berhasil dikoreksi');
    });
});

test('adjust - kurangi stok berhasil', function () {

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->user)
            ->visit('/dashboard/dapur/deliveries')
            ->assertSee('Logistics & Deliveries')
            ->click('@adjust-btn-' . $this->stok->id)
            ->waitFor('#adjustModal', 5)
            ->radio('adjustment_type', 'subtract')
            ->type('input[name="adjustment_amount"]', '5')
            ->type('input[name="reason"]', 'Test kurangi stok')
            ->press('Simpan Koreksi')
            ->waitForText('Stok berhasil dikoreksi', 5)
            ->assertSee('Stok berhasil dikoreksi');
    });
});

test('adjust - reason kosong tidak bisa submit', function () {

    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->user)
            ->visit('/dashboard/dapur/deliveries')
            ->assertSee('Logistics & Deliveries')
            ->click('@adjust-btn-' . $this->stok->id)
            ->waitFor('#adjustModal', 5)
            ->radio('adjustment_type', 'add')
            ->type('input[name="adjustment_amount"]', '10')
            ->press('Simpan Koreksi')
            ->pause(1000)
            ->assertSee('Logistics & Deliveries')
            ->assertDontSee('Stok berhasil dikoreksi');
    });
});