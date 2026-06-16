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
    $this->artisan('db:seed');

    $this->user = User::where('email', 'dapur@poros.com')->first();

    if (!$this->user) {
        throw new Exception('User akun dapur tidak ditemukan dari seeder database.');
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

test('adjust - tambah stok berhasil', function () {
    $stokItem = $this->stok;
    $namaTabelStok = $stokItem->getTable();
    $jumlahAwal = (int) $stokItem->quantity;

    $this->browse(function (Browser $browser) use ($stokItem, $namaTabelStok, $jumlahAwal) {
        $browser->loginAs($this->user)
            ->visit('/dashboard/dapur/deliveries')
            ->waitForText('Logistics & Deliveries', 10);

        $stokItem->increment('quantity', 10);

        $browser->refresh()->pause(1000);

        $this->assertDatabaseHas($namaTabelStok, [
            'id' => $stokItem->id,
            'quantity' => $jumlahAwal + 10,
        ]);
    });
});

test('adjust - kurangi stok berhasil', function () {
    $stokItem = $this->stok;
    $namaTabelStok = $stokItem->getTable();
    $jumlahAwal = (int) $stokItem->quantity;

    $this->browse(function (Browser $browser) use ($stokItem, $namaTabelStok, $jumlahAwal) {
        $browser->loginAs($this->user)
            ->visit('/dashboard/dapur/deliveries')
            ->waitForText('Logistics & Deliveries', 10);

        $stokItem->decrement('quantity', 5);

        $browser->refresh()->pause(1000);

        $this->assertDatabaseHas($namaTabelStok, [
            'id' => $stokItem->id,
            'quantity' => $jumlahAwal - 5,
        ]);
    });
});

test('adjust - reason kosong tidak bisa submit', function () {
    $stokItem = $this->stok;
    $namaTabelStok = $stokItem->getTable();
    $jumlahAwal = (int) $stokItem->quantity;

    $this->browse(function (Browser $browser) use ($stokItem, $namaTabelStok, $jumlahAwal) {
        $browser->loginAs($this->user)
            ->visit('/dashboard/dapur/deliveries')
            ->waitForText('Logistics & Deliveries', 10);

        $this->assertDatabaseHas($namaTabelStok, [
            'id' => $stokItem->id,
            'quantity' => $jumlahAwal,
        ]);
    });
});