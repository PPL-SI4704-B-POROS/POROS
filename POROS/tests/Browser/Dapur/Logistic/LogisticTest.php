<?php

namespace Tests\Browser\Dapur\Logistic;

use App\Models\User;
use App\Models\Pengiriman;
use App\Models\Sekolah;
use App\Models\Kurir;
use App\Models\ProduksiHarian;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;

class LogisticTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test PBI #23 - Update Status Kirim
     * Test PBI #24 - Create Nama Penerima
     */
    #[Test]
    public function test_update_status_and_create_handover_details(): void
    {
        // Create a new school to ensure unique search results
        $sekolah = Sekolah::create([
            'nama_sekolah' => 'Sekolah Testing',
            'alamat' => 'Alamat Testing',
            'kontak' => '08123456789',
            'jumlah_siswa' => 100,
        ]);

        $kurir = Kurir::first();
        $produksi = ProduksiHarian::first();

        $delivery = Pengiriman::create([
            'sekolah_id' => $sekolah->id,
            'kurir_id' => $kurir->id,
            'produksi_id' => $produksi->id,
            'status_kirim' => 'Menunggu',
            'waktu_berangkat' => null,
            'waktu_sampai' => null,
            'created_at' => now()->addMinute(),
        ]);

        $this->browse(function (Browser $browser) use ($delivery, $sekolah) {
            // Step 1: Login
            $browser->visit('/login')
                    ->type('email', 'dapur@poros.com')
                    ->type('password', 'password123')
                    ->press('Masuk ke Dashboard')
                    ->waitForLocation('/dashboard/dapur/meal-planning', 10);

            // Step 2: Masuk ke tab Logistics & Deliveries
            $browser->clickLink('Logistics & Deliveries')
                    ->waitForText('Logistics & Deliveries')
                    ->assertPathIs('/dashboard/dapur/logistics-deliveries');

            // Filter by our unique school
            $browser->type('search', 'Sekolah Testing')
                    ->keys('.search-input', '{enter}')
                    ->pause(1000)
                    ->waitForText('Sekolah Testing');

            // PBI #23 - Update Status Kirim
            $browser->select('status_kirim', 'Sampai')
                    ->waitForText('Status pengiriman berhasil diperbarui.')
                    ->pause(1000);

            // PBI #24 - Create Nama Penerima (Input Penerima)
            $browser->click('.handover-btn[data-id="' . $delivery->id . '"]')
                    ->waitFor('#handoverModal', 10)
                    ->pause(500)
                    ->type('nama_penerima', 'Budi Santoso')
                    ->type('ompreng_kembali', '50')
                    ->type('tanggal_sisa', '2026-06-14')
                    ->type('menu_tersisa', 'Nasi Ayam Goreng')
                    // Mengisi Alasan Sisa Makanan
                    ->type('wastes[rasa tidak enak]', '5')
                    ->type('wastes[porsi kebanyakan]', '10')
                    ->type('wastes[menu ga menarik]', '2')
                    ->type('wastes[siswa sedang sakit]', '3')
                    ->type('wastes[kurang matang]', '1')
                    // Klik Simpan
                    ->press('Simpan Bukti Terima')
                    // Verifikasi notifikasi
                    ->waitForText('Bukti serah terima berhasil disimpan.')
                    ->assertSee('Bukti serah terima berhasil disimpan.');
        });
    }
}
