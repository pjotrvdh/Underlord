<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheatsVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private const string HIDDEN_CHEATS_BUTTON = 'id="bCheat" type="button" aria-expanded="false" aria-controls="cheats" hidden';

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_guests_get_the_hidden_cheats_panel(): void
    {
        $this->get('/')->assertOk()->assertSeeHtml(self::HIDDEN_CHEATS_BUTTON)->assertSeeHtml('id="cheats" hidden');
    }

    public function test_players_get_the_hidden_cheats_panel(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/')->assertOk()->assertSeeHtml(self::HIDDEN_CHEATS_BUTTON);
    }

    public function test_admins_also_have_to_type_the_cheat_code(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/')->assertOk()->assertSeeHtml(self::HIDDEN_CHEATS_BUTTON);
    }
}
