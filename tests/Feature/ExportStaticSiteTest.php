<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportStaticSiteTest extends TestCase
{
    use RefreshDatabase;

    private string $out;

    protected function setUp(): void
    {
        parent::setUp();
        $this->out = storage_path('framework/testing/static-export');
    }

    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->out);
        parent::tearDown();
    }

    public function test_it_exports_a_playable_page_with_the_config_defaults(): void
    {
        // An edit in the admin panel must not leak into the static build: it uses the config defaults.
        Setting::query()->whereKey('economy.startGold')->update(['value' => 9000]);

        $this->artisan('underlord:export-static', ['--out' => $this->out])->assertSuccessful();

        $html = (string) file_get_contents($this->out.'/index.html');

        $this->assertStringContainsString('"startGold":1500', $html);
        $this->assertStringNotContainsString('"startGold":9000', $html);
        $this->assertStringContainsString('"imp":"assets\/units\/imp.svg"', $html);
        $this->assertStringContainsString('id="bCheat" type="button" aria-expanded="false" aria-controls="cheats" hidden', $html);
        $this->assertStringNotContainsString('csrf-token', $html);
        $this->assertStringNotContainsString('/login', $html);
        $this->assertMatchesRegularExpression('#src="build/assets/underlord-[^"]+\.js"#', $html);

        $this->assertFileExists($this->out.'/favicon.svg');
        $this->assertFileExists($this->out.'/assets/units/imp.svg');
        $this->assertFileExists($this->out.'/.nojekyll');
    }
}
