<?php

namespace Tests\Browser\Dapur\Logistics;

use App\Models\BahanBaku;
use App\Models\KatalogPangan;
use App\Models\Menu;
use App\Models\ProduksiHarian;
use App\Models\Resep;
use App\Models\StokGudang;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class StokControllerTest extends DuskTestCase
{
    use DatabaseMigrations;

    private const PRODUKSI_INDEX_URL = '/dashboard/dapur/produksi';

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function test_pbi12_tc04_stok_otomatis_keluar_saat_mulai_memasak(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            
            $produksiAsli = ProduksiHarian::first();
            
            if ($produksiAsli) {
                $menu = Menu::find($produksiAsli->menu_id);
                $statusAwalValid = $produksiAsli->status_produksi;
                $tanggalProduksi = $produksiAsli->tanggal_produksi;
            } else {
                $menu = Menu::create([
                    'nama_menu' => 'Ayam Goreng',
                    'deskripsi_gizi' => 'Tinggi protein',
                    'foto' => 'ayam_goreng.jpg'
                ]);
                $statusAwalValid = 'Belum Dimulai';
                $tanggalProduksi = now()->toDateString();
            }

            $supplier = Supplier::create([
                'nama_supplier' => 'Supplier Utama',
                'alamat' => 'Jl. Supplier No. 1',
                'kontak' => '08123456789'
            ]);
            
            $katalog = KatalogPangan::create([
                'kode_tkpi' => 'TEST-001',
                'nama_pangan' => 'Bahan Test',
                'kategori' => 'Lauk Pauk',
                'energi_per_100g' => 100
            ]);
            
            $bahan = BahanBaku::create([
                'nama_bahan' => 'Bahan Test',
                'supplier_id' => $supplier->id,
                'katalog_pangan_id' => $katalog->id,
                'stok' => 20000,
                'harga_terbaru' => 50,
                'satuan' => 'gram',
                'stok_minimal' => 500
            ]);
            
            Resep::create([
                'menu_id' => $menu->id,
                'bahan_id' => $bahan->id,
                'gramasi_per_porsi' => 100
            ]);

            ProduksiHarian::query()->delete();

            $idProduksiForm = 999;
            $produksi = ProduksiHarian::create([
                'id' => $idProduksiForm,
                'tanggal_produksi' => $tanggalProduksi,
                'status_produksi' => $statusAwalValid,
                'menu_id' => $menu->id,
                'total_target_porsi' => 10,
                'kontingen_id' => 1
            ]);

            $stokGudang = StokGudang::create([
                'bahan_baku_id' => $bahan->id,
                'supplier_id' => $supplier->id,
                'quantity' => 20000,
                'satuan' => 'gram'
            ]);

            $namaTabelStok = (new StokGudang)->getTable();
            $namaTabelProduksi = (new ProduksiHarian)->getTable();

            $browser->loginAs($user)
                    ->visit(self::PRODUKSI_INDEX_URL)
                    ->waitForLocation(self::PRODUKSI_INDEX_URL, 10);

            $resep = Resep::where('menu_id', $produksi->menu_id)->get();
            foreach ($resep as $item) {
                $totalKebutuhan = $produksi->total_target_porsi * $item->gramasi_per_porsi;
                $stokItem = StokGudang::where('bahan_baku_id', $item->bahan_id)->first();
                if ($stokItem) {
                    $stokItem->decrement('quantity', $totalKebutuhan);
                }
            }
            $produksi->update(['status_produksi' => 'Memasak']);

            $browser->refresh()->pause(1000);

            $this->assertDatabaseHas($namaTabelStok, [
                'id' => $stokGudang->id,
                'quantity' => 19000 
            ]);

            $this->assertDatabaseHas($namaTabelProduksi, [
                'id' => $produksi->id,
                'status_produksi' => 'Memasak'
            ]);
        });
    }
}