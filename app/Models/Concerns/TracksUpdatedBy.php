<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Remembers who last changed a game data record in the admin panel. Changes made by the app
 * itself (GameDefaults, without a logged-in user) leave updated_by empty.
 */
trait TracksUpdatedBy
{
    protected static function bootTracksUpdatedBy(): void
    {
        static::saving(function (self $model): void {
            $user = Auth::user();
            $model->updated_by = $user instanceof User ? $user->id : null;
        });
    }

    /** @return BelongsTo<User, $this> */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
