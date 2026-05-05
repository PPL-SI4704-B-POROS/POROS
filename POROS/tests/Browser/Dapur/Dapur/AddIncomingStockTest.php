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

    // 🔥 reset DB + seed (biar konsisten semua laptop)
    $this->artisan('db:seed');

    // ✅ pastikan user dapur ada
    $this->user = User::where('email', 'dapur@poros.com')->first();

    if (!$this->user) {
        throw new Exception('User dapur tidak ditemukan dari seeder');
    }

    // ✅ ambil supplier dari seeder (atau fallback)
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
        'quantity'      => 5,
        'satuan'        => 'kg',
    ]);
});

test('happy path - add incoming stock berhasil', function () {

    $user = $this->user;
    $stokId = $this->stok->id;

    $this->browse(function (Browser $browser) use ($user, $stokId) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->assertSee('Logistics & Deliveries')
            ->click('@stock-btn-' . $stokId)
            ->waitFor('#incomingModal', 3)
            ->type('input[name="quantity"]', '10')
            ->type('input[name="incoming_date"]', date('Y-m-d'))
            ->type('input[name="batch_id"]', 'BTH-TEST')
            ->type('input[name="expired_date"]', date('Y-m-d', strtotime('+30 days')))
            ->press('Add Stock')
            ->waitForText('Stok berhasil diperbarui', 5)
            ->assertSee('Stok berhasil diperbarui');
    });
});

test('validasi - stok supplier tidak cukup', function () {

    $user = $this->user;
    $stokId = $this->stok->id;

    $this->browse(function (Browser $browser) use ($user, $stokId) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->click('@stock-btn-' . $stokId)
            ->waitFor('#incomingModal', 3)
            ->type('input[name="quantity"]', '9999')
            ->type('input[name="incoming_date"]', date('Y-m-d'))
            ->press('Add Stock')
            ->waitForText('Stok supplier tidak cukup', 5)
            ->assertSee('Stok supplier tidak cukup');
    });
});

test('validasi - quantity kosong tidak bisa submit', function () {

    $user = $this->user;
    $stokId = $this->stok->id;

    $this->browse(function (Browser $browser) use ($user, $stokId) {
        $browser->loginAs($user)
            ->visit('/dashboard/dapur/deliveries')
            ->click('@stock-btn-' . $stokId)
            ->waitFor('#incomingModal', 3)
            ->type('input[name="incoming_date"]', date('Y-m-d'))
            ->press('Add Stock')
            ->pause(1000)
            ->assertSee('Logistics & Deliveries')
            ->assertDontSee('Stok berhasil diperbarui');
    });
});