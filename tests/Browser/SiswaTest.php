<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Siswa;

class SiswaTest extends DuskTestCase
{
    /**
     * Test full Student Management CRUD operations.
     */
    public function test_siswa_management_crud(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'sekolah@poros.com')
                    ->type('password', 'password123')
                    ->press('Masuk ke Dashboard')
                    ->assertPathIs('/dashboard')
                    ->visit('/dashboard/sekolah/siswas')
                    ->assertSee('Data Siswa')
                    
                    // 1. Add Student
                    ->click('.planning-header button.btn-primary') // Klik tombol tambah di header
                    ->waitFor('#addSiswaModal')
                    ->type('nama_siswa', 'Dusk Test Student')
                    ->type('nisn', '1234567890')
                    ->type('kelas', '1A')
                    ->type('contact', '08123456789')
                    ->type('alergi', 'Peanut')
                    ->select('status', 'Active')
                    ->press('Tambah Siswa') // Dusk akan mencari tombol di dalam modal yang aktif
                    ->waitForText('Data siswa berhasil ditambahkan.')
                    ->assertSee('Dusk Test Student')
                    
                    // 2. View Student (The fix we applied earlier)
                    ->click('button[title="View"]') // Assuming title="View" is unique enough
                    ->waitFor('#viewSiswaModal')
                    ->assertSeeIn('#view_siswa_nama', 'Dusk Test Student')
                    ->assertSee('NISN')
                    ->press('Tutup')
                    ->waitUntilMissing('#viewSiswaModal')
                    
                    // 3. Edit Student
                    ->click('button[title="Edit"]')
                    ->waitFor('#editSiswaModal')
                    ->type('nama_siswa', 'Dusk Test Student Updated')
                    ->press('Simpan Perubahan')
                    ->waitForText('Data siswa berhasil diperbarui.')
                    ->assertSee('Dusk Test Student Updated')
                    
                    // 4. Record Anthropometry
                    ->click('button[title="Ukur Antropometri"]')
                    ->waitFor('#ukurModal')
                    ->type('berat_badan', '20')
                    ->type('tinggi_badan', '110')
                    ->press('Simpan Pengukuran')
                    ->waitForText('Data antropometri berhasil disimpan.')
                    
                    // 5. Delete Student
                    ->click('button[title="Hapus"]')
                    ->waitFor('#deleteModal')
                    ->press('Ya, Hapus')
                    ->waitForText('Siswa berhasil dihapus.')
                    ->assertDontSee('Dusk Test Student Updated');
        });
    }
}