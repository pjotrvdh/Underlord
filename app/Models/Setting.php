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
use Illuminate\Support\Str;

/**
 * @property float|int $value
 */
#[Fillable(['key', 'group', 'value'])]
#[WithoutIncrementing]
class Setting extends Model implements HasDefaults
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
        return ['value' => 'json'];
    }

    public function configDefaults(): ?array
    {
        $default = GameData::settings()[$this->group][$this->name()] ?? null;

        return $default === null ? null : ['value' => $default];
    }

    public static function configOrder(): array
    {
        $order = [];
        foreach (GameData::settings() as $group => $values) {
            foreach (array_keys($values) as $key) {
                $order[] = ['key' => "{$group}.{$key}"];
            }
        }

        return $order;
    }

    public function description(): ?string
    {
        $line = "resource.setting.descriptions.{$this->key}";

        return trans()->has($line) ? GameData::trans($line) : null;
    }

    /** The setting's name within its group: economy.startGold → startGold. */
    public function name(): string
    {
        return Str::after($this->key, $this->group.'.');
    }
}
