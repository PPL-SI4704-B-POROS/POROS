<?php

namespace Tests\Browser;

use App\Models\Antropometri;
use App\Models\BahanBaku;
use App\Models\FormHarga;
use App\Models\KatalogPangan;
use App\Models\Kurir;
use App\Models\Menu;
use App\Models\Pengiriman;
use App\Models\ProduksiHarian;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\StokGudang;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AnalyticsTableTest2 extends DuskTestCase
{
    // biar database bersih terus setiap abis ganti test case
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        // jalanin seeder bawaan dulu biar dapet data dasar
        $this->artisan('db:seed');
    }

    /**
     * PBI #31 & PBI #34 - E2E Flow Biaya Belanja
     */
    public function test_e2e_tren_biaya_flow(): void
    {
        // 1. bikin dummy supplier pertama, katalog, sama bahan baku buat testing belanja
        $supplier1 = Supplier::create([
            'nama_supplier' => 'PT Pangan E2E',
            'alamat' => 'Alamat E2E',
            'kontak' => '0899999999',
        ]);

        $katalog = KatalogPangan::create([
            'kode_tkpi' => 'E2E-001',
            'nama_pangan' => 'Bahan E2E',
            'kategori' => 'Kategori E2E',
            'energi_per_100g' => 100,
        ]);

        $bahan1 = BahanBaku::create([
            'nama_bahan' => 'Bahan E2E',
            'stok' => 1000,
            'stok_minimal' => 10,
            'satuan' => 'gram',
            'katalog_pangan_id' => $katalog->id,
            'supplier_id' => $supplier1->id,
        ]);

        // set harga satuannya mahal sekalian (15jt/kg)
        FormHarga::create([
            'harga_satuan' => 15000000,
            'satuan_harga' => 'kg',
            'tanggal_update' => now()->toDateString(),
            'supplier_id' => $supplier1->id,
            'bahan_id' => $bahan1->id,
        ]);

        // daftarin item pertama ke stok gudang dulu
        $stok1 = StokGudang::create([
            'bahan_baku_id' => $bahan1->id,
            'supplier_id' => $supplier1->id,
            'quantity' => 0,
            'satuan' => 'kg',
        ]);

        // 1b. bikin dummy supplier kedua yang menyuplai barang bermerk sama ("Bahan E2E")
        $supplier2 = Supplier::create([
            'nama_supplier' => 'PT Pangan E2E Kedua',
            'alamat' => 'Alamat E2E Kedua',
            'kontak' => '0888888888',
        ]);

        $bahan2 = BahanBaku::create([
            'nama_bahan' => 'Bahan E2E', // Nama persis sama
            'stok' => 1000,
            'stok_minimal' => 10,
            'satuan' => 'gram',
            'katalog_pangan_id' => $katalog->id,
            'supplier_id' => $supplier2->id,
        ]);

        // set harga satuan supplier kedua (12jt/kg)
        FormHarga::create([
            'harga_satuan' => 12000000,
            'satuan_harga' => 'kg',
            'tanggal_update' => now()->toDateString(),
            'supplier_id' => $supplier2->id,
            'bahan_id' => $bahan2->id,
        ]);

        // daftarin item kedua ke stok gudang
        $stok2 = StokGudang::create([
            'bahan_baku_id' => $bahan2->id,
            'supplier_id' => $supplier2->id,
            'quantity' => 0,
            'satuan' => 'kg',
        ]);

        // 2. login jadi dapur terus input stok masuk untuk KEDUA barang tadi
        $this->browse(function (Browser $browser) use ($stok1, $stok2) {
            $browser->loginAs(User::where('email', 'dapur@poros.com')->first())
                ->visit('/dashboard/dapur/deliveries')
                ->assertSee('Stock Gudang')
                // input barang pertama (10 kg x 15jt = 150jt)
                ->waitFor("@stock-btn-{$stok1->id}")
                ->click("@stock-btn-{$stok1->id}")
                ->waitFor('#incomingModal')
                ->type('quantity', '10');

            $browser->script("document.querySelector('#incomingModal input[name=\"incoming_date\"]').value = '".now()->toDateString()."';");

            $browser->press('Tambah Stok')
                ->waitForText('Stok berhasil diperbarui.')
                // input barang kedua (10 kg x 12jt = 120jt)
                ->waitFor("@stock-btn-{$stok2->id}")
                ->click("@stock-btn-{$stok2->id}")
                ->waitFor('#incomingModal')
                ->type('quantity', '10');

            $browser->script("document.querySelector('#incomingModal input[name=\"incoming_date\"]').value = '".now()->toDateString()."';");

            $browser->press('Tambah Stok')
                ->waitForText('Stok berhasil diperbarui.');
        });

        // pastiin transaksi pertama kesimpen ke db
        $this->assertDatabaseHas('biaya_belanja', [
            'supplier_id' => $supplier1->id,
            'bahan_baku_id' => $bahan1->id,
            'jumlah_beli' => 10.0,
            'total_harga' => 150000000.0,
        ]);

        // pastiin transaksi kedua kesimpen ke db
        $this->assertDatabaseHas('biaya_belanja', [
            'supplier_id' => $supplier2->id,
            'bahan_baku_id' => $bahan2->id,
            'jumlah_beli' => 10.0,
            'total_harga' => 120000000.0,
        ]);

        // 3. login jadi admin terus cek dashboard analytic buat mastiin penggabungan & pemisahan supplier
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'admin@poros.com')->first())
                ->visit('/dashboard/superadmin/analytics')
                ->assertSee('Advanced Analytics')
                // Supplier harus terpisah (PT Pangan E2E dan PT Pangan E2E Kedua)
                ->assertSee('PT Pangan E2E')
                ->assertSee('Rp 150.000.000')
                ->assertSee('PT Pangan E2E Kedua')
                ->assertSee('Rp 120.000.000');

            // Untuk bahan baku, namanya digabungkan (Bahan E2E harus bernilai kumulatif: 150jt + 120jt = 270jt)
            $totalHargaChart = $browser->script("
                const chart = Chart.getChart('biayaChart');
                if (!chart) return null;
                const idx = chart.data.labels.indexOf('Bahan E2E');
                return idx !== -1 ? chart.data.datasets[0].data[idx] : null;
            ");

            $this->assertEquals(270000000.0, $totalHargaChart[0]);
        });
    }

    /**
     * PBI #32 & PBI #35 - E2E Flow Status Gizi
     */
    public function test_e2e_status_gizi_flow(): void
    {
        // 1. siapin data sekolah sama bikin data siswa baru untuk test status gizi
        $sekolah = Sekolah::first();
        $siswa = Siswa::create([
            'nisn' => '9999999999',
            'nama_siswa' => 'Siswa Gizi E2E',
            'kelas' => '1A',
            'alergi' => null,
            'sekolah_id' => $sekolah->id,
            'contact' => '0812345678',
            'status' => 'Active',
        ]);

        // 2. login jadi petugas sekolah terus input data riwayat kesehatan (antropometri)
        $this->browse(function (Browser $browser) use ($siswa) {
            $browser->loginAs(User::where('email', 'sekolah@poros.com')->first())
                ->visit('/dashboard/sekolah/siswas')
                ->assertSee('Data Siswa')
                ->waitFor("@ukur-btn-{$siswa->id}")
                ->click("@ukur-btn-{$siswa->id}")
                ->waitFor('#ukurModal')
                ->type('berat_badan', '20')
                ->type('tinggi_badan', '110');

            // set tanggal pengukuran pake javascript biar aman
            $browser->script("document.querySelector('#ukurModal input[name=\"tanggal_ukur\"]').value = '".now()->toDateString()."';");

            $browser->press('Simpan Pengukuran')
                ->waitForText('Data antropometri berhasil disimpan.');
        });

        // pastiin status gizi otomatis kehitung sebagai Kurus di database
        $this->assertDatabaseHas('antropometris', [
            'siswa_id' => $siswa->id,
            'berat_badan' => 20.0,
            'tinggi_badan' => 110.0,
            'status_gizi' => 'Kurus',
        ]);

        // itung jumlah siswa berstatus Kurang/Kurus yang ada di db sekarang
        $expectedCount = Antropometri::whereIn('status_gizi', ['Kurus', 'Kurang'])->count();

        // 3. login jadi admin terus cek total status gizi kurang di scorecard dashboard
        $this->browse(function (Browser $browser) use ($expectedCount) {
            $browser->loginAs(User::where('email', 'admin@poros.com')->first())
                ->visit('/dashboard/superadmin/analytics')
                ->assertSee('Advanced Analytics')
                ->assertSeeIn('.scorecard.warning h3', $expectedCount);
        });
    }

    /**
     * PBI #33 & PBI #36 - E2E Flow Food Waste
     */
    public function test_e2e_food_waste_flow(): void
    {
        // 1. setup sekolah, kurir, menu, sama status pengiriman 'Jalan'
        $sekolah = Sekolah::first();
        $kurir = Kurir::first() ?? Kurir::create([
            'nama_kurir' => 'Kurir E2E',
            'no_plat' => 'B 9999 E2E',
            'kontak' => '089999999',
        ]);

        $menu = Menu::first();
        $produksi = ProduksiHarian::create([
            'tanggal_produksi' => now()->toDateString(),
            'total_target_porsi' => 100,
            'status_produksi' => 'Siap Kirim',
            'menu_id' => $menu->id,
        ]);

        // bikin pengiriman baru dengan waktu masa depan biar selalu muncul paling atas (page 1)
        $pengiriman = new Pengiriman([
            'waktu_berangkat' => now(),
            'status_kirim' => 'Jalan',
            'produksi_id' => $produksi->id,
            'sekolah_id' => $sekolah->id,
            'kurir_id' => $kurir->id,
        ]);
        $pengiriman->created_at = now()->addMinutes(10);
        $pengiriman->updated_at = now()->addMinutes(10);
        $pengiriman->save();

        // 2. login jadi dapur terus input serah terima & sisa porsi makanan (food waste)
        $this->browse(function (Browser $browser) use ($pengiriman) {
            $browser->loginAs(User::where('email', 'dapur@poros.com')->first())
                ->visit('/dashboard/dapur/logistics-deliveries')
                ->assertSee('Logistics & Deliveries')
                ->waitFor("@handover-btn-{$pengiriman->id}")
                ->click("@handover-btn-{$pengiriman->id}")
                ->waitFor('#handoverModal')
                ->type('nama_penerima', 'Penerima E2E')
                ->type('ompreng_kembali', '80')
                ->type('menu_tersisa', 'Menu E2E')
                ->type('wastes[rasa tidak enak]', '999'); // isi 999 porsi biar langsung dominan di chart

            // set tanggal sisa pake script
            $browser->script("document.querySelector('#handoverModal input[name=\"tanggal_sisa\"]').value = '".now()->toDateString()."';");

            $browser->press('Simpan Bukti Terima')
                ->waitForText('Bukti serah terima berhasil disimpan.');
        });

        // pastiin data sisa porsi keinput dengan keterangan 'rasa tidak enak' ke database
        $this->assertDatabaseHas('plate_wastes', [
            'pengiriman_id' => $pengiriman->id,
            'jumlah_waste' => 999.0,
            'keterangan' => 'rasa tidak enak',
            'sekolah_id' => $sekolah->id,
        ]);

        // 3. login jadi admin terus pastiin grafiknya ke-update sesuai data food waste tadi
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'admin@poros.com')->first())
                ->visit('/dashboard/superadmin/analytics')
                ->assertSee('Advanced Analytics');

            // ambil label di chart donat sisa makanan (wasteChart)
            $wasteLabels = $browser->script("
                const chart = Chart.getChart('wasteChart');
                return chart ? chart.data.labels : [];
            ");
            $hasWasteLabel = false;
            if (isset($wasteLabels[0]) && is_array($wasteLabels[0])) {
                foreach ($wasteLabels[0] as $lbl) {
                    if (strtolower(trim($lbl)) === 'rasa tidak enak') {
                        $hasWasteLabel = true;
                        break;
                    }
                }
            }
            $this->assertTrue($hasWasteLabel, "Chart labels did not contain 'rasa tidak enak'. Got: ".json_encode($wasteLabels));

            // ambil label di chart bar menu sisa teratas (topMenuChart)
            $topMenuLabels = $browser->script("
                const chart = Chart.getChart('topMenuChart');
                return chart ? chart.data.labels : [];
            ");
            $hasTopMenuLabel = false;
            if (isset($topMenuLabels[0]) && is_array($topMenuLabels[0])) {
                foreach ($topMenuLabels[0] as $lbl) {
                    if (strtolower(trim($lbl)) === 'menu e2e') {
                        $hasTopMenuLabel = true;
                        break;
                    }
                }
            }
            $this->assertTrue($hasTopMenuLabel, "Top menu labels did not contain 'Menu E2E'. Got: ".json_encode($topMenuLabels));
        });
    }

    /**
     * TC.34.02 - PBI #34 Dashboard Skenario Negatif (Inverted Dates)
     */
    public function test_pbi_34_negative_analytics_inverted_dates(): void
    {
        // 1. login sebagai super admin
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'admin@poros.com')->first())
                // buka analytics dengan tanggal terbalik (start > end)
                ->visit('/dashboard/superadmin/analytics?start_date=2026-12-31&end_date=2026-01-01')
                ->assertSee('Advanced Analytics')
                // pastiin widget Top 3 Supplier mendeteksi gak ada data
                ->assertSee('Belum ada data supplier.');
        });
    }

    /**
     * TC.35.02 - PBI #35 Dashboard Skenario Negatif (Non-existent School Filter)
     */
    public function test_pbi_35_negative_analytics_non_existent_school_scorecard(): void
    {
        // 1. login sebagai super admin
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'admin@poros.com')->first())
                // buka analytics dengan id sekolah fiktif (99999)
                ->visit('/dashboard/superadmin/analytics?sekolah_id=99999')
                ->assertSee('Advanced Analytics')
                // semua scorecard status gizi harus menampilkan angka 0
                ->assertSeeIn('.scorecard.good h3', '0')
                ->assertSeeIn('.scorecard.warning h3', '0')
                ->assertSeeIn('.scorecard.bad h3', '0');
        });
    }

    /**
     * TC.36.02 - PBI #36 Dashboard Skenario Negatif (Non-existent School Filter)
     */
    public function test_pbi_36_negative_analytics_non_existent_school_waste(): void
    {
        // 1. login sebagai super admin
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'admin@poros.com')->first())
                // buka analytics dengan id sekolah fiktif (99999)
                ->visit('/dashboard/superadmin/analytics?sekolah_id=99999')
                ->assertSee('Advanced Analytics');

            // ambil data total porsi sisa dari chart donat evaluasi sisa makanan
            // karena sekolah fiktif, datanya harus kosong/nol
            $wasteData = $browser->script("
                const chart = Chart.getChart('wasteChart');
                return chart ? chart.data.datasets[0].data : [];
            ");

            $totalWaste = 0;
            if (isset($wasteData[0]) && is_array($wasteData[0])) {
                $totalWaste = array_sum($wasteData[0]);
            }
            $this->assertEquals(0, $totalWaste);
        });
    }
}
