<?php

namespace Tests\Browser;

use App\Models\Antropometri;
use App\Models\BahanBaku;
use App\Models\BiayaBelanja;
use App\Models\PlateWaste;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class AnalyticsTableTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * TC.31.01 - PBI #31 Positif
     */
    #[Test]
    public function test_pbi_31_positive_biaya_belanja()
    {
        $supplier = Supplier::first();
        $bahan = BahanBaku::first();

        $this->browse(function (Browser $browser) use ($supplier, $bahan) {
            $browser->visit('/');

            $biaya = BiayaBelanja::create([
                'bahan_baku_id' => $bahan->id,
                'supplier_id' => $supplier->id,
                'jumlah_beli' => 10.5,
                'total_harga' => 100000,
                'tanggal_belanja' => now(),
            ]);

            $this->assertDatabaseHas('biaya_belanja', ['id' => $biaya->id]);
        });
    }

    /**
     * TC.32.01 - PBI #32 Positif
     */
    #[Test]
    public function test_pbi_32_positive_antropometri()
    {
        $siswa = Siswa::first();

        $this->browse(function (Browser $browser) use ($siswa) {
            $browser->visit('/');

            $gizi = Antropometri::create([
                'siswa_id' => $siswa->id,
                'berat_badan' => 55.0,
                'tinggi_badan' => 165.0,
                'tanggal_ukur' => now(),
            ]);

            $this->assertDatabaseHas('antropometris', ['id' => $gizi->id]);
        });
    }

    /**
     * TC.33.01 - PBI #33 Positif
     */
    #[Test]
    public function test_pbi_33_positive_plate_waste()
    {
        $sekolah = Sekolah::first();

        $this->browse(function (Browser $browser) use ($sekolah) {
            $browser->visit('/');

            $waste = PlateWaste::create([
                'sekolah_id' => $sekolah->id,
                'jumlah_waste' => 2.5,
                'tanggal' => now(),
                'keterangan' => 'Skenario Positif Berhasil',
            ]);

            $this->assertDatabaseHas('plate_wastes', ['id' => $waste->id]);
        });
    }

    /**
     * SKENARIO NEGATIF
     */
    #[Test]
    public function test_pbi_31_negative_invalid_supplier()
    {
        $this->expectException(QueryException::class);
        BiayaBelanja::create(['supplier_id' => 99999, 'bahan_baku_id' => 1, 'jumlah_beli' => 1, 'total_harga' => 1, 'tanggal_belanja' => now()]);
    }

    #[Test]
    public function test_pbi_32_negative_missing_date()
    {
        $this->expectException(QueryException::class);
        Antropometri::create(['siswa_id' => 1, 'berat_badan' => 50, 'tinggi_badan' => 160]);
    }

    #[Test]
    public function test_pbi_33_negative_missing_waste_amount()
    {
        $this->expectException(QueryException::class);
        PlateWaste::create(['sekolah_id' => 1, 'tanggal' => now()]);
    }

    /**
     * TC.34_35_36.01 - Visualisasi Laporan & Grafik Analytics Positif
     */
    #[Test]
    public function test_pbi_34_35_36_analytics_dashboard_ui()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@poros.com')
                ->type('password', 'password123')
                ->press('Masuk ke Dashboard')
                ->assertPathIs('/dashboard')
                ->visit('/dashboard/superadmin/analytics')
                ->assertSee('Advanced Analytics')
                ->assertSee('Status Gizi Scorecard')
                ->assertSee('STATUS GIZI NORMAL')
                ->assertSee('STATUS GIZI KURANG')
                ->assertSee('STATUS GIZI LEBIH / OBESITAS')
                ->assertPresent('#biayaBulananChart')
                ->assertPresent('#biayaChart')
                ->assertPresent('#trendGiziChart')
                ->assertPresent('#wasteChart')
                ->assertPresent('#topMenuChart')
                ->assertPresent('select[name="dapur"]')
                ->assertPresent('select[name="sekolah_id"]')
                ->assertPresent('input[name="start_date"]')
                ->assertPresent('input[name="end_date"]')
                ->select('dapur', 'all')
                ->select('sekolah_id', 'all')
                ->press('Terapkan Filter')
                ->assertPathIs('/dashboard/superadmin/analytics');
        });
    }
}
