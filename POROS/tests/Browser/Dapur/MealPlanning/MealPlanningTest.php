<?php

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> 396b92a708d91f3669812074bcf1b6d964c079cb
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
<<<<<<< HEAD
=======
namespace Tests\Browser\Dapur\MealPlanning;

use App\Models\Menu;
use App\Models\User;
use App\Models\BahanBaku;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class MealPlanningTest extends DuskTestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function test_pbi14_tc01_view_dashboard_weekly(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->assertSee('Meal Planning')
                    ->assertSee('Kalender Menu Mingguan');
        });
    }

    #[Test]
    public function test_pbi14_tc02_calendar_navigation(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->clickLink('→')
                    ->assertQueryStringHas('week', '1')
                    ->clickLink('←')
                    ->assertQueryStringHas('week', '0');
        });
    }

    #[Test]
    public function test_pbi14_tc03_view_nutrition_detail(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.add-menu-link')
                    ->waitFor('#scheduleModal')
                    ->select('menu_id', $menu->id)
                    ->press('Jadwalkan')
                    ->waitUntilMissing('#scheduleModal');
            $browser->waitFor('.btn-view')
                    ->click('.btn-view')
                    ->waitFor('#viewScheduleModal', 10)
                    ->assertSee('Detail Jadwal Menu');
        });
    }

    #[Test]
    public function test_pbi13_tc01_add_schedule_positive(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.add-menu-link')
                    ->waitFor('#scheduleModal')
                    ->select('menu_id', $menu->id)
                    ->type('#schedulePortionInput', '150')
                    ->press('Jadwalkan')
                    ->waitUntilMissing('#scheduleModal')
                    ->assertSee($menu->nama_menu)
                    ->assertSee('150 porsi');
        });
    }

    #[Test]
    public function test_pbi13_tc02_add_new_menu_recipe_positive(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $bahan = BahanBaku::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.btn-outline')
                    ->waitFor('#addMenuModal')
                    ->type('nama_menu', 'Nasi Goreng Dusk')
                    ->keys('input[name="ingredients[0][gramasi]"]', '200')
                    ->script("document.querySelector('input[name=\"ingredients[0][bahan_id]\"]').value = '{$bahan->id}';");

            $browser->press('Simpan Menu')
                    ->waitForText('Nasi Goreng Dusk')
                    ->assertSee('Nasi Goreng Dusk');
        });
    }

    #[Test]
    public function test_pbi13_tc03_add_schedule_negative_invalid_portion(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.add-menu-link')
                    ->waitFor('#scheduleModal')
                    ->select('menu_id', $menu->id)
                    ->type('#schedulePortionInput', '-5')
                    ->press('Jadwalkan')
                    ->assertPathIs('/dashboard/dapur/meal-planning');
        });
    }

    #[Test]
    public function test_pbi15_tc01_edit_schedule_portion_positive(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.add-menu-link')
                    ->waitFor('#scheduleModal')
                    ->select('menu_id', $menu->id)
                    ->press('Jadwalkan')
                    ->waitUntilMissing('#scheduleModal');

            $browser->click('.btn-edit')
                    ->waitFor('#editScheduleModal')
                    ->clear('#editPortionInput')
                    ->type('#editPortionInput', '300')
                    ->press('Simpan Perubahan')
                    ->waitUntilMissing('#editScheduleModal')
                    ->assertSee('300 porsi');
        });
    }

    #[Test]
    public function test_pbi15_tc02_edit_portion_negative_zero(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.add-menu-link')
                    ->waitFor('#scheduleModal')
                    ->select('menu_id', $menu->id)
                    ->press('Jadwalkan')
                    ->waitUntilMissing('#scheduleModal');

            $browser->click('.btn-edit')
                    ->waitFor('#editScheduleModal')
                    ->clear('#editPortionInput')
                    ->type('#editPortionInput', '0')
                    ->press('Simpan Perubahan')
                    ->assertPathIs('/dashboard/dapur/meal-planning');
        });
    }

    #[Test]
    public function test_pbi15_tc03_delete_schedule_positive(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.add-menu-link')
                    ->waitFor('#scheduleModal')
                    ->select('menu_id', $menu->id)
                    ->press('Jadwalkan')
                    ->waitUntilMissing('#scheduleModal');

            $browser->click('.btn-del')
                    ->acceptDialog()
                    ->waitFor('.week-grid', 10)
                    ->with('.week-grid', function ($grid) use ($menu) {
                        $grid->assertDontSee($menu->nama_menu);
                    });
        });
    }
}
>>>>>>> 565012b3174e552472aac61174ed4943b451ebe5
=======
>>>>>>> 396b92a708d91f3669812074bcf1b6d964c079cb
