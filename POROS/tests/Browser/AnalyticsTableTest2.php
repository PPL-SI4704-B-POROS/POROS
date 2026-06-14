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
        // 1. bikin dummy supplier, katalog, sama bahan baku buat testing belanja
        $supplier = Supplier::create([
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

        $bahan = BahanBaku::create([
            'nama_bahan' => 'Bahan E2E',
            'stok' => 1000,
            'stok_minimal' => 10,
            'satuan' => 'gram',
            'katalog_pangan_id' => $katalog->id,
            'supplier_id' => $supplier->id,
        ]);

        // set harga satuannya mahal sekalian (15jt/kg) biar nanti gampang masuk top 3 supplier
        FormHarga::create([
            'harga_satuan' => 15000000,
            'satuan_harga' => 'kg',
            'tanggal_update' => now()->toDateString(),
            'supplier_id' => $supplier->id,
            'bahan_id' => $bahan->id,
        ]);

        // daftarin itemnya ke stok gudang dulu dengan jumlah awal 0
        $stok = StokGudang::create([
            'bahan_baku_id' => $bahan->id,
            'supplier_id' => $supplier->id,
            'quantity' => 0,
            'satuan' => 'kg',
        ]);

        // 2. login jadi dapur terus input stok masuk di fitur stock gudang
        $this->browse(function (Browser $browser) use ($stok) {
            $browser->loginAs(User::where('email', 'dapur@poros.com')->first())
                ->visit('/dashboard/dapur/deliveries')
                ->assertSee('Stock Gudang')
                ->waitFor("@stock-btn-{$stok->id}")
                ->click("@stock-btn-{$stok->id}")
                ->waitFor('#incomingModal')
                ->type('quantity', '10');

            // input tanggal pake javascript biar gak bentrok sama format locale chrome
            $browser->script("document.querySelector('#incomingModal input[name=\"incoming_date\"]').value = '".now()->toDateString()."';");

            $browser->press('Tambah Stok')
                ->waitForText('Stok berhasil diperbarui.')
                ->assertSee('Stok berhasil diperbarui.');
        });

        // pastiin datanya beneran kesimpen ke database (10 kg x 15jt = 150jt)
        $this->assertDatabaseHas('biaya_belanja', [
            'supplier_id' => $supplier->id,
            'bahan_baku_id' => $bahan->id,
            'jumlah_beli' => 10.0,
            'total_harga' => 150000000.0,
        ]);

        // 3. login jadi admin terus cek dashboard analytic buat mastiin datanya masuk
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'admin@poros.com')->first())
                ->visit('/dashboard/superadmin/analytics')
                ->assertSee('Advanced Analytics')
                ->assertSee('PT Pangan E2E')
                ->assertSee('Rp 150.000.000');

            // intip isi labels di Chart.js lewat script browser buat pastiin chartnya terupdate
            $labels = $browser->script("
                const chart = Chart.getChart('biayaChart');
                return chart ? chart.data.labels : [];
            ");
            $this->assertContains('Bahan E2E', $labels[0]);
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
}
