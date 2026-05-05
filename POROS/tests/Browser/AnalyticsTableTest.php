<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\BiayaBelanja;
use App\Models\Antropometri;
use App\Models\PlateWaste;
use App\Models\Supplier;
use App\Models\Siswa;
use App\Models\BahanBaku;
use App\Models\Sekolah;
use PHPUnit\Framework\Attributes\Test;

class AnalyticsTableTest extends DuskTestCase
{
    /**
     * TC.31.01 - PBI #31 Positif 
     */
    #[Test]
    public function test_pbi_31_positive_biaya_belanja()
    {
        $supplier = Supplier::first();
        $bahan    = BahanBaku::first();

        $this->browse(function (Browser $browser) use ($supplier, $bahan) {
            $browser->visit('/');

            $biaya = BiayaBelanja::create([
                'bahan_baku_id'   => $bahan->id,
                'supplier_id'     => $supplier->id,
                'jumlah_beli'     => 10.5,
                'total_harga'     => 100000,
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
                'siswa_id'     => $siswa->id,
                'berat_badan'  => 55.0,
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
                'sekolah_id'   => $sekolah->id,
                'jumlah_waste' => 2.5,
                'tanggal'      => now(),
                'keterangan'   => 'Skenario Positif Berhasil',
            ]);

            $this->assertDatabaseHas('plate_wastes', ['id' => $waste->id]);
        });
    }

    /**
     * SKENARIO NEGATIF 
     */
    #[Test]
    public function test_pbi_31_negative_invalid_supplier() {
        $this->expectException(\Illuminate\Database\QueryException::class);
        BiayaBelanja::create(['supplier_id' => 99999, 'bahan_baku_id' => 1, 'jumlah_beli' => 1, 'total_harga' => 1, 'tanggal_belanja' => now()]);
    }

    #[Test]
    public function test_pbi_32_negative_missing_date() {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Antropometri::create(['siswa_id' => 1, 'berat_badan' => 50, 'tinggi_badan' => 160]);
    }

    #[Test]
    public function test_pbi_33_negative_missing_waste_amount() {
        $this->expectException(\Illuminate\Database\QueryException::class);
        PlateWaste::create(['sekolah_id' => 1, 'tanggal' => now()]);
    }
}