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
        $this->assertSame(5, Creature::query()->where('side', Creature::SIDE_CREATURE)->count());
        $this->assertSame(1500, Setting::query()->whereKey('economy.startGold')->value('value'));
    }

    public function test_sync_adds_missing_records_and_keeps_edits(): void
    {
        Setting::query()->whereKey('economy.startGold')->update(['value' => 9000]);
        Creature::query()->where('key', 'troll')->delete();

        resolve(GameDefaults::class)->sync();

        $this->assertSame(9000, Setting::query()->whereKey('economy.startGold')->value('value'));
        $this->assertTrue(Creature::query()->where('key', 'troll')->exists());
    }

    public function test_reset_puts_edits_back_to_the_defaults(): void
    {
        Setting::query()->whereKey('economy.startGold')->update(['value' => 9000]);

        $this->artisan('underlord:defaults', ['--reset' => true])->expectsConfirmation('This overwrites every change made in the admin panel. Continue?', 'yes')->assertSuccessful();

        $this->assertSame(1500, Setting::query()->whereKey('economy.startGold')->value('value'));
    }

    public function test_the_admin_panel_cannot_create_game_data(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        foreach (['settings', 'creatures', 'rooms', 'spells', 'waves'] as $resource) {
            $this->get("/admin/{$resource}/create")->assertNotFound();
            $this->get("/admin/{$resource}")->assertOk()->assertDontSeeHtml("/admin/{$resource}/create");
        }
    }

    public function test_records_are_listed_in_config_order(): void
    {
        $this->assertSame(array_keys(config('underlord.creatures')), Creature::query()->inConfigOrder()->pluck('key')->all());
        $this->assertSame(
            collect(config('underlord.settings'))->flatMap(fn ($values, $group): array => array_map(fn (int|string $key): string => "{$group}.{$key}", array_keys($values)))->all(),
            Setting::query()->inConfigOrder()->pluck('key')->all(),
        );
    }

    public function test_edit_forms_show_the_default_and_flag_changes(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $troll = Creature::query()->where('key', 'troll')->first();
        $troll->update(['hp' => 999]);

        $this->get("/admin/creatures/{$troll->getKey()}/edit")
            ->assertOk()
            ->assertSee('Default: 110')
            ->assertSee('Default: lair 9, hatchery 9');

        $this->get('/admin/settings')->assertOk()->assertSee('Gold you start every game with.');
    }

    public function test_changes_record_who_made_them_and_defaults_do_not(): void
    {
        $troll = Creature::query()->where('key', 'troll')->first();
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

    public function test_sync_removes_records_the_config_no_longer_lists(): void
    {
        Creature::query()->create(['key' => 'goblin', 'side' => Creature::SIDE_CREATURE, 'name' => 'Goblin', 'hp' => 90, 'attack' => 9, 'speed' => 2.4]);

        resolve(GameDefaults::class)->sync();

        $this->assertFalse(Creature::query()->whereKey('goblin')->exists());
        $this->assertTrue(Creature::query()->whereKey('orc')->exists());
    }
}
