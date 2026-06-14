<?php

namespace Tests\Browser\Dapur\BahanBakudanSupplierSprint2Test;

use App\Models\User;
use App\Models\Supplier;
use App\Models\BahanBaku;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;

class BahanBakudanSupplierSprint2Test extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_positive_delete_supplier(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            
            $supplier = Supplier::create([
                'nama_supplier' => 'Supplier Tumbal Delete', 
                'kontak' => '081299998888', 
                'alamat' => 'Jalan Kenangan'
            ]);

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->waitFor('form[action*="suppliers/' . $supplier->id . '"] button.btn-delete')
                    ->click('form[action*="suppliers/' . $supplier->id . '"] button.btn-delete') 
                    ->pause(500)
                    ->acceptDialog()
                    ->waitForText('berhasil') 
                    ->assertDontSee('Supplier Tumbal Delete');
        });
    }

    public function test_positive_delete_bahan_baku(): void
    {
        $user = User::where('email', 'dapur@poros.com')->first();
        
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Khusus Bahan', 
            'kontak' => '081233334444', 
            'alamat' => 'Bandung'
        ]);

        $bahanBaku = BahanBaku::create([
            'nama_bahan' => 'Bahan Tumbal Hapus',
            'stok' => 10,
            'satuan' => 'kg',
            'stok_minimal' => 2,
            'supplier_id' => $supplier->id,
            'harga' => 15000
        ]);

        $this->browse(function (Browser $browser) use ($user, $supplier, $bahanBaku) {
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    
                    ->click('#header-' . $supplier->id)
                    ->pause(300)
                    ->waitForText('Bahan Tumbal Hapus')
                    ->click('form[action*="bahan-bakus/' . $bahanBaku->id . '"] button.btn-delete')
                    ->pause(500)
                    ->acceptDialog()
                    ->waitForText('berhasil')
                    ->assertDontSee('Bahan Tumbal Hapus');
        });
        $this->assertDatabaseMissing('bahan_bakus', [
            'id' => $bahanBaku->id,
            'deleted_at' => null
        ]);
    }

    public function test_positive_create_harga_bahan_baku(): void
    {
        $user = User::where('email', 'dapur@poros.com')->first();
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Beras Sejahtera',
            'kontak' => '0812345678',
            'alamat' => 'Bandung'
        ]);

        $this->browse(function (Browser $browser) use ($user, $supplier) {
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->type('nama_bahan', 'Beras Pandan Wangi')
                    ->select('supplier_id', $supplier->id)
                    ->type('stok', '50')
                    ->type('stok_minimal', '10')
                    ->select('satuan', 'kg')
                    ->type('harga', '15000')
                    ->click('button.btn-submit.blue-variant')
                    ->waitForText('berhasil')
                    ->click('#header-' . $supplier->id)
                    ->pause(300)
                    ->assertSee('Beras Pandan Wangi')
                    ->assertSee('Rp 15.000');
        });
    }

    public function test_negative_create_harga_bahan_baku(): void
    {
        $user = User::where('email', 'dapur@poros.com')->first();
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Gula Manis',
            'kontak' => '081277776666',
            'alamat' => 'Jakarta'
        ]);

        $this->browse(function (Browser $browser) use ($user, $supplier) {
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->type('nama_bahan', 'Gula Pasir Putih')
                    ->select('supplier_id', $supplier->id)
                    ->type('stok', '20')
                    ->type('stok_minimal', '5')
                    ->select('satuan', 'gram')
                    ->type('harga', '-5000')
                    ->click('button.btn-submit.blue-variant')
                    ->pause(500);
        });
    }

    public function test_positive_read_harga_bahan_baku(): void
    {
        $user = User::where('email', 'dapur@poros.com')->first();
        
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Beras Sejahtera Read',
            'kontak' => '0812345678',
            'alamat' => 'Bandung'
        ]);

        $bahanBaku = BahanBaku::create([
            'nama_bahan' => 'Beras Pandan Wangi',
            'stok' => 50,
            'satuan' => 'kg',
            'stok_minimal' => 10,
            'supplier_id' => $supplier->id,
            'harga' => 15000 
        ]);

        \App\Models\FormHarga::create([
            'harga_satuan' => 15000,
            'satuan_harga' => 'kg',
            'tanggal_update' => now()->toDateString(),
            'supplier_id' => $supplier->id,
            'bahan_id' => $bahanBaku->id,
        ]);

        $this->browse(function (Browser $browser) use ($user, $supplier) {
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->click('#header-' . $supplier->id)
                    ->pause(300)
                    ->assertSee('Beras Pandan Wangi')
                    ->assertSee('Rp 15.000')
                    ->assertSee('per kg');
        });
    }

    public function test_negative_read_harga_bahan_baku(): void
    {
        $user = User::where('email', 'dapur@poros.com')->first();
        
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Tanpa Harga',
            'kontak' => '081299990000',
            'alamat' => 'Bandung'
        ]);

        $bahanBaku = BahanBaku::create([
            'nama_bahan' => 'Minyak Goreng Curah',
            'stok' => 10,
            'satuan' => 'liter',
            'stok_minimal' => 2,
            'supplier_id' => $supplier->id,
            'harga' => 0 
        ]);

        $this->browse(function (Browser $browser) use ($user, $supplier) {
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->click('#header-' . $supplier->id)
                    ->pause(300)
                    ->assertSee('Minyak Goreng Curah')
                    ->assertSee('Rp 0');
        });
    }

    public function test_positive_update_harga_bahan_baku(): void
    {
        $user = User::where('email', 'dapur@poros.com')->first();
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Gula Update',
            'kontak' => '0812345678',
            'alamat' => 'Bandung'
        ]);

        $bahanBaku = BahanBaku::create([
            'nama_bahan' => 'Gula Pasir',
            'stok' => 30,
            'satuan' => 'kg',
            'stok_minimal' => 5,
            'supplier_id' => $supplier->id,
            'harga' => 15000
        ]);

        \App\Models\FormHarga::create([
            'harga_satuan' => 15000,
            'satuan_harga' => 'kg',
            'tanggal_update' => now()->toDateString(),
            'supplier_id' => $supplier->id,
            'bahan_id' => $bahanBaku->id,
        ]);

        $this->browse(function (Browser $browser) use ($user, $supplier, $bahanBaku) {
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')      
                    ->click('#header-' . $supplier->id)
                    ->pause(300)
                    ->click('a[href*="bahan-bakus/' . $bahanBaku->id . '/edit"]')                    ->waitForText('Edit Data Bahan Baku')
                    ->type('harga', '20000')
                    ->click('button.btn-submit.blue-variant')   
                    ->waitForText('berhasil')
                    ->click('#header-' . $supplier->id)
                    ->pause(300)
                    ->assertSee('Gula Pasir')
                    ->assertSee('Rp 20.000');
        });
    }

    public function test_negative_update_harga_bahan_baku(): void
    {
        $user = User::where('email', 'dapur@poros.com')->first();
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Garam Update',
            'kontak' => '0812345678',
            'alamat' => 'Bandung'
        ]);

        $bahanBaku = BahanBaku::create([
            'nama_bahan' => 'Garam Dapur',
            'stok' => 10,
            'satuan' => 'kg',
            'stok_minimal' => 2,
            'supplier_id' => $supplier->id,
            'harga' => 5000
        ]);

        $this->browse(function (Browser $browser) use ($user, $supplier, $bahanBaku) {
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->click('#header-' . $supplier->id)
                    ->pause(300)
                    ->click('a[href*="bahan-bakus/' . $bahanBaku->id . '/edit"]')
                    ->waitForText('Edit Data Bahan Baku')
                    ->type('harga', '-5000')
                    ->click('button.btn-submit.blue-variant')
                    ->pause(500);
                    
            $browser->assertDontSee('Rp -5.000');
        });
    }

    public function test_positive_delete_harga_bahan_baku(): void
    {
        $user = User::where('email', 'dapur@poros.com')->first();
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Ayam Hapus',
            'kontak' => '0812345678',
            'alamat' => 'Bandung'
        ]);

        $bahanBaku = BahanBaku::create([
            'nama_bahan' => 'Daging Ayam Fillet',
            'stok' => 15,
            'satuan' => 'kg',
            'stok_minimal' => 3,
            'supplier_id' => $supplier->id,
            'harga' => 45000
        ]);

        \App\Models\FormHarga::create([
            'harga_satuan' => 45000,
            'satuan_harga' => 'kg',
            'tanggal_update' => now()->toDateString(),
            'supplier_id' => $supplier->id,
            'bahan_id' => $bahanBaku->id,
        ]);

        $this->browse(function (Browser $browser) use ($user, $supplier, $bahanBaku) {
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->click('#header-' . $supplier->id)
                    ->pause(300)
                    ->click('form[action*="bahan-bakus/' . $bahanBaku->id . '"] button.btn-delete')
                    ->pause(500)
                    ->acceptDialog()
                    ->waitForText('berhasil')
                    ->click('#header-' . $supplier->id)
                    ->pause(300)
                    ->assertDontSee('Daging Ayam Fillet')
                    ->assertDontSee('Rp 45.000');
        });
    }

    public function test_negative_delete_harga_bahan_baku(): void
    {
        $user = User::where('email', 'dapur@poros.com')->first();
        $supplier = Supplier::create([
            'nama_supplier' => 'Supplier Kecap Aman',
            'kontak' => '0812345678',
            'alamat' => 'Bandung'
        ]);

        $bahanBaku = BahanBaku::create([
            'nama_bahan' => 'Kecap Manis Botol',
            'stok' => 20,
            'satuan' => 'ml',
            'stok_minimal' => 5,
            'supplier_id' => $supplier->id,
            'harga' => 11000
        ]);

        \App\Models\FormHarga::create([
            'harga_satuan' => 11000,
            'satuan_harga' => 'ml',
            'tanggal_update' => now()->toDateString(),
            'supplier_id' => $supplier->id,
            'bahan_id' => $bahanBaku->id,
        ]);

        $this->browse(function (Browser $browser) use ($user, $supplier, $bahanBaku) {
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/inventory')
                    ->click('#header-' . $supplier->id)
                    ->pause(300)
                    ->click('form[action*="bahan-bakus/' . $bahanBaku->id . '"] button.btn-delete')
                    ->pause(500)
                    ->dismissDialog()
                    ->assertSee('Kecap Manis Botol')
                    ->assertSee('Rp 11.000');
        });
    }
}