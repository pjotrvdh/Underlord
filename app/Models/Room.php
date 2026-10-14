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

#[Fillable(['key', 'name', 'cost', 'tip'])]
#[WithoutIncrementing]
class Room extends Model implements HasDefaults
{
    use ClearsGameConfig;
    use HasConfigDefaults;
    use TracksUpdatedBy;

    /** Game data comes from config/underlord; only the last change in the admin panel is kept. */
    public const CREATED_AT = null;

    /** Records are identified by their handle (e.g. troll, economy.startGold), not by an id. */
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    /** The game logic depends on these ids, in this order (room codes 1..5). */
    public const KEYS = ['treasury', 'lair', 'hatchery', 'training', 'library'];

    public function configDefaults(): ?array
    {
        return GameData::rooms()[$this->key] ?? null;
    }

    public static function configOrder(): array
    {
        return array_map(static fn (string $key): array => ['key' => $key], array_keys(GameData::rooms()));
    }

    /**
     * The colour is fixed in config/underlord/rooms.php, like the SVG that goes with it; it is not stored or edited.
     *
     * @return Attribute<string|null, never>
     */
    protected function color(): Attribute
    {
        return Attribute::get(fn (): ?string => GameData::rooms()[$this->key]['color'] ?? null);
    }
}
