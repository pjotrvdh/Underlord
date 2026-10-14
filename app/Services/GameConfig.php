<?php

namespace App\Services;

use App\Models\Creature;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Spell;
use App\Models\Wave;
use Illuminate\Support\Facades\Cache;

/**
 * Builds the config object the game reads (window.UNDERLORD.config).
 * Its shape matches the old config/*.yml files, so the game's applyConfig() did not have to change.
 */
class GameConfig
{
    private const CACHE_KEY = 'underlord.game-config';

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => $this->build());
    }

    /** @return array<string, mixed> */
    private function build(): array
    {
        $game = [];
        foreach (Setting::inConfigOrder()->get() as $setting) {
            $game[$setting->group][$setting->name()] = $setting->value;
        }

        $creatures = ['creatures' => [], 'heroes' => []];
        foreach (Creature::inConfigOrder()->get() as $c) {
            $row = [
                'name' => $c->name, 'hp' => $c->hp, 'attack' => $c->attack,
                'speed' => $c->speed, 'range' => $c->range, 'rate' => $c->rate, 'color' => $c->color,
            ];
            if ($c->side === Creature::SIDE_HERO) {
                $creatures['heroes'][$c->key] = $row + ['bounty' => $c->bounty, 'dig' => $c->dig, 'big' => $c->big];
            } else {
                $creatures['creatures'][$c->key] = $row + [
                    'wage' => $c->wage, 'vision' => $c->vision, 'pool' => $c->pool, 'attract' => $c->attract ?? [],
                ];
            }
        }

        $rooms = Room::all()->keyBy('key');
        $roomRows = [];
        foreach (Room::KEYS as $key) {
            $r = $rooms[$key];
            $roomRows[$key] = ['name' => $r->name, 'cost' => $r->cost, 'color' => $r->color, 'tip' => $r->tip];
        }

        $spells = [];
        foreach (Spell::inConfigOrder()->get() as $s) {
            $spells[$s->key] = ['name' => $s->name, 'research' => $s->research, 'cost' => $s->cost] + ($s->params ?? []);
        }

        // Assets are managed in the project (config/underlord.php), not in the database.
        $assets = array_map(fn (array $files): array => array_map(fn (string $path): string => asset($path), $files), config('underlord.assets'));

        return [
            'game' => $game,
            'creatures' => $creatures,
            'rooms' => ['rooms' => $roomRows],
            'spells' => ['spells' => $spells],
            'waves' => ['waves' => Wave::inConfigOrder()->pluck('heroes')->all()],
            'assets' => $assets,
        ];
    }
}
