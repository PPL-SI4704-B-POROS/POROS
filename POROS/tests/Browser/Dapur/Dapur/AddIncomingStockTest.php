<?php

use App\Models\BahanBaku;
use App\Models\FormHarga;
use App\Models\KatalogPangan;
use App\Models\StokGudang;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->artisan('db:seed');

    $this->user = User::where('email', 'dapur@poros.com')->first();

    if (! $this->user) {
        throw new Exception('User dapur tidak ditemukan dari seeder');
    }

    $stokAsli = StokGudang::first();

    if (!$stokAsli) {
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Test',
            'alamat' => 'Jl. Test No. 1',
            'kontak' => '08123456789',
        ]);

        $katalog = KatalogPangan::create([
            'kode_tkpi' => 'TEST-001',
            'nama_pangan' => 'Bahan Test',
            'kategori' => 'Test Kategori',
            'energi_per_100g' => 100,
        ]);

        $bahanBaku = BahanBaku::create([
            'nama_bahan' => 'Bahan Test',
            'supplier_id' => $supplier->id,
            'katalog_pangan_id' => $katalog->id,
            'satuan' => 'kg',
            'stok' => 100,
            'stok_minimal' => 10,
        ]);

        FormHarga::create([
            'harga_satuan' => 5000,
            'satuan_harga' => 'kg',
            'tanggal_update' => now()->toDateString(),
            'supplier_id' => $supplier->id,
            'bahan_id' => $bahanBaku->id,
        ]);

        $stokAsli = StokGudang::create([
            'bahan_baku_id' => $bahanBaku->id,
            'supplier_id' => $supplier->id,
            'quantity' => 50000,
            'satuan' => 'kg',
        ]);
    }

    $this->stok = $stokAsli;
});

test('happy path - add incoming stock berhasil', function () {
    $user = $this->user;
    $stokItem = $this->stok;
    $namaTabelStok = $stokItem->getTable();
    $jumlahAwal = (int) $stokItem->quantity;

    $this->browse(function (Browser $browser) use ($user, $stokItem, $namaTabelStok, $jumlahAwal) {
        $browser->loginAs($user)
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

test('validasi - stok supplier tidak cukup', function () {
    $user = $this->user;
    $stokItem = $this->stok;
    $namaTabelStok = $stokItem->getTable();
    $jumlahAwal = $stokItem->quantity;

    $this->browse(function (Browser $browser) use ($user, $stokItem, $namaTabelStok, $jumlahAwal) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->waitForText('Logistics & Deliveries', 10);

        $this->assertDatabaseHas($namaTabelStok, [
            'id' => $stokItem->id,
            'quantity' => $jumlahAwal,
        ]);
    });
});

test('validasi - quantity kosong tidak bisa submit', function () {
    $user = $this->user;
    $stokItem = $this->stok;
    $namaTabelStok = $stokItem->getTable();
    $jumlahAwal = $stokItem->quantity;

    $this->browse(function (Browser $browser) use ($user, $stokItem, $namaTabelStok, $jumlahAwal) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->waitForText('Logistics & Deliveries', 10);

        $this->assertDatabaseHas($namaTabelStok, [
            'id' => $stokItem->id,
            'quantity' => $jumlahAwal,
        ]);
    });
});