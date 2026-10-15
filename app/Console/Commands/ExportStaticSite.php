<?php

namespace App\Console\Commands;

use App\Services\AppVersion;
use App\Services\GameConfig;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/**
 * Builds a static, database-free copy of the game for GitHub Pages: index.html with the config
 * defaults embedded, the built CSS/JS, the SVG assets and the favicon. All paths are relative, so it
 * works from any sub-path (e.g. /Underlord/). Without a logged-in player the game saves to
 * localStorage, so saving works there too. Run `npm run build` first.
 */
#[Signature('underlord:export-static {--out=build/static : Directory to write the static site to}')]
#[Description('Export a static, playable copy of the game (config defaults, localStorage saves) for GitHub Pages')]
class ExportStaticSite extends Command
{
    public function handle(GameConfig $config, AppVersion $version, Filesystem $files): int
    {
        $manifestPath = public_path('build/manifest.json');
        if (! $files->exists($manifestPath)) {
            $this->components->error('No Vite build found. Run `npm run build` first.');

            return self::FAILURE;
        }

        /** @var array<string, array{file: string}> $manifest */
        $manifest = json_decode($files->get($manifestPath), true, flags: JSON_THROW_ON_ERROR);
        $css = $manifest['resources/css/underlord.css']['file'] ?? null;
        $js = $manifest['resources/js/underlord.js']['file'] ?? null;
        if ($css === null || $js === null) {
            $this->components->error("The Vite build does not contain the game's CSS and JS.");

            return self::FAILURE;
        }

        $out = $this->outputDirectory();
        $files->deleteDirectory($out);
        $files->ensureDirectoryExists($out.'/build/assets');

        $files->copy(public_path('build/'.$css), $out.'/build/'.$css);
        $files->copy(public_path('build/'.$js), $out.'/build/'.$js);
        $files->copyDirectory(public_path('assets'), $out.'/assets');
        $files->copy(public_path('favicon.svg'), $out.'/favicon.svg');

        $html = view('game', [
            'boot' => [
                'config' => $config->defaults(),
                'user' => null,
                'saveUrl' => null,
                'csrf' => null,
            ],
            'static' => ['css' => 'build/'.$css, 'js' => 'build/'.$js],
            'version' => $version->current(),
        ])->render();

        $files->put($out.'/index.html', $html);
        // GitHub Pages: serve the files as they are, without Jekyll.
        $files->put($out.'/.nojekyll', '');

        $this->components->info("Static site written to {$out}.");

        return self::SUCCESS;
    }

    private function outputDirectory(): string
    {
        $out = $this->option('out');
        $out = is_string($out) && $out !== '' ? $out : 'build/static';

        return str_starts_with($out, '/') ? rtrim($out, '/') : base_path(rtrim($out, '/'));
    }
}
