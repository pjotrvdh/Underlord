<?php

namespace App\Models;

use App\Models\Concerns\ClearsGameConfig;
use App\Models\Concerns\HasConfigDefaults;
use App\Models\Concerns\TracksUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class Wave extends Model
{
    use ClearsGameConfig, HasConfigDefaults, TracksUpdatedBy;

    /** Game data comes from config/underlord; only the last change in the admin panel is kept. */
    public const CREATED_AT = null;

    /** Waves are identified by their number. */
    protected $primaryKey = 'number';

    public $incrementing = false;

    protected $fillable = ['number', 'heroes'];

    protected function casts(): array
    {
        return ['heroes' => 'array'];
    }

    public function configDefaults(): ?array
    {
        $heroes = config('underlord.waves')[$this->number - 1] ?? null;

        return $heroes === null ? null : ['number' => $this->number, 'heroes' => $heroes];
    }

    public static function configOrder(): array
    {
        return array_map(fn (int $i): array => ['number' => $i + 1], array_keys(config('underlord.waves')));
    }
}
