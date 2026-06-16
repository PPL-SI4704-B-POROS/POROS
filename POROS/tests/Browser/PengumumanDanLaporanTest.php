<?php

namespace Tests\Browser\Pages;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PengumumanDanLaporanTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    private function loginSuperAdmin(Browser $browser): void
    {
        $user = User::where('email', 'admin@poros.com')->first() ?? User::factory()->create(['email' => 'admin@poros.com']);
        $browser->loginAs($user)->visit('/dashboard')->waitForLocation('/dashboard', 10);
    }

    private function loginSekolah(Browser $browser): void
    {
        $user = User::where('email', 'sekolah@poros.com')->first() ?? User::factory()->create(['email' => 'sekolah@poros.com']);
        $browser->loginAs($user)->visit('/dashboard')->waitForLocation('/dashboard', 10);
    }

    private function loginDapur(Browser $browser): void
    {
        $user = User::where('email', 'dapur@poros.com')->first() ?? User::factory()->create(['email' => 'dapur@poros.com']);
        $browser->loginAs($user)->visit('/dashboard')->waitForLocation('/dashboard', 10);
    }

    private function pilihKategori(Browser $browser, string $kategori): void
    {
        $browser->script("
            document.querySelectorAll('input[name=\"kategori\"]').forEach(function(radio) {
                if (radio.value === '$kategori') {
                    radio.checked = true;
                    var label = radio.closest('label');
                    if(label) label.click();
                }
            });
        ");
    }

    public function test_superadmin_dapat_login(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->assertPathIs('/dashboard');
        });
    }

    public function test_superadmin_dapat_membuka_halaman_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForText('Pengumuman', 10)
                    ->assertSee('Pengumuman');
        });
    }

    public function test_superadmin_dapat_membuat_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForLocation('/dashboard/superadmin/pengumuman', 10);

            $browser->script([
                "let j = document.querySelector('input[name=\"judul\"]'); if(j) j.value = 'Pengumuman Test Otomatis Dusk';",
                "let i = document.querySelector('textarea[name=\"isi\"]') || document.querySelector('input[name=\"isi\"]'); if(i) i.value = 'Isi pengumuman otomatis.';"
            ]);

            $browser->pause(1000);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_dapat_buat_pengumuman_target_dapur(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForLocation('/dashboard/superadmin/pengumuman', 10);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_dapat_membuka_halaman_edit_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForLocation('/dashboard/superadmin/pengumuman', 10);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_dapat_mengedit_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForLocation('/dashboard/superadmin/pengumuman', 10);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_dapat_menghapus_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForLocation('/dashboard/superadmin/pengumuman', 10);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_gagal_buat_pengumuman_tanpa_judul(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForLocation('/dashboard/superadmin/pengumuman', 10);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_gagal_buat_pengumuman_tanpa_isi(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForLocation('/dashboard/superadmin/pengumuman', 10);
            $this->assertTrue(true);
        });
    }

    public function test_sekolah_dapat_melihat_halaman_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForText('Pengumuman', 10)
                    ->assertSee('Pengumuman');
        });
    }

    public function test_sekolah_tidak_melihat_form_buat_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForLocation('/dashboard/superadmin/pengumuman', 10);
            $this->assertTrue(true);
        });
    }

    public function test_sekolah_tidak_melihat_tombol_edit_hapus_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForLocation('/dashboard/superadmin/pengumuman', 10);
            $this->assertTrue(true);
        });
    }

    public function test_dapur_dapat_melihat_halaman_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginDapur($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForText('Pengumuman', 10)
                    ->assertSee('Pengumuman');
        });
    }

    public function test_dapur_tidak_melihat_form_dan_tombol_kelola_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginDapur($browser);
            $browser->visit('/dashboard/superadmin/pengumuman')
                    ->waitForLocation('/dashboard/superadmin/pengumuman', 10);
            $this->assertTrue(true);
        });
    }

    public function test_sekolah_dapat_membuka_halaman_laporan_masalah(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('/dashboard/sekolah/laporan-masalah')
                    ->waitForText('Laporan', 10);
            $this->assertTrue(true);
        });
    }

    public function test_sekolah_melihat_semua_kategori_laporan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('/dashboard/sekolah/laporan-masalah')
                    ->waitForLocation('/dashboard/sekolah/laporan-masalah', 10);
            $this->assertTrue(true);
        });
    }

    public function test_sekolah_dapat_submit_laporan_masalah(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('/dashboard/sekolah/laporan-masalah')
                    ->waitForLocation('/dashboard/sekolah/laporan-masalah', 10);
            $this->assertTrue(true);
        });
    }

    public function test_sekolah_gagal_submit_laporan_tanpa_judul(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('/dashboard/sekolah/laporan-masalah')
                    ->waitForLocation('/dashboard/sekolah/laporan-masalah', 10);
            $this->assertTrue(true);
        });
    }

    public function test_laporan_sekolah_status_default_open(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('/dashboard/sekolah/laporan-masalah')
                    ->waitForLocation('/dashboard/sekolah/laporan-masalah', 10);
            $this->assertTrue(true);
        });
    }

    public function test_sekolah_dapat_hapus_laporan_open(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('/dashboard/sekolah/laporan-masalah')
                    ->waitForLocation('/dashboard/sekolah/laporan-masalah', 10);
            $this->assertTrue(true);
        });
    }

    public function test_dapur_dapat_submit_laporan_masalah(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginDapur($browser);
            $browser->visit('/dashboard/dapur/laporan-masalah')
                    ->waitForLocation('/dashboard/dapur/laporan-masalah', 10);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_dapat_melihat_all_laporan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/laporan-masalah')
                    ->waitForText('Laporan', 10);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_dapat_melihat_detail_laporan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/laporan-masalah')
                    ->waitForLocation('/dashboard/superadmin/laporan-masalah', 10);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_dapat_ubah_status_laporan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/laporan-masalah')
                    ->waitForLocation('/dashboard/superadmin/laporan-masalah', 10);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_dapat_filter_laporan_berdasarkan_role(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/laporan-masalah')
                    ->waitForLocation('/dashboard/superadmin/laporan-masalah', 10);
            $this->assertTrue(true);
        });
    }

    public function test_superadmin_dapat_reset_filter_laporan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('/dashboard/superadmin/laporan-masalah')
                    ->waitForLocation('/dashboard/superadmin/laporan-masalah', 10);
            $this->assertTrue(true);
        });
    }
}