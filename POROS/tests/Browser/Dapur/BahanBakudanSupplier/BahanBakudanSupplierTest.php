<?php

namespace Tests\Browser\Dapur\BahanBakudanSupplier;

use App\Models\User;
use App\Models\Supplier;
use App\Models\BahanBaku;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;

class BahanBakudanSupplierTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** 1. POSITIVE: Create Supplier & Bahan Baku */
    #[Test]
    public function test_positive_create_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->type('nama_supplier', 'PT Makmur Jaya')
                    ->type('kontak', '0812345678')
                    ->type('alamat', 'Bandung')
                    ->press('Simpan Supplier')
                    ->waitForText('PT Makmur Jaya') 
                    ->type('nama_bahan', 'Tepung Terigu')
                    ->select('supplier_id', Supplier::latest()->first()->id)                    
                    ->type('stok', '150')
                    ->type('stok_minimal', '25')
                    ->type('satuan', 'Kg')
                    ->press('Simpan')
                    ->waitForText('Bahan baku berhasil ditambahkan!');
        });
    }

    /** 2. NEGATIVE: Create (Empty Form) */
    #[Test]
    public function test_negative_create_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->press('Simpan') 
                    ->assertPathIs('/dashboard/dapur/inventory');
        });
    }

    /** 3. POSITIVE: Read */
    #[Test]
    public function test_positive_read_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $supplier = Supplier::create(['nama_supplier' => 'PT Makmur Jaya', 'kontak' => '123', 'alamat' => 'A']);
            BahanBaku::create(['nama_bahan' => 'Tepung', 'supplier_id' => $supplier->id, 'stok' => 10, 'stok_minimal' => 5, 'satuan' => 'Kg']);

            $browser->loginAs($user)
                ->visit('/dashboard/dapur/inventory')
                ->waitForText($supplier->nama_supplier)
                ->waitFor('#icon-' . $supplier->id)
                ->click('#icon-' . $supplier->id)
                ->pause(500)
                ->assertSee('Tepung');
        });
    }

    /** 4. NEGATIVE: Read (Empty Supplier) */
    #[Test]
    public function test_negative_read_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $supplier = Supplier::create(['nama_supplier' => 'Supplier Kosong', 'kontak' => '123', 'alamat' => 'A']);

            $browser->loginAs($user)
                ->visit('/dashboard/dapur/inventory')
                ->waitFor('#icon-' . $supplier->id)
                ->click('#icon-' . $supplier->id)
                ->pause(500)
                ->assertSee('Belum ada bahan baku dari supplier ini.');
        });
    }

    /** 5. POSITIVE: Update Supplier */
    #[Test]
    public function test_positive_update_supplier(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $supplier = Supplier::create(['nama_supplier' => 'Nama Lama', 'kontak' => '123', 'alamat' => 'A']);

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->waitFor('a[href*="suppliers/' . $supplier->id . '/edit"]')
                    ->click('a[href*="suppliers/' . $supplier->id . '/edit"]')
                    ->waitForText('Edit Supplier')
                    ->type('nama_supplier', 'Nama Baru')
                    ->press('Update Supplier')
                    ->waitForText('Nama Baru')
                    ->assertSee('Nama Baru');
        });
    }

    /** 6. NEGATIVE: Update Supplier (Empty Name) */
    #[Test]
    public function test_negative_update_supplier(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $supplier = Supplier::create(['nama_supplier' => 'Supplier Test', 'kontak' => '123', 'alamat' => 'A']);

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->waitFor('a[href*="suppliers/' . $supplier->id . '/edit"]')
                    ->click('a[href*="suppliers/' . $supplier->id . '/edit"]')
                    ->waitForText('Edit Supplier')
                    ->clear('nama_supplier')
                    ->press('Update Supplier')
                    ->assertPathContains('/suppliers/' . $supplier->id . '/edit');
        });
    }

    /** 7. POSITIVE: Update Bahan Baku */
    #[Test]
    public function test_positive_update_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $supplier = Supplier::create(['nama_supplier' => 'PT Test', 'kontak' => '123', 'alamat' => 'A']);
            $bahan = BahanBaku::create(['nama_bahan' => 'Bahan A', 'supplier_id' => $supplier->id, 'stok' => 10, 'stok_minimal' => 5, 'satuan' => 'Kg']);

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->waitFor('#icon-' . $supplier->id)
                    ->click('#icon-' . $supplier->id)
                    ->pause(500)
                    ->waitFor('a[href*="bahan-bakus/' . $bahan->id . '/edit"]')
                    ->click('a[href*="bahan-bakus/' . $bahan->id . '/edit"]')
                    ->waitForText('Edit Bahan Baku')
                    ->type('stok', '5000')
                    ->press('Update')
                    ->waitForText('Data bahan baku berhasil diupdate!')
                    ->click('#icon-' . $supplier->id) 
                    ->pause(500)
                    ->assertSee('5000');
        });
    }

    /** 8. NEGATIVE: Update Bahan Baku (Empty) */
    #[Test]
    public function test_negative_update_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $supplier = Supplier::create(['nama_supplier' => 'PT Test', 'kontak' => '123', 'alamat' => 'A']);
            $bahan = BahanBaku::create(['nama_bahan' => 'Bahan A', 'supplier_id' => $supplier->id, 'stok' => 10, 'stok_minimal' => 5, 'satuan' => 'Kg']);

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->waitFor('#icon-' . $supplier->id)
                    ->click('#icon-' . $supplier->id)
                    ->pause(500)
                    ->waitFor('a[href*="bahan-bakus/' . $bahan->id . '/edit"]')
                    ->click('a[href*="bahan-bakus/' . $bahan->id . '/edit"]')
                    ->clear('nama_bahan')
                    ->press('Update')
                    ->assertPathContains('/bahan-bakus/' . $bahan->id . '/edit');
        });
    }
}