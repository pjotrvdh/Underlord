<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\GameDefaults;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('underlord:defaults {--reset : Put every record back to its default, overwriting changes made in the admin panel}')]
#[Description('Add the built-in game data (settings, creatures, rooms, spells, waves) that is missing from the database')]
class SyncGameDefaults extends Command
{
    public function handle(GameDefaults $defaults): int
    {
        if ($this->option('reset') && ! $this->confirm('This overwrites every change made in the admin panel. Continue?', ! $this->input->isInteractive())) {
            return self::FAILURE;
        }

        $defaults->sync(reset: (bool) $this->option('reset'));
        $this->components->info($this->option('reset') ? 'All game data is back to its defaults.' : 'Missing game data added; edited records were left alone.');

        return self::SUCCESS;
    }
}
