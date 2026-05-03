<?php

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

    /**
     * Setup environment dan data awal.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * ═══ PBI #14: MELIHAT DAFTAR MENU MINGGUAN ═══
     */

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

            // Klik Tombol View (Read)
            $browser->waitFor('.btn-view')
                    ->click('.btn-view')
                    ->waitFor('#viewScheduleModal', 10)
                    ->assertSee('Detail Jadwal Menu');
        });
    }

    /**
     * ═══ PBI #13: MEMASUKKAN RESEP DAN JADWAL MENU ═══
     */

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
                    ->click('.btn-outline') // + Add New Menu
                    ->waitFor('#addMenuModal')
                    ->type('nama_menu', 'Nasi Goreng Dusk')
                    // Karena menggunakan searchable select custom, kita isi hidden inputnya langsung
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
                    ->type('#schedulePortionInput', '-5') // Negatif
                    ->press('Jadwalkan')
                    ->assertPathIs('/dashboard/dapur/meal-planning');
        });
    }

    /**
     * ═══ PBI #15: MEMPERBARUI ISI RESEP ATAU MENU ═══
     */

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
