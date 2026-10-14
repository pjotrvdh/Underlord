<?php

namespace App\Models\Concerns;

use App\Services\GameConfig;

/**
 * Any change to a game setting drops the cached config, so the next page load gets the new values.
 */
trait ClearsGameConfig
{
    protected static function bootClearsGameConfig(): void
    {
        static::saved(fn () => GameConfig::forget());
        static::deleted(fn () => GameConfig::forget());
    }
}
