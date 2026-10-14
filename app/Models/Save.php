<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Save extends Model
{
    protected $fillable = ['user_id', 'slot', 'version', 'wave', 'data'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
