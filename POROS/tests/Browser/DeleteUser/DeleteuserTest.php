<?php

namespace Tests\Browser\Deleteuser;

use App\Models\User;
use App\Models\Role;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;

class DeleteuserTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test PBI #22 - Delete Data Kurir & Sekolah (Via User Management)
     */
    #[Test]
    public function test_create_and_delete_user(): void
    {
        $this->browse(function (Browser $browser) {
            $dapurRole = Role::where('nama_role', 'dapur')->first();
            
            // Login menggunakan admin@poros.com (Super Admin)
            // Catatan: PBI menyebutkan login dengan dapur@poros.com, namun fitur Create/Delete User 
            // hanya tersedia untuk Role Super Admin pada rute /dashboard/superadmin/users.
            $browser->visit('/login')
                    ->type('email', 'admin@poros.com')
                    ->type('password', 'password123')
                    ->press('Masuk ke Dashboard')
                    ->waitForLocation('/dashboard/superadmin/users', 10);

            // Membuka modal Add New User
            $browser->waitForText('User Management')
                    ->click("button[onclick=\"openAddModal('users')\"]")
                    ->waitFor('#addUserModal', 5)
                    ->pause(500) // Tunggu modal animasi
                    ->type('nama_lengkap', 'Dummy User')
                    ->type('email', 'dummy@poros.com')
                    ->type('password', 'password123')
                    ->type('no_telp', '081234567890')
                    ->type('lokasi', 'Jakarta')
                    ->select('role_id', (string) $dapurRole->id)
                    ->select('status', 'Active')
                    ->press('Create System User')
                    ->waitForText('User baru berhasil ditambahkan.')
                    ->assertSee('User baru berhasil ditambahkan.');

            // Ambil data user yang baru dibuat
            $dummyUser = User::where('email', 'dummy@poros.com')->first();
            $this->assertNotNull($dummyUser);

            // Lanjut ke PBI #22 - Delete Data
            // Cari user tersebut agar pasti muncul di layar
            $browser->type('search', 'Dummy User')
                    ->keys('.search-input', '{enter}')
                    ->pause(1000)
                    ->waitForText('Dummy User');

            // Klik icon trash berdasarkan onclick yang memanggil openDeleteModal
            $browser->click("button[onclick*=\"openDeleteModal('user', {$dummyUser->id}\"]")
                    ->waitFor('#deleteUserModal', 5)
                    ->pause(500)
                    ->waitForText('Hapus User?')
                    ->press('Ya, Hapus')
                    ->waitForText('User berhasil dihapus.')
                    ->assertSee('User berhasil dihapus.');
        });
    }
}
