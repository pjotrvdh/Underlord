<?php

declare(strict_types=1);

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
 * @property array<string, int> $heroes
 */
#[Fillable(['number', 'heroes'])]
#[WithoutIncrementing]
class Wave extends Model implements HasDefaults
{
    use ClearsGameConfig;
    use HasConfigDefaults;
    use TracksUpdatedBy;

    /** Game data comes from config/underlord; only the last change in the admin panel is kept. */
    public const CREATED_AT = null;

    /** Waves are identified by their number. */
    protected $primaryKey = 'number';

    protected function casts(): array
    {
        return ['heroes' => 'array'];
    }

    public function configDefaults(): ?array
    {
        $heroes = GameData::waves()[$this->number - 1] ?? null;

        return $heroes === null ? null : ['number' => $this->number, 'heroes' => $heroes];
    }

    public static function configOrder(): array
    {
        return array_map(static fn (int $i): array => ['number' => $i + 1], array_keys(GameData::waves()));
    }
}
