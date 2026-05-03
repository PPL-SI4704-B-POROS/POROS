<?php

namespace Tests\Browser\Dapur\BahanBakudanSupplier;

use App\Models\User;
use App\Models\Supplier;
use App\Models\BahanBaku;
use App\Models\KatalogPangan;
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
        // Pastikan tabel ada dan user dapur tersedia
        $this->artisan('db:seed');
    }

    #[Test]
    public function test_positive_create_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->assertSee('Manajemen Bahan Baku & Supplier')
                    // Create Supplier
                    ->type('nama_supplier', 'PT Makmur Jaya Abadi')
                    ->type('kontak', '081234567890')
                    ->type('alamat', 'Jl. Kebon Jeruk No. 123, Bandung')
                    ->press('Simpan Supplier')
                    ->waitForText('PT Makmur Jaya Abadi') 

                    // Create Bahan Baku (Berdasarkan Supplier yang baru dibuat)
                    ->type('nama_bahan', 'Tepung Terigu Premium')
                    ->select('supplier_id', Supplier::latest()->first()->id)                    
                    ->type('stok', '150')
                    ->type('stok_minimal', '25')
                    ->type('satuan', 'Kg')
                    ->press('Simpan')
                    ->waitForText('Bahan baku berhasil ditambahkan!')
                    
                    ->click('#icon-' . Supplier::latest()->first()->id)
                    ->pause(500)
                    ->assertSee('Tepung Terigu Premium')
                    ->assertSee('150')
                    ->assertSee('Kg');
        });
    }

    #[Test]
    public function test_negative_create_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->press('Simpan') // Tekan simpan tanpa isi form
                    ->assertPathIs('/dashboard/dapur/inventory');
        });
    }

    #[Test]
    public function test_positive_read_supplier_and_bahan_baku(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $supplier = Supplier::create([
                'nama_supplier' => 'PT Test Read',
                'kontak' => '123',
                'alamat' => 'Alamat'
            ]);

            $browser->loginAs($user)
                ->visit('/dashboard/dapur/inventory')
                ->assertSee('PT Test Read');
        });
    }

    #[Test]
    public function test_positive_update_supplier(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $supplier = Supplier::create([
                'nama_supplier' => 'Supplier Asli',
                'kontak' => '123',
                'alamat' => 'Alamat'
            ]);

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->click('a[href*="suppliers/' . $supplier->id . '/edit"]')
                    ->waitForText('Edit Supplier')
                    ->type('nama_supplier', 'Supplier Diedit')
                    ->press('Update Supplier')
                    ->waitForText('Supplier Diedit')
                    ->assertSee('Supplier Diedit');
        });
    }

    #[Test]
    public function test_positive_delete_supplier(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $supplier = Supplier::create([
                'nama_supplier' => 'Supplier Mau Dihapus',
                'kontak' => '123',
                'alamat' => 'Alamat'
            ]);

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->press('@delete-supplier-' . $supplier->id)
                    ->acceptDialog()
                    ->waitForText('Supplier berhasil dihapus!')
                    ->assertDontSee('Supplier Mau Dihapus');
        });
    }
}