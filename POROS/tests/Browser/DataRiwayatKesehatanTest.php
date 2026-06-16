<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;

class RiwayatKesehatanTest extends DuskTestCase
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

    
    // PBI #28: Hapus Data Siswa (Bulk Delete)
    #[Test]
    public function test_pbi_28_hapus_data_siswa_terpilih(): void
    {
        $user = $this->setUpUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/siswas')
                    ->waitForText('Data Siswa', 10);

            
            $browser->script("document.querySelector('table tbody tr:first-child input[type=\"checkbox\"]').click();");

            
            $browser->waitForText('dipilih', 5) 
                    ->press('Hapus Terpilih')
                    ->pause(1000) 
                    ->acceptDialog() 
                    ->waitForText('berhasil', 10)
                    ->assertSee('berhasil');
        });
    }
    
    // PBI #29: Import BB/TB (Positive Case)
    #[Test]
    public function test_pbi_29_import_bbtb_berhasil(): void
    {
        $user = $this->setUpUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/riwayat-kesehatan')
                    ->waitForText('Riwayat Kesehatan', 10);

            $browser->script("
                let btns = document.querySelectorAll('button, a');
                for(let btn of btns) {
                    if(btn.textContent.includes('Import BB/TB (CSV)')) {
                        btn.click();
                        break;
                    }
                }
            "); 

            $browser->waitForText('Import Hasil Timbangan BB/TB', 5) 
                    ->pause(1000)
                    ->attach('file_csv', __DIR__.'/files/dummy_bbtb_valid.csv') 
                    ->press('Unggah & Import') 
                    ->waitForText('Berhasil mengimpor', 15) 
                    ->assertSee('Berhasil mengimpor'); 
        });
    }

    
    // PBI #29: Import BB/TB (Negative Case)
    #[Test]
    public function test_pbi_29_import_bbtb_gagal_nisn_tidak_terdaftar(): void
    {
        $user = $this->setUpUser();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/riwayat-kesehatan')
                    ->waitForText('Riwayat Kesehatan', 10);

            $browser->script("
                let btns = document.querySelectorAll('button, a');
                for(let btn of btns) {
                    if(btn.textContent.includes('Import BB/TB (CSV)')) {
                        btn.click();
                        break;
                    }
                }
            "); 

            $browser->waitForText('Import Hasil Timbangan BB/TB', 5) 
                    ->pause(1000)
                    ->attach('file_csv', __DIR__.'/files/dummy_bbtb_invalid.csv') 
                    ->press('Unggah & Import') 
                    ->waitForText('Beberapa baris gagal diimpor', 15) 
                    ->assertSee('tidak ditemukan di sekolah Anda'); 
        });
    }

   
// PBI #30: Lihat & Filter Riwayat Kesehatan
    #[Test]
    public function test_pbi_30_lihat_dan_filter_riwayat_kesehatan(): void
    {
        $user = $this->setUpUser();
        
        
        $siswa = \App\Models\Siswa::withTrashed()->firstOrNew(['nisn' => '555666777']);
        $siswa->nama_siswa = 'Budi Testing';
        
        $siswa->sekolah_id = $user->sekolah_id ?? 1; 
        $siswa->kelas = '10A';
        $siswa->status = 'Active';
        $siswa->deleted_at = null; 
        $siswa->save();

        \App\Models\Antropometri::updateOrCreate(
            ['siswa_id' => $siswa->id],
            [
                'berat_badan' => 55.5,
                'tinggi_badan' => 165.0,
                'imt' => 20.38,
                'status_gizi' => 'Normal',
                'tanggal_ukur' => now()->format('Y-m-d')
            ]
        );
        
        $namaPencarian = 'Budi Testing'; 

        
        $this->browse(function (Browser $browser) use ($user, $namaPencarian) {
            $browser->loginAs($user)
                    ->visit('/dashboard/sekolah/riwayat-kesehatan')
                    ->waitForText('Riwayat Kesehatan', 10)
                    ->assertSee('NAMA SISWA') 
                    ->assertSee('BERAT BADAN')
                    ->assertSee('IMT (BMI)');
            
            $browser->type('input[placeholder="Nama atau NISN..."]', $namaPencarian)
                    ->press('Filter')
                    ->pause(2000) 
                    ->assertSee($namaPencarian)
                    ->assertVisible('table tbody tr:first-child'); 
        });
    }
}