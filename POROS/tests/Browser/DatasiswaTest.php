<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;

class DatasiswaTest extends DuskTestCase
{
    
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
        $this->setUpUser(); 

        $this->browse(function (Browser $browser) {
            
            $browser->visit('/login')
                    ->type('email', 'sekolah@poros.com')
                    ->type('password', 'password123')
                    ->press('Masuk ke Dashboard')
                    ->waitForLocation('/dashboard')
                    ->visit('/dashboard/sekolah/siswas')
                    ->waitForText('Data Siswa');
            
             
            $browser->script("document.querySelector('button.btn-primary').click();");

            
            $browser->waitFor('#addSiswaModal')
                    ->pause(1500)
                    ->type('nama_siswa', 'Dusk Test Student')
                    ->type('nisn', '99' . rand(10000000, 99999999)) 
                    ->type('kelas', '1A')
                    ->type('contact', '08123456789')
                    ->type('alergi', 'Peanut')
                    ->select('status', 'Active')
                    ->click('#addSiswaModal button[type="submit"]')
                    ->waitForText('Data siswa berhasil ditambahkan.', 15)
                    ->assertSee('Dusk Test Student');
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
                    ->waitFor('table.user-table tbody tr')
                    ->click('button[title="View"]')
                    ->waitFor('#viewSiswaModal')
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
                    ->waitFor('table.user-table')
                    ->click('button[title="Edit"]') 
                    ->waitFor('#editSiswaModal')
                    ->pause(1000)
                    ->type('#edit_siswa_nama', 'Budi Santoso Updated')
                    ->click('#editSiswaModal button[type="submit"]')
                    ->waitForText('Data siswa berhasil diperbarui.')
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
                    ->waitFor('table.user-table');
                    
                    
                    $namaSiswa = $browser->text('table.user-table tbody tr:first-child td:first-child div div:last-child');

                    
                    $browser->click('button[title="Hapus"]')
                    
                    
                    ->waitFor('#deleteModal')
                    ->pause(1000)
                    ->press('Ya, Hapus')
                    ->waitForText('Siswa berhasil dihapus.')
                    ->assertDontSee($namaSiswa);
        });
    }

    #[Test]
    public function test_pbi_25_tambah_siswa_negatif_nisn_duplikat(): void
    {
        $user = $this->setUpUser();
        $nisnDuplikat = '1234567890'; 

        $this->browse(function (Browser $browser) use ($user, $nisnDuplikat) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/siswas')
                    ->waitForText('Data Siswa');
            
            
            $browser->script("document.querySelector('button.btn-primary').click();");

            
            $browser->waitFor('#addSiswaModal')
                    ->pause(1000)
                    ->type('nama_siswa', 'Siswa Duplikat')
                    ->type('nisn', $nisnDuplikat)
                    ->type('kelas', '1A')
                    ->click('#addSiswaModal button[type="submit"]')
                    ->waitForText('The nisn has already been taken.')
                    ->assertSee('The nisn has already been taken.');
        });
   
    }
}