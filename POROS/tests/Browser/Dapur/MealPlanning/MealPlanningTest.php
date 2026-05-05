<?php

use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\Menu;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseMigrations;

uses(DatabaseMigrations::class);

beforeEach(function () {
    $this->artisan('db:seed');
});

/**
 * A. Skenario Akses & Otorisasi
 */
test('test_guest_redirect', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/dashboard/dapur/meal-planning')
                ->assertPathIs('/login');
    });
});

test('test_admin_access', function () {
    $this->browse(function (Browser $browser) {
        $user = User::where('email', 'dapur@poros.com')->first();
        $browser->loginAs($user)
                ->visit('/dashboard/dapur/meal-planning')
                ->assertSee('Meal Planning')
                ->assertSee('Kalender Menu Mingguan');
    });
});

/**
 * B. Skenario Navigasi Kalender
 */
test('test_calendar_nav', function () {
    $this->browse(function (Browser $browser) {
        $user = User::where('email', 'dapur@poros.com')->first();
        $browser->loginAs($user)
                ->visit('/dashboard/dapur/meal-planning');

        $initialDate = $browser->text('.week-grid .day-card:first-child .day-date');
        
        $browser->clickLink('→')
                ->pause(1000);
        
        $nextDate = $browser->text('.week-grid .day-card:first-child .day-date');
        expect($nextDate)->not->toBe($initialDate);

        $browser->clickLink('←')
                ->pause(1000);
        
        $prevDate = $browser->text('.week-grid .day-card:first-child .day-date');
        expect($prevDate)->toBe($initialDate);
    });
});

/**
 * C. Skenario Pembuatan Jadwal (Create)
 */
test('test_add_schedule_success', function () {
    $this->browse(function (Browser $browser) {
        $user = User::where('email', 'dapur@poros.com')->first();
        $menu = Menu::first();

        $browser->loginAs($user)
                ->visit('/dashboard/dapur/meal-planning')
                ->click('.add-menu-link') // Klik tombol tambah di hari pertama
                ->pause(500)
                ->select('menu_id', $menu->id)
                ->type('#schedulePortionInput', '150')
                ->press('Jadwalkan')
                ->pause(1000)
                ->assertSee($menu->nama_menu)
                ->assertSee('150 porsi');
    });
});

test('test_add_invalid_portion', function () {
    $this->browse(function (Browser $browser) {
        $user = User::where('email', 'dapur@poros.com')->first();
        $menu = Menu::first();

        $browser->loginAs($user)
                ->visit('/dashboard/dapur/meal-planning')
                ->click('.add-menu-link')
                ->pause(500)
                ->select('menu_id', $menu->id)
                ->type('#schedulePortionInput', '-10')
                ->press('Jadwalkan')
                ->pause(500)
                // Karena validasi menggunakan atribut HTML 'min="1"', 
                // form tidak akan terkirim. Kita bisa cek apakah modal masih terbuka.
                ->assertVisible('#scheduleModal');
    });
});

/**
 * D. Skenario Edit Jadwal (Update)
 */
test('test_edit_portion', function () {
    $this->browse(function (Browser $browser) {
        $user = User::where('email', 'dapur@poros.com')->first();
        $menu = Menu::first();

        // Setup awal: buat jadwal dulu
        $browser->loginAs($user)
                ->visit('/dashboard/dapur/meal-planning')
                ->click('.add-menu-link')
                ->pause(500)
                ->select('menu_id', $menu->id)
                ->type('#schedulePortionInput', '100')
                ->press('Jadwalkan')
                ->pause(1000);

        // Aksi Edit
        $browser->click('.btn-edit')
                ->pause(500)
                ->type('#editPortionInput', '250')
                ->press('Simpan Perubahan')
                ->pause(1000)
                ->assertSee('250 porsi');
    });
});

/**
 * E. Skenario Hapus Jadwal (Delete)
 */
test('test_delete_success', function () {
    $this->browse(function (Browser $browser) {
        $user = User::where('email', 'dapur@poros.com')->first();
        $menu = Menu::first();

        // Setup awal: buat jadwal
        $browser->loginAs($user)
                ->visit('/dashboard/dapur/meal-planning')
                ->click('.add-menu-link')
                ->pause(500)
                ->select('menu_id', $menu->id)
                ->type('total_target_porsi', '100')
                ->press('Jadwalkan')
                ->pause(1000);

        // Aksi Hapus
        $browser->click('.btn-del')
                ->acceptDialog() // Menangani confirm('Hapus jadwal ini?')
                ->pause(1000)
                ->assertDontSee('100 porsi');
    });
});
