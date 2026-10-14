<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheatsVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_guests_do_not_get_the_cheats_panel(): void
    {
        $this->get('/')->assertOk()->assertDontSee('id="bCheat"', false)->assertSee('"isAdmin":false', false);
    }

    public function test_players_who_are_not_admin_do_not_get_the_cheats_panel(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/')->assertOk()->assertDontSee('id="bCheat"', false);
    }

    public function test_admins_get_the_cheats_panel(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]))
            ->get('/')->assertOk()->assertSee('id="bCheat"', false)->assertSee('"isAdmin":true', false);
    }
}
