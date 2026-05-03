<?php

namespace Tests\Browser\Dapur\MealPlanning;

use App\Models\Menu;
use App\Models\User;
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

    /**
     * A. Skenario Akses & Otorisasi
     */
    #[Test]
    public function test_guest_redirect(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/dashboard/dapur/meal-planning')
                    ->assertPathIs('/login');
        });
    }

    #[Test]
    public function test_admin_access(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->assertSee('Meal Planning')
                    ->assertSee('Kalender Menu Mingguan');
        });
    }

    /**
     * B. Skenario Navigasi Kalender
     */
    #[Test]
    public function test_calendar_nav(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning');

            $initialDate = $browser->text('.week-grid .day-card:first-child .day-date');

            $browser->click('a[href*="week=1"]')
                    ->waitFor('.week-grid');

            $nextDate = $browser->text('.week-grid .day-card:first-child .day-date');
            $this->assertNotEquals($initialDate, $nextDate);

            $browser->click('a[href*="week=0"]')
                    ->waitFor('.week-grid');

            $prevDate = $browser->text('.week-grid .day-card:first-child .day-date');
            $this->assertEquals($initialDate, $prevDate);
        });
    }

    /**
     * C. Skenario Pembuatan Jadwal (Create)
     */
    #[Test]
    public function test_add_schedule_success(): void
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
    public function test_add_invalid_portion(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.add-menu-link')
                    ->waitFor('#scheduleModal')
                    ->select('menu_id', $menu->id)
                    ->type('#schedulePortionInput', '-10')
                    ->press('Jadwalkan')
                    ->pause(500)
                    ->assertVisible('#scheduleModal');
        });
    }

    /**
     * D. Skenario Edit Jadwal (Update)
     */
    #[Test]
    public function test_edit_portion(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.add-menu-link')
                    ->waitFor('#scheduleModal')
                    ->select('menu_id', $menu->id)
                    ->type('#schedulePortionInput', '100')
                    ->press('Jadwalkan')
                    ->waitUntilMissing('#scheduleModal');

            $browser->click('.btn-edit')
                    ->waitFor('#editModal')
                    ->type('#editPortionInput', '250')
                    ->press('Simpan Perubahan')
                    ->waitUntilMissing('#editModal')
                    ->assertSee('250 porsi');
        });
    }

    /**
     * E. Skenario Hapus Jadwal (Delete)
     */
    #[Test]
    public function test_delete_success(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.add-menu-link')
                    ->waitFor('#scheduleModal')
                    ->select('menu_id', $menu->id)
                    ->type('total_target_porsi', '100')
                    ->press('Jadwalkan')
                    ->waitUntilMissing('#scheduleModal');

            $browser->click('.btn-del')
                    ->acceptDialog()
                    ->waitForReload()
                    ->assertDontSee('100 porsi');
        });
    }
}
