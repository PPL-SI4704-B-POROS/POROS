<?php

namespace Tests\Browser\Dapur\Logistics;

use App\Models\BahanBaku;
use App\Models\KatalogPangan;
use App\Models\Menu;
use App\Models\ProduksiHarian;
use App\Models\Resep;
use App\Models\Siswa;
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

    private const STOK_INDEX_URL = '/dashboard/dapur/deliveries';

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function test_pbi19_tc01_predict_stock_status_good_positive(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            
            $supplier = Supplier::first() ?? Supplier::create(['nama_supplier' => 'Catering Selaras']);
            $katalog = KatalogPangan::first() ?? KatalogPangan::create(['kategori' => 'Sumber Karbohidrat']);
            
            $bahan = BahanBaku::create([
                'nama_bahan' => 'Beras Cianjur',
                'supplier_id' => $supplier->id,
                'katalog_pangan_id' => $katalog->id,
                'stok' => 1000,
                'harga_terbaru' => 12,
                'satuan' => 'gram',
                'stok_minimal' => 100
            ]);

            if (Siswa::count() === 0) {
                for ($i = 0; $i < 10; $i++) {
                    Siswa::create([
                        'nama_siswa' => 'Siswa Test ' . $i,
                        'status' => 'Active',
                        'nis' => 'NIS' . rand(1000, 9999),
                        'kelas' => '1A'
                    ]);
                }
            }
            
            $menu = Menu::first() ?? Menu::create(['nama_menu' => 'Menu Test']);
            
            Resep::create([
                'menu_id' => $menu->id,
                'bahan_id' => $bahan->id,
                'gramasi_per_porsi' => 100
            ]);

            $schedules = ProduksiHarian::whereNotIn('status_produksi', ['Memasak', 'Selesai'])
                ->take(3)
                ->get();

            if ($schedules->count() > 0) {
                foreach ($schedules as $schedule) {
                    $schedule->update([
                        'menu_id' => $menu->id,
                        'total_target_porsi' => 10
                    ]);
                }
            } else {
                $statusValid = ProduksiHarian::first()?->status_produksi ?? 'Draft';
                for ($i = 0; $i < 3; $i++) {
                    ProduksiHarian::create([
                        'tanggal_produksi' => now()->addDays($i)->toDateString(),
                        'status_produksi' => $statusValid,
                        'menu_id' => $menu->id,
                        'total_target_porsi' => 10,
                        'kontingen_id' => 1
                    ]);
                }
            }

            StokGudang::create([
                'bahan_baku_id' => $bahan->id,
                'supplier_id' => $supplier->id,
                'quantity' => 20000,
                'satuan' => 'gram'
            ]);

            $browser->loginAs($user)
                    ->visit(self::STOK_INDEX_URL)
                    ->waitForText('Beras Cianjur')
                    ->assertSee('Aman 7 Hari')
                    ->assertSeeIn('.card', '1')
                    ->assertPresent("div[style*='background:#22c55e']")
                    ->assertPresent("span[style*='background:#dcfce7']");
        });
    }

    #[Test]
    public function test_pbi19_tc02_predict_stock_status_low_via_adjustment(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            
            $supplier = Supplier::first() ?? Supplier::create(['nama_supplier' => 'Toko Sembako']);
            $katalog = KatalogPangan::first() ?? KatalogPangan::create(['kategori' => 'Sayuran']);
            
            $bahan = BahanBaku::create([
                'nama_bahan' => 'Kentang Dieng',
                'supplier_id' => $supplier->id,
                'katalog_pangan_id' => $katalog->id,
                'stok' => 2000,
                'harga_terbaru' => 15,
                'satuan' => 'gram',
                'stok_minimal' => 100
            ]);

            if (Siswa::count() === 0) {
                for ($i = 0; $i < 10; $i++) {
                    Siswa::create([
                        'nama_siswa' => 'Siswa Test ' . $i,
                        'status' => 'Active',
                        'nis' => 'NIS' . rand(1000, 9999),
                        'kelas' => '1A'
                    ]);
                }
            }
            
            $menu = Menu::first() ?? Menu::create(['nama_menu' => 'Menu Test']);
            
            Resep::create([
                'menu_id' => $menu->id,
                'bahan_id' => $bahan->id,
                'gramasi_per_porsi' => 100
            ]);

            $statusValid = ProduksiHarian::whereNotIn('status_produksi', ['Memasak', 'Selesai'])->first()?->status_produksi ?? 'Draft';
            
            ProduksiHarian::query()->delete();

            for ($i = 0; $i < 5; $i++) {
                ProduksiHarian::create([
                    'tanggal_produksi' => now()->addDays($i)->toDateString(),
                    'status_produksi' => $statusValid,
                    'menu_id' => $menu->id,
                    'total_target_porsi' => 10,
                    'kontingen_id' => 1
                ]);
            }

            StokGudang::create([
                'bahan_baku_id' => $bahan->id,
                'supplier_id' => $supplier->id,
                'quantity' => 3500,
                'satuan' => 'gram'
            ]);

            $browser->loginAs($user)
                    ->visit(self::STOK_INDEX_URL)
                    ->waitForText('Kentang Dieng')
                    ->assertSee('Stok Menipis')
                    ->assertPresent("div[style*='background:#f59e0b']")
                    ->assertPresent("span[style*='background:#fef3c7']");
        });
    }

    #[Test]
    public function test_pbi19_tc03_predict_stock_status_critical_fallback(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            
            $supplier = Supplier::first() ?? Supplier::create(['nama_supplier' => 'Daging Segar Jaya']);
            $katalog = KatalogPangan::first() ?? KatalogPangan::create(['kategori' => 'Lauk Pauk']);
            
            $bahan = BahanBaku::create([
                'nama_bahan' => 'Daging Sapi Slice',
                'supplier_id' => $supplier->id,
                'katalog_pangan_id' => $katalog->id,
                'stok' => 500,
                'harga_terbaru' => 120,
                'satuan' => 'gram',
                'stok_minimal' => 100
            ]);

            if (Siswa::count() === 0) {
                for ($i = 0; $i < 10; $i++) {
                    Siswa::create([
                        'nama_siswa' => 'Siswa Test ' . $i,
                        'status' => 'Active',
                        'nis' => 'NIS' . rand(1000, 9999),
                        'kelas' => '1A'
                    ]);
                }
            }
            
            $menu = Menu::first() ?? Menu::create(['nama_menu' => 'Menu Test']);
            
            Resep::create([
                'menu_id' => $menu->id,
                'bahan_id' => $bahan->id,
                'gramasi_per_porsi' => 100
            ]);

            $statusValid = ProduksiHarian::whereNotIn('status_produksi', ['Memasak', 'Selesai'])->first()?->status_produksi ?? 'Draft';
            
            ProduksiHarian::query()->delete();

            ProduksiHarian::create([
                'tanggal_produksi' => now()->addDays(1)->toDateString(),
                'status_produksi' => $statusValid,
                'menu_id' => $menu->id,
                'total_target_porsi' => 10,
                'kontingen_id' => 1
            ]);

            StokGudang::create([
                'bahan_baku_id' => $bahan->id,
                'supplier_id' => $supplier->id,
                'quantity' => 500,
                'satuan' => 'gram'
            ]);

            $browser->loginAs($user)
                    ->visit(self::STOK_INDEX_URL)
                    ->waitForText('Daging Sapi Slice')
                    ->assertSee('Perlu Kelola Restock')
                    ->assertSee('Kritis')
                    ->assertPresent("div[style*='background:#ef4444']")
                    ->assertPresent("span[style*='background:#fee2e2']");
        });
    }
}