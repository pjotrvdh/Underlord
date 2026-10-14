<?php

namespace App\Models;

use App\Models\Concerns\ClearsGameConfig;
use App\Models\Concerns\HasConfigDefaults;
use App\Models\Concerns\TracksUpdatedBy;
use App\Models\Contracts\HasDefaults;
use App\Support\GameData;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<string, int>|null $attract
 */
#[Fillable([
    'key', 'side', 'name', 'hp', 'attack', 'speed', 'range', 'rate', 'wage', 'vision',
    'pool', 'attract', 'bounty', 'dig', 'big',
])]
#[WithoutIncrementing]
class Creature extends Model implements HasDefaults
{
    use ClearsGameConfig;
    use HasConfigDefaults;
    use TracksUpdatedBy;

    /** Game data comes from config/underlord; only the last change in the admin panel is kept. */
    public const CREATED_AT = null;

    /** Records are identified by their handle (e.g. troll, economy.startGold), not by an id. */
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public const SIDE_CREATURE = 'creature';

    public const SIDE_HERO = 'hero';

    protected function casts(): array
    {
        return [
            'attract' => 'array',
            'big' => 'boolean',
            'speed' => 'float',
            'range' => 'float',
            'rate' => 'float',
            'dig' => 'float',
        ];
    }

    public function configDefaults(): ?array
    {
        return GameData::creatures()[$this->key] ?? null;
    }

    public static function configOrder(): array
    {
        return array_map(static fn (string $key): array => ['key' => $key], array_keys(GameData::creatures()));
    }

    /** URL of the creature's SVG (config/underlord/assets.php), or null when it has none. */
    public function assetUrl(): ?string
    {
        $path = GameData::assets()['units'][$this->key] ?? null;

        return $path !== null ? asset($path) : null;
    }

    /**
     * The colour is fixed in config/underlord/creatures.php, like the SVG that goes with it; it is not stored or edited.
     *
     * @return Attribute<string|null, never>
     */
    protected function color(): Attribute
    {
        return Attribute::get(fn (): ?string => GameData::creatures()[$this->key]['color'] ?? null);
    }
}
