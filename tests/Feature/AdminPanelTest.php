<?php

namespace Tests\Feature;

use App\Models\Creature;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Spell;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @return array<string, array{string}> */
    public static function pages(): array
    {
        return [
            'dashboard' => ['/admin'],
            'settings' => ['/admin/settings'],
            'creatures' => ['/admin/creatures'],
            'rooms' => ['/admin/rooms'],
            'spells' => ['/admin/spells'],
            'waves' => ['/admin/waves'],
            'users' => ['/admin/users'],
        ];
    }

    #[DataProvider('pages')]
    public function test_admins_can_open_every_page(string $url): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]))->get($url)->assertOk();
    }

    /** @return array<string, array{class-string<Model>, string}> */
    public static function editPages(): array
    {
        return [
            'setting' => [Setting::class, 'settings'],
            'creature' => [Creature::class, 'creatures'],
            'hero' => [Creature::class, 'creatures'],
            'room' => [Room::class, 'rooms'],
            'spell' => [Spell::class, 'spells'],
            'wave' => [Wave::class, 'waves'],
            'user' => [User::class, 'users'],
        ];
    }

    #[DataProvider('editPages')]
    public function test_admins_can_open_edit_forms(string $model, string $slug): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $record = $this->dataName() === 'hero' ? $model::where('side', 'hero')->first() : $model::query()->first();

        $this->actingAs($admin)->get("/admin/{$slug}/{$record->getKey()}/edit")->assertOk();
    }

    public function test_players_cannot_open_the_panel(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))->get('/admin')->assertForbidden();
    }

    public function test_only_admins_impersonate_and_only_players_are_impersonated(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $player = User::factory()->create(['is_admin' => false]);

        $this->assertTrue($admin->canImpersonate());
        $this->assertFalse($player->canImpersonate());
        $this->assertTrue($player->canBeImpersonated());
        $this->assertFalse($admin->canBeImpersonated());
    }

    public function test_the_game_page_renders_for_guests(): void
    {
        $this->get('/')->assertOk()->assertSee('window.UNDERLORD', false);
    }

    public function test_game_data_urls_use_handles(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->get('/admin/creatures/troll/edit')->assertOk();
        $this->get('/admin/settings/economy.startGold/edit')->assertOk();
        $this->get('/admin/waves/6/edit')->assertOk();
    }

    public function test_creatures_show_their_svg_in_the_list_and_the_form(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->get('/admin/creatures')->assertOk()->assertSee('assets/units/troll.svg', false);
        $this->get('/admin/creatures/troll/edit')->assertOk()->assertSee('assets/units/troll.svg', false);
    }
}
