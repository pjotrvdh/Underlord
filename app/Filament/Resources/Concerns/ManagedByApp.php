<?php

namespace App\Filament\Resources\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Game data records come from the app itself (config/underlord/*.php, synced by GameDefaults).
 * In the admin panel they can only be edited, never created or deleted.
 */
trait ManagedByApp
{
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
