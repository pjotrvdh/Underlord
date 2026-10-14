<?php

namespace App\Models;

use App\Models\Concerns\ClearsGameConfig;
use App\Models\Concerns\HasConfigDefaults;
use App\Models\Concerns\TracksUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class Spell extends Model
{
    use ClearsGameConfig, HasConfigDefaults, TracksUpdatedBy;

    /** Game data comes from config/underlord; only the last change in the admin panel is kept. */
    public const CREATED_AT = null;

    /** Records are identified by their handle (e.g. troll, economy.startGold), not by an id. */
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'name', 'research', 'cost', 'params'];

    protected function casts(): array
    {
        return ['params' => 'array'];
    }

    public function configDefaults(): ?array
    {
        return config("underlord.spells.{$this->key}");
    }

    public static function configOrder(): array
    {
        return array_map(fn (string $key): array => ['key' => $key], array_keys(config('underlord.spells')));
    }
}
