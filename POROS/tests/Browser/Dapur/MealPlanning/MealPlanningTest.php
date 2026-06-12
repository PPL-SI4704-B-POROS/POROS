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

            $browser->click('button.btn-edit:not([disabled])')
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

            $browser->click('button.btn-edit:not([disabled])')
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

            $browser->click('button.btn-del:not([disabled])')
                    ->acceptDialog()
                    ->waitFor('.week-grid', 10)
                    ->with('.week-grid', function ($grid) use ($menu) {
                        $grid->assertDontSee($menu->nama_menu);
                    });
        });
    }

    #[Test]
    public function test_pbi16_tc01_delete_menu_positive(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.btn-delete-menu')
                    ->waitFor('#deleteConfirmModal')
                    ->click('.btn-confirm-delete')
                    ->pause(1500)
                    ->assertPathIs('/dashboard/dapur/meal-planning')
                    ->assertDontSee($menu->nama_menu);
        });
    }

    #[Test]
    public function test_pbi16_tc02_cancel_delete_menu(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();
            $menu = Menu::first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.btn-delete-menu')
                    ->waitFor('#deleteConfirmModal')
                    ->click('.btn-cancel')
                    ->waitUntilMissing('#deleteConfirmModal')
                    ->assertSee($menu->nama_menu);
        });
    }

    #[Test]
    public function test_pbi16_tc03_delete_menu_shows_modal(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->click('.btn-delete-menu')
                    ->waitFor('#deleteConfirmModal')
                    ->assertSee('Hapus Menu?');
        });
    }

    #[Test]
    public function test_pbi17_tc01_read_kalkulator_porsi_positive(): void
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
                    ->waitFor('#portionPreview')
                    ->assertSeeIn('#portionPreview', 'Berat / porsi')
                    ->assertSeeIn('#portionPreview', 'Kalori / porsi')
                    ->assertSeeIn('#portionPreview', 'Total Berat');
        });
    }

    #[Test]
    public function test_pbi17_tc02_kalkulator_porsi_update(): void
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
                    ->waitFor('#portionPreview')
                    ->assertSeeIn('#pvTotal', '100 porsi')
                    ->clear('#schedulePortionInput')
                    ->type('#schedulePortionInput', '200')
                    ->assertSeeIn('#pvTotal', '200 porsi');
        });
    }

    #[Test]
    public function test_pbi17_tc03_kalkulator_porsi_hidden_on_empty(): void
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
                    ->waitFor('#portionPreview')
                    ->select('menu_id', '')
                    ->waitUntilMissing('#portionPreview');
        });
    }

    #[Test]
    public function test_pbi18_tc01_read_total_modal_menu_library(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('email', 'dapur@poros.com')->first();

            $browser->loginAs($user)
                    ->visit('/dashboard/dapur/meal-planning')
                    ->assertSee('Modal: Rp');
        });
    }

    #[Test]
    public function test_pbi18_tc02_read_total_modal_schedule_calculator(): void
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
                    ->waitFor('#portionPreview')
                    ->assertSeeIn('#portionPreview', 'Estimasi Modal / porsi')
                    ->assertSeeIn('#portionPreview', 'Total Anggaran Modal');
        });
    }

    #[Test]
    public function test_pbi18_tc03_read_total_modal_view_schedule(): void
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
                    ->waitUntilMissing('#scheduleModal');

            $browser->click('.btn-view')
                    ->waitFor('#viewScheduleModal')
                    ->assertSeeIn('#viewScheduleModal', 'Modal: Rp')
                    ->assertSeeIn('#viewScheduleModal', 'Total Modal: Rp');
        });
    }
}