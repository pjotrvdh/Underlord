<?php

namespace App\Models;

use App\Models\Concerns\ClearsGameConfig;
use App\Models\Concerns\HasConfigDefaults;
use App\Models\Concerns\TracksUpdatedBy;
use App\Models\Contracts\HasDefaults;
use App\Support\GameData;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<string, float|int>|null $params
 */
#[Fillable(['key', 'name', 'research', 'cost', 'params'])]
#[WithoutIncrementing]
class Spell extends Model implements HasDefaults
{
    use ClearsGameConfig;
    use HasConfigDefaults;
    use TracksUpdatedBy;

    /** Game data comes from config/underlord; only the last change in the admin panel is kept. */
    public const CREATED_AT = null;

    /** Records are identified by their handle (e.g. troll, economy.startGold), not by an id. */
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected function casts(): array
    {
        return ['params' => 'array'];
    }

    public function configDefaults(): ?array
    {
        return GameData::spells()[$this->key] ?? null;
    }

    public static function configOrder(): array
    {
        return array_map(static fn (string $key): array => ['key' => $key], array_keys(GameData::spells()));
    }
}
