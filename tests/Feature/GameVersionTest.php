<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class GameVersionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_the_game_shows_the_commit_hash_and_time(): void
    {
        config(['app.timezone' => 'UTC']);
        Process::fake(['*' => Process::result('caed9fc 2026-10-15T14:03:27+02:00')]);

        $this->get('/')->assertOk()->assertSeeHtml('class="version"')->assertSee('caed9fc · 2026-10-15 12:03');
    }

    public function test_the_game_leaves_the_version_out_without_git(): void
    {
        Process::fake(['*' => Process::result(errorOutput: 'fatal: not a git repository', exitCode: 128)]);

        $this->get('/')->assertOk()->assertDontSeeHtml('class="version"');
    }
}
