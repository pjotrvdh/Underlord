<?php

namespace App\Models;

use App\Models\Concerns\ClearsGameConfig;
use App\Models\Concerns\HasConfigDefaults;
use App\Models\Concerns\TracksUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Setting extends Model
{
    use ClearsGameConfig, HasConfigDefaults, TracksUpdatedBy;

    /** Game data comes from config/underlord; only the last change in the admin panel is kept. */
    public const CREATED_AT = null;

    /** Records are identified by their handle (e.g. troll, economy.startGold), not by an id. */
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'group', 'value'];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    public function configDefaults(): ?array
    {
        $default = config("underlord.settings.{$this->key}");

        return $default === null ? null : ['value' => $default];
    }

    public static function configOrder(): array
    {
        $order = [];
        foreach (config('underlord.settings') as $group => $values) {
            foreach (array_keys($values) as $key) {
                $order[] = ['key' => "{$group}.{$key}"];
            }
        }

        return $order;
    }

    public function description(): ?string
    {
        $line = "resource.setting.descriptions.{$this->key}";

        return trans()->has($line) ? __($line) : null;
    }

    /** The setting's name within its group: economy.startGold → startGold. */
    public function name(): string
    {
        return Str::after($this->key, $this->group.'.');
    }
}
