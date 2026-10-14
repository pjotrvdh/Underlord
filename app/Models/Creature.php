<?php

namespace App\Models;

use App\Models\Concerns\ClearsGameConfig;
use App\Models\Concerns\HasConfigDefaults;
use App\Models\Concerns\TracksUpdatedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Creature extends Model
{
    use ClearsGameConfig, HasConfigDefaults, TracksUpdatedBy;

    /** Game data comes from config/underlord; only the last change in the admin panel is kept. */
    public const CREATED_AT = null;

    /** Records are identified by their handle (e.g. troll, economy.startGold), not by an id. */
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    public const SIDE_CREATURE = 'creature';

    public const SIDE_HERO = 'hero';

    protected $fillable = [
        'key', 'side', 'name', 'hp', 'attack', 'speed', 'range', 'rate', 'wage', 'vision',
        'pool', 'attract', 'bounty', 'dig', 'big',
    ];

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
        return config("underlord.creatures.{$this->key}");
    }

    public static function configOrder(): array
    {
        return array_map(fn (string $key): array => ['key' => $key], array_keys(config('underlord.creatures')));
    }

    /** URL of the creature's SVG (config/underlord/assets.php), or null when it has none. */
    public function assetUrl(): ?string
    {
        $path = config("underlord.assets.units.{$this->key}");

        return $path ? asset($path) : null;
    }

    /**
     * The colour is fixed in config/underlord/creatures.php, like the SVG that goes with it; it is not stored or edited.
     *
     * @return Attribute<string|null, never>
     */
    protected function color(): Attribute
    {
        return Attribute::get(fn (): ?string => config("underlord.creatures.{$this->key}.color"));
    }
}
