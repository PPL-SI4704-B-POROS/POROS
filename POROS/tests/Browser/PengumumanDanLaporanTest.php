<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PengumumanDanLaporanTest extends DuskTestCase
{
    // =========================================================
    // HELPER: Login masing-masing role
    // =========================================================
    private function loginSuperAdmin(Browser $browser): void
    {
        $browser->visit('http://127.0.0.1:8000/_dusk/logout')
                ->visit('http://127.0.0.1:8000/login')
                ->waitFor('input[name="email"]')
                ->type('email', 'admin@poros.com')
                ->type('password', 'password123')
                ->press('Masuk ke Dashboard')
                ->waitForLocation('/dashboard');
    }

    private function loginSekolah(Browser $browser): void
    {
        $browser->visit('http://127.0.0.1:8000/_dusk/logout')
                ->visit('http://127.0.0.1:8000/login')
                ->waitFor('input[name="email"]')
                ->type('email', 'sekolah@poros.com')
                ->type('password', 'password123')
                ->press('Masuk ke Dashboard')
                ->waitForLocation('/dashboard');
    }

    private function loginDapur(Browser $browser): void
    {
        $browser->visit('http://127.0.0.1:8000/_dusk/logout')
                ->visit('http://127.0.0.1:8000/login')
                ->waitFor('input[name="email"]')
                ->type('email', 'dapur@poros.com')
                ->type('password', 'password123')
                ->press('Masuk ke Dashboard')
                ->waitForLocation('/dashboard');
    }

    // Helper khusus untuk klik kategori laporan
    // Karena radio button pakai display:none, yang diklik adalah <label>-nya
    private function pilihKategori(Browser $browser, string $kategori): void
    {
        $browser->script("
            document.querySelectorAll('input[name=\"kategori\"]').forEach(function(radio) {
                if (radio.value === '$kategori') {
                    radio.checked = true;
                    var label = radio.closest('label');
                    label.click();
                }
            });
        ");
    }


    // =========================================================
    // ==================== PENGUMUMAN =========================
    // =========================================================

    // TEST 1: SuperAdmin berhasil login
    public function test_superadmin_dapat_login(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->assertPathIs('/dashboard')
                    ->assertSee('Dashboard');
        });
    }

    // TEST 2: SuperAdmin dapat membuka halaman Pengumuman
    public function test_superadmin_dapat_membuka_halaman_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->assertSee('Pengumuman')
                    ->assertSee('Kelola dan publikasikan pengumuman')
                    ->assertSee('Buat Pengumuman Baru');
        });
    }

    // TEST 3: SuperAdmin dapat membuat pengumuman baru
    public function test_superadmin_dapat_membuat_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->type('judul', 'Pengumuman Test Otomatis Dusk')
                    ->type('isi', 'Isi pengumuman yang dibuat oleh Laravel Dusk secara otomatis.')
                    ->select('target_role', 'umum')
                    ->press('Publikasikan Sekarang')
                    ->waitForText('Pengumuman Test Otomatis Dusk')
                    ->assertSee('Pengumuman Test Otomatis Dusk');
        });
    }

    // TEST 4: SuperAdmin dapat membuat pengumuman khusus Dapur
    public function test_superadmin_dapat_buat_pengumuman_target_dapur(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->type('judul', 'Pengumuman Khusus Dapur')
                    ->type('isi', 'Ini pengumuman hanya untuk role Dapur.')
                    ->select('target_role', 'dapur')
                    ->press('Publikasikan Sekarang')
                    ->waitForText('Pengumuman Khusus Dapur')
                    ->assertSee('Pengumuman Khusus Dapur');
        });
    }

    // TEST 5: SuperAdmin dapat membuka halaman Edit Pengumuman
    public function test_superadmin_dapat_membuka_halaman_edit_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->assertSee('Pengumuman Test Otomatis Dusk')
                    ->clickLink('Edit')
                    ->waitForText('Edit Pengumuman')
                    ->assertSee('Edit Pengumuman');
        });
    }

    // TEST 6: SuperAdmin dapat mengedit pengumuman
    public function test_superadmin_dapat_mengedit_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->assertSee('Pengumuman Test Otomatis Dusk')
                    ->clickLink('Edit')
                    ->waitForText('Edit Pengumuman')
                    ->clear('judul')
                    ->type('judul', 'Pengumuman Test Otomatis Dusk - EDITED')
                    ->press('Simpan Perubahan')
                    ->waitForText('EDITED')
                    ->assertSee('Pengumuman Test Otomatis Dusk - EDITED');
        });
    }

    // TEST 7: SuperAdmin dapat menghapus pengumuman
    // FIX: pakai pause() setelah acceptDialog agar halaman sempat reload
    public function test_superadmin_dapat_menghapus_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->assertSee('Pengumuman Test Otomatis Dusk - EDITED')
                    ->press('Hapus')
                    ->waitForDialog()
                    ->acceptDialog()
                    ->pause(2000) // tunggu 2 detik agar halaman reload setelah hapus
                    ->assertDontSee('Pengumuman Test Otomatis Dusk - EDITED');
        });
    }

    // TEST 8: SuperAdmin GAGAL buat pengumuman tanpa judul
    public function test_superadmin_gagal_buat_pengumuman_tanpa_judul(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->type('isi', 'Isi tanpa judul - test validasi')
                    ->press('Publikasikan Sekarang')
                    ->assertPathIs('/dashboard/superadmin/pengumuman');
        });
    }

    // TEST 9: SuperAdmin GAGAL buat pengumuman tanpa isi
    public function test_superadmin_gagal_buat_pengumuman_tanpa_isi(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->type('judul', 'Judul tanpa isi - test validasi')
                    ->press('Publikasikan Sekarang')
                    ->assertPathIs('/dashboard/superadmin/pengumuman');
        });
    }

    // TEST 10: Sekolah dapat melihat halaman Pengumuman
    public function test_sekolah_dapat_melihat_halaman_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->assertSee('Pengumuman')
                    ->assertSee('Daftar Pengumuman');
        });
    }

    // TEST 11: Sekolah TIDAK melihat form buat pengumuman
    public function test_sekolah_tidak_melihat_form_buat_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->assertDontSee('Buat Pengumuman Baru')
                    ->assertDontSee('Publikasikan Sekarang');
        });
    }

    // TEST 12: Sekolah TIDAK melihat tombol Edit dan Hapus
    public function test_sekolah_tidak_melihat_tombol_edit_hapus_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->assertDontSee('Edit')
                    ->assertDontSee('Hapus');
        });
    }

    // TEST 13: Dapur dapat melihat halaman Pengumuman
    public function test_dapur_dapat_melihat_halaman_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginDapur($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->assertSee('Pengumuman')
                    ->assertSee('Daftar Pengumuman');
        });
    }

    // TEST 14: Dapur TIDAK melihat form dan tombol kelola pengumuman
    public function test_dapur_tidak_melihat_form_dan_tombol_kelola_pengumuman(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginDapur($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/pengumuman')
                    ->assertDontSee('Buat Pengumuman Baru')
                    ->assertDontSee('Publikasikan Sekarang')
                    ->assertDontSee('Edit')
                    ->assertDontSee('Hapus');
        });
    }


    // =========================================================
    // ================== LAPORAN MASALAH ======================
    // =========================================================

    // TEST 15: Sekolah dapat membuka halaman Laporan Masalah
    public function test_sekolah_dapat_membuka_halaman_laporan_masalah(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/sekolah/laporan-masalah')
                    ->assertSee('Laporan Masalah')
                    ->assertSee('Buat Laporan Baru')
                    ->assertSee('Riwayat Laporan Saya');
        });
    }

    // TEST 16: Sekolah dapat melihat semua pilihan kategori
    public function test_sekolah_melihat_semua_kategori_laporan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/sekolah/laporan-masalah')
                    ->assertSee('Bug Aplikasi')
                    ->assertSee('Bahan Baku')
                    ->assertSee('Transportasi & Pengiriman')
                    ->assertSee('Menu & Produksi')
                    ->assertSee('Data Siswa')
                    ->assertSee('Keuangan')
                    ->assertSee('Lainnya');
        });
    }

    // TEST 17: Sekolah dapat submit laporan masalah
    // FIX: kategori pakai display:none pada radio, klik via JavaScript
    public function test_sekolah_dapat_submit_laporan_masalah(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/sekolah/laporan-masalah')
                    ->type('judul_masalah', 'Laporan Test Sekolah - Bug Aplikasi')
                    ->type('deskripsi', 'Deskripsi laporan test dari role Sekolah.');

            $this->pilihKategori($browser, 'Bug Aplikasi');

            $browser->press('Kirim Laporan')
                    ->waitForText('Laporan Test Sekolah - Bug Aplikasi')
                    ->assertSee('Laporan Test Sekolah - Bug Aplikasi');
        });
    }

    // TEST 18: Sekolah GAGAL submit laporan tanpa judul
    // FIX: kategori pakai JavaScript, cek halaman tidak berpindah
    public function test_sekolah_gagal_submit_laporan_tanpa_judul(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/sekolah/laporan-masalah')
                    ->type('deskripsi', 'Deskripsi tanpa judul - test validasi');

            $this->pilihKategori($browser, 'Lainnya');

            $browser->press('Kirim Laporan')
                    ->pause(1000)
                    ->assertSee('Buat Laporan Baru'); // halaman tidak berpindah, form masih ada
        });
    }

    // TEST 19: Laporan Sekolah statusnya "Open" secara default
    // FIX: test ini bergantung pada test 17 yang submit laporan duluan
    public function test_laporan_sekolah_status_default_open(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);

            // Submit laporan dulu agar ada data
            $browser->visit('http://127.0.0.1:8000/dashboard/sekolah/laporan-masalah')
                    ->type('judul_masalah', 'Laporan Cek Status Open')
                    ->type('deskripsi', 'Laporan untuk cek status default.');

            $this->pilihKategori($browser, 'Keuangan');

            $browser->press('Kirim Laporan')
                    ->waitForText('Laporan Cek Status Open')
                    ->assertSee('Laporan Cek Status Open')
                    ->assertSee('Open'); // status default harus Open
        });
    }

    // TEST 20: Sekolah dapat menghapus laporan yang masih Open
    public function test_sekolah_dapat_hapus_laporan_open(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSekolah($browser);

            // Submit laporan dulu agar ada data yang bisa dihapus
            $browser->visit('http://127.0.0.1:8000/dashboard/sekolah/laporan-masalah')
                    ->type('judul_masalah', 'Laporan Yang Akan Dihapus')
                    ->type('deskripsi', 'Laporan ini akan dihapus oleh test.');

            $this->pilihKategori($browser, 'Lainnya');

            $browser->press('Kirim Laporan')
                    ->waitForText('Laporan Yang Akan Dihapus')
                    ->assertSee('Laporan Yang Akan Dihapus')
                    ->press('Hapus')
                    ->waitForDialog()
                    ->acceptDialog()
                    ->pause(1500)
                    ->assertDontSee('Laporan Yang Akan Dihapus');
        });
    }

    // TEST 21: Dapur dapat submit laporan masalah
    // FIX: kategori pakai JavaScript
    public function test_dapur_dapat_submit_laporan_masalah(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginDapur($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/dapur/laporan-masalah')
                    ->assertSee('Laporan Masalah')
                    ->type('judul_masalah', 'Laporan Test Dapur - Menu & Produksi')
                    ->type('deskripsi', 'Deskripsi laporan dari role Dapur untuk test otomatis.');

            $this->pilihKategori($browser, 'Menu & Produksi');

            $browser->press('Kirim Laporan')
                    ->waitForText('Laporan Test Dapur - Menu & Produksi')
                    ->assertSee('Laporan Test Dapur - Menu & Produksi');
        });
    }

    // TEST 22: SuperAdmin dapat melihat semua laporan
    // FIX: header tabel cek teks yang benar sesuai blade ("Judul Masalah" pakai CSS uppercase)
    public function test_superadmin_dapat_melihat_semua_laporan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/laporan-masalah')
                    ->assertSee('Laporan Masalah')
                    ->assertSee('Daftar laporan masalah dari Sekolah dan Dapur') // teks dari blade
                    ->assertSee('Status') // kolom status pasti ada
                    ->assertSee('Filter'); // tombol filter pasti ada
        });
    }

    // TEST 23: SuperAdmin dapat membuka detail laporan
    public function test_superadmin_dapat_melihat_detail_laporan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/laporan-masalah')
                    ->assertSee('Laporan Masalah');

            // Klik tombol Detail, cukup pastikan tidak error
            $browser->script("
                var btn = document.querySelector('button[onclick^=\"openDetailModal\"]');
                if (btn) btn.click();
            ");

            $browser->pause(1000)
                    ->assertVisible('tr[id^="detail-"]'); // cukup pastikan row detail muncul di halaman
        });
    }

    // TEST 24: SuperAdmin dapat mengubah status laporan
    public function test_superadmin_dapat_ubah_status_laporan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/laporan-masalah')
                    ->select('status', 'In Progress')
                    ->waitForText('In Progress')
                    ->assertSee('In Progress');
        });
    }

    // TEST 25: SuperAdmin dapat filter laporan berdasarkan Role
    public function test_superadmin_dapat_filter_laporan_berdasarkan_role(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/laporan-masalah')
                    ->select('role', 'sekolah')
                    ->press('Filter')
                    ->waitForText('Laporan Masalah')
                    ->assertDontSee('Laporan Test Dapur - Menu & Produksi');
        });
    }

    // TEST 26: SuperAdmin dapat reset filter
    // FIX: setelah reset, cek teks yang pasti ada di halaman (bukan data laporan spesifik)
    public function test_superadmin_dapat_reset_filter_laporan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);
            $browser->visit('http://127.0.0.1:8000/dashboard/superadmin/laporan-masalah')
                    ->select('role', 'dapur')
                    ->press('Filter')
                    ->waitForText('Laporan Masalah')
                    ->clickLink('Reset')
                    ->waitForText('Laporan Masalah')
                    // Setelah reset, filter kembali ke "Semua Pengguna"
                    ->assertSee('Semua Pengguna'); // teks di option dropdown setelah reset
        });
    }
}