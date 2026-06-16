<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;

class DatasiswaTest extends DuskTestCase
{
    /**
     * Setup user untuk testing
     */
    protected function setUpUser()
    {
        return User::updateOrCreate(
            ['email' => 'sekolah@poros.com'],
            [
                'nama_lengkap' => 'Petugas Sekolah', 
                'password' => bcrypt('password123'),
                'role_id' => 3 
            ]
        );
    }

    #[Test]
    public function test_pbi_25_tambah_siswa_dan_alergi(): void
    {
        $user = $this->setUpUser(); 

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/siswas')
                    ->waitForText('Data Siswa');
            
            // KITA KEMBALIKAN KE CARA LAMAMU YANG TERBUKTI SUKSES
            $browser->script("document.querySelector('button.btn-primary').click();");

            $browser->waitFor('#addSiswaModal', 5)
                    ->pause(1000)
                    ->type('nama_siswa', 'Dusk Test Student')
                    ->type('nisn', '99' . rand(1000000, 9999999)) 
                    ->type('kelas', '1A')
                    ->type('contact', '08123456789')
                    ->type('alergi', 'Peanut')
                    ->select('status', 'Active')
                    ->click('#addSiswaModal button[type="submit"]')
                    ->waitForText('Data siswa berhasil ditambahkan.', 10)
                    ->assertSee('Dusk Test Student');
        });
    }

    #[Test]
    public function test_pbi_25_import_data_siswa_csv(): void
    {
        $user = $this->setUpUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/siswas')
                    ->waitForText('Data Siswa');
            
            $browser->script("
                let btns = document.querySelectorAll('button, a');
                for(let btn of btns) {
                    if(btn.textContent.includes('Import CSV')) {
                        btn.click();
                        break;
                    }
                }
            "); 

            $browser->waitForText('Import Data Siswa (CSV)', 5) 
                    ->pause(1000)
                    ->attach('file_csv', __DIR__.'/files/dummy_siswa.csv') 
                    ->press('Unggah & Import') 
                    // REVISI: Menggunakan huruf 'B' kapital sesuai dengan notifikasi di UI
                    ->waitForText('Berhasil mengimpor', 15) 
                    ->assertSee('Berhasil mengimpor'); 
        });
    }

    #[Test]
    public function test_pbi_26_lihat_detail_siswa(): void
    {
        $user = $this->setUpUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/siswas')
                    ->waitForText('Data Siswa')
                    ->waitFor('table tbody tr', 5)
                    // REVISI: Memastikan kita mengklik tombol View pada baris pertama saja
                    ->click('table tbody tr:first-child button[title="View"]')
                    ->waitFor('#viewSiswaModal', 5)
                    ->pause(1000)
                    ->assertSeeIn('#viewSiswaModal', 'BIODATA & KONTAK')
                    ->assertSeeIn('#viewSiswaModal', 'DATA FISIK');
        });
    }

    #[Test]
    public function test_pbi_27_update_siswa(): void
    {
        $user = $this->setUpUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/siswas')
                    ->waitFor('table tbody tr', 5)
                    // REVISI: Mengklik tombol Edit pada baris pertama
                    ->click('table tbody tr:first-child button[title="Edit"]') 
                    ->waitFor('#editSiswaModal', 5)
                    ->pause(1000)
                    ->type('#edit_siswa_nama', 'Budi Santoso Updated')
                    ->click('#editSiswaModal button[type="submit"]')
                    ->waitForText('Data siswa berhasil diperbarui.', 10)
                    ->assertSee('Budi Santoso Updated');
        });
    }

    #[Test]
    public function test_pbi_27_delete_siswa(): void
    {
        $user = $this->setUpUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/siswas')
                    ->waitFor('table tbody tr', 5);
                    
            // REVISI: Menyederhanakan pengambilan nama siswa agar tidak error 'NoSuchElement'
            // Kita asumsikan nama siswa ada di dalam baris pertama.
            $namaSiswa = $browser->text('table tbody tr:first-child td:nth-child(2)'); // Sesuaikan nth-child dengan urutan kolom nama
                    
            // Mengklik tombol hapus di baris pertama
            $browser->click('table tbody tr:first-child button[title="Hapus"]')
                    ->waitFor('#deleteModal', 5)
                    ->pause(1000)
                    ->press('Ya, Hapus')
                    ->waitForText('Siswa berhasil dihapus.', 10)
                    ->assertDontSee($namaSiswa);
        });
    }

    #[Test]
    public function test_pbi_25_tambah_siswa_negatif_nisn_duplikat(): void
    {
        $user = $this->setUpUser();
        $nisnDuplikat = '786998767'; // Pastikan NISN ini sudah ada di databasemu

        $this->browse(function (Browser $browser) use ($user, $nisnDuplikat) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/siswas')
                    ->waitForText('Data Siswa');
            
            // KITA KEMBALIKAN KE CARA LAMAMU
            $browser->script("document.querySelector('button.btn-primary').click();");

            $browser->waitFor('#addSiswaModal', 5)
                    ->pause(1000)
                    ->type('nama_siswa', 'Siswa Duplikat')
                    ->type('nisn', $nisnDuplikat)
                    ->type('kelas', '1A')
                    ->click('#addSiswaModal button[type="submit"]')
                    ->waitForText('The nisn has already been taken.', 10) 
                    ->assertSee('The nisn has already been taken.');
        });
    }
}