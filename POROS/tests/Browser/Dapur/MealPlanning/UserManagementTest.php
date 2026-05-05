<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\Menu;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;

class UserManagementTest extends DuskTestCase
{
    /**
     * Test PBI #19, #20, dan #21: Manajemen Kurir dan Sekolah oleh Admin Dapur.
     */
    public function test_admin_can_manage_kurir_and_sekolah()
    {
        $this->browse(function (Browser $browser) {
            // Membuat email unik menggunakan time() agar test bisa dijalankan berulang kali 
            // tanpa terkena error validasi "email has already been taken"
            $uniqueEmail = 'kurir.' . time() . '@poros.com';

            // ==========================================
            // PRE-CONDITION: Login Admin
            // ==========================================
            $browser->visit('/login') 
                    ->type('email', 'admin@poros.com') 
                    ->type('password', 'password123') 
                    ->press('Masuk ke Dashboard')
                    // Menunggu proses redirect URL dari sistem selesai
                    ->waitForLocation('/dashboard', 5); 

            // ==========================================
            // PBI #19: Mendaftarkan kurir dan alamat sekolah
            // ==========================================
            $browser->clickLink('Users Management') 
                    ->waitForText('User Management', 5)
                    ->press('Add New User') 
                    ->waitForText('Add System User', 5) 
                    
                    // Mengisi form (pastikan name-nya sesuai dengan HTML)
                    ->type('nama_lengkap', 'Kurir Sekolah Bojongsoang')
                    ->type('email', $uniqueEmail)
                    ->type('password', 'password123')
                    ->type('no_telp', '081234567890') 
                    ->type('lokasi', 'Jl. Terusan Buah Batu, Bojongsoang')
                    ->select('role', 'Sekolah') 
                    ->select('status', 'Active') 
                    
                    ->press('Create System User') 
                    
                    // Menunggu notifikasi sukses dari sistem
                    ->waitForText('User baru berhasil ditambahkan.', 5);

            // ==========================================
            // PBI #20: Melihat daftar kurir dan sekolah
            // ==========================================
            // Memastikan data yang baru diinputkan langsung muncul di tabel daftar user
            $browser->type('input[placeholder="Search users..."]', $uniqueEmail)
                    ->pause(1500) // Tunggu 1.5 detik biar tabelnya selesai filter hasil pencarian
        
        // Baru kita pastikan datanya muncul
                    ->waitForText('Kurir Sekolah Bojongsoang', 5)
                    ->waitForText($uniqueEmail, 5);

            // ==========================================
            // PBI #21: Mengubah data kurir atau sekolah (Nomor Telepon)
            // ==========================================
            // Mengeklik tombol Edit (berdasarkan title) pada baris TERAKHIR (last-child) di tabel
            $browser->click('table tbody tr:last-child button[title="Edit"]')
        ->pause(1500); // Menunggu animasi modal muncul

// 2. Gunakan ID modal yang benar: #editUserModal
$browser->with('#editUserModal', function ($modal) {
    $modal->clear('no_telp')
          ->type('no_telp', '089998887776')
          ->press('Simpan Perubahan');
});

// 3. Verifikasi sukses setelah modal tertutup
$browser->waitForText('User berhasil diperbarui.', 5)
        ->assertSee('089998887776');
        });
    }
}