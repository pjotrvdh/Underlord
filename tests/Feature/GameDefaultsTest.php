<?php

namespace Tests\Feature;

use App\Models\Creature;
use App\Models\Setting;
use App\Models\User;
use App\Services\GameDefaults;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrating_fills_in_the_game_data(): void
    {
        $this->assertSame(5, Creature::where('side', Creature::SIDE_CREATURE)->count());
        $this->assertSame(1500, Setting::whereKey('economy.startGold')->value('value'));
    }

    public function test_sync_adds_missing_records_and_keeps_edits(): void
    {
        Setting::whereKey('economy.startGold')->update(['value' => 9000]);
        Creature::where('key', 'troll')->delete();

        app(GameDefaults::class)->sync();

        $this->assertSame(9000, Setting::whereKey('economy.startGold')->value('value'));
        $this->assertTrue(Creature::where('key', 'troll')->exists());
    }

    public function test_reset_puts_edits_back_to_the_defaults(): void
    {
        Setting::whereKey('economy.startGold')->update(['value' => 9000]);

        $this->artisan('underlord:defaults', ['--reset' => true])->expectsConfirmation('This overwrites every change made in the admin panel. Continue?', 'yes')->assertSuccessful();

        $this->assertSame(1500, Setting::whereKey('economy.startGold')->value('value'));
    }

    public function test_the_admin_panel_cannot_create_game_data(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        foreach (['settings', 'creatures', 'rooms', 'spells', 'waves'] as $resource) {
            $this->get("/admin/{$resource}/create")->assertNotFound();
            $this->get("/admin/{$resource}")->assertOk()->assertDontSee("/admin/{$resource}/create", false);
        }
    }

    public function test_records_are_listed_in_config_order(): void
    {
        $this->assertSame(array_keys(config('underlord.creatures')), Creature::inConfigOrder()->pluck('key')->all());
        $this->assertSame(
            collect(config('underlord.settings'))->flatMap(fn ($values, $group) => array_map(fn ($key) => "{$group}.{$key}", array_keys($values)))->all(),
            Setting::inConfigOrder()->pluck('key')->all(),
        );
    }

    public function test_edit_forms_show_the_default_and_flag_changes(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $troll = Creature::where('key', 'troll')->first();
        $troll->update(['hp' => 999]);

        $this->get("/admin/creatures/{$troll->getKey()}/edit")
            ->assertOk()
            ->assertSee('Default: 170')
            ->assertSee('Default: lair 9, hatchery 9');

        $this->get('/admin/settings')->assertOk()->assertSee('Gold you start every game with.');
    }

    public function test_changes_record_who_made_them_and_defaults_do_not(): void
    {
        $troll = Creature::where('key', 'troll')->first();
        $this->assertNull($troll->updated_at);
        $this->assertNull($troll->updated_by);

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $troll->update(['hp' => 200]);

        $this->assertSame($admin->id, $troll->fresh()->updated_by);
        $this->assertNotNull($troll->fresh()->updated_at);
        $this->assertNull($troll->fresh()->created_at);

        $this->get("/admin/creatures/{$troll->getKey()}/edit")->assertOk()->assertSee($admin->name);
    }
}
