<?php

namespace Tests\Browser\Dapur\BahanBakudanSupplier;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BahanBakudanSupplierTest extends DuskTestCase
{
    /**
     * Test create supplier then create bahan baku.
     */
    public function test_positive_create_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            
            $user = User::where('email', 'dapur@poros.com')->first();
            
            if (!$user) {
                $this->fail("User dapur@poros.com tidak ditemukan di database.");
            }

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->assertSee('Manajemen Bahan Baku & Supplier')
                    ->type('nama_supplier', 'PT Makmur Jaya Abadi')
                    ->type('kontak', '081234567890')
                    ->type('alamat', 'Jl. Kebon Jeruk No. 123, Bandung')
                    ->press('Simpan Supplier')
                    ->waitForText('PT Makmur Jaya Abadi') 

                    ->type('nama_bahan', 'Tepung Terigu Premium')
                    ->select('supplier_id', '1')                    
                    ->type('stok', '150')
                    ->type('stok_minimal', '25')
                    ->type('satuan', 'Kg')
                    ->press('Simpan')
                    
                    ->click('#icon-1')
                    ->pause(500)
                    ->assertSee('Tepung Terigu Premium')
                    ->assertSee('150.00 (Min: 25.00)')
                    ->assertSee('Kg');
        });
    }

    public function test_negative_create_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->press('Simpan') 
                    ->assertPresent('input[name="nama_bahan"]:invalid');
        });
    }

    public function test_positive_read_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                ->visit('/dashboard/dapur/inventory')
                ->assertSee('Supplier: PT Makmur Jaya Abadi')
                ->click('[id^="icon-"]')
                ->pause(500)
                ->assertSee('Tepung Terigu Premium')
                ->assertSee('Kg');
        });
    }

    public function test_negative_read_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $browser->loginAs($user)
                ->visit('/dashboard/dapur/inventory')
                ->click('#icon-2')
                ->pause(500)
                ->assertSee('Belum ada bahan baku dari supplier ini.');
        });
    }

    public function test_positive_update_supplier(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->clickLink('Edit Supplier')
                    ->assertPathContains('/suppliers/1/edit')
                    ->pause(500)
                    ->type('nama_supplier', 'PT Makmur Jaya Abadi 2')
                    ->press('Update Supplier')
                    ->waitForText('PT Makmur Jaya Abadi 2')
                    ->assertSee('PT Makmur Jaya Abadi 2');
        });
    }

    public function test_negative_update_supplier(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->clickLink('Edit Supplier')
                    ->assertPathContains('/suppliers/1/edit')
                    ->pause(500)
                    ->clear('nama_supplier')
                    ->press('Update Supplier')
                    ->assertPresent('input[name="nama_supplier"]:invalid');
        });
    }

    public function test_positive_update_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->click('#icon-1')
                    ->pause(500)
                    ->click('#supplier-1 a[href*="bahan-bakus/1/edit"]')
                    ->assertPathContains('/bahan-bakus/1/edit')
                    ->clear('stok')
                    ->type('stok', '5000')
                    ->press('Update')
                    ->assertSee('Data bahan baku berhasil diupdate!');
        });
    }

    public function test_negative_update_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->click('#icon-1')
                    ->pause(500)
                    ->click('#supplier-1 a[href*="bahan-bakus/1/edit"]')
                    ->clear('nama_bahan')
                    ->press('Update')
                    ->assertPresent('input[name="nama_bahan"]:invalid');
        });
    }
}