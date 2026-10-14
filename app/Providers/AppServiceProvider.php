<?php

namespace App\Providers;

use App\Services\GameDefaults;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Game data always comes from the app itself: after every migration, add any missing defaults.
        // Records edited in the admin panel are left alone.
        Event::listen(function (MigrationsEnded $event): void {
            if ($event->method === 'up' && empty($event->options['pretend']) && Schema::hasTable('settings')) {
                app(GameDefaults::class)->sync();
            }
        });
    }
}
