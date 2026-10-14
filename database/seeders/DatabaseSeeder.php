<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\GameDefaults;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(GameDefaults $defaults): void
    {
        // Normally already done after migrating; kept here so `db:seed` alone also works.
        $defaults->sync();

        // Local development account for the admin panel (/admin) and for saving games.
        if (app()->isLocal()) {
            User::updateOrCreate(
                ['email' => 'admin@underlord.test'],
                ['name' => 'Underlord Admin', 'password' => 'password', 'is_admin' => true],
            );
        }
    }
}
