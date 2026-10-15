<?php

namespace App\Services;

use App\Models\Creature;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Spell;
use App\Models\Wave;
use App\Support\GameData;
use Illuminate\Support\Facades\Cache;

/**
 * Builds the config object the game reads (window.UNDERLORD.config).
 * Its shape matches the old config/*.yml files, so the game's applyConfig() did not have to change.
 */
class GameConfig
{
    private const string CACHE_KEY = 'underlord.game-config';

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): array => $this->build());
    }

    /**
     * The same object as toArray(), built from the defaults in config/underlord only (no database),
     * with asset paths relative to the page. Used for the static build (underlord:export-static).
     *
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        $creatures = ['creatures' => [], 'heroes' => []];
        foreach (GameData::creatures() as $key => $c) {
            $row = [
                'name' => $c['name'], 'hp' => $c['hp'], 'attack' => $c['attack'],
                'speed' => $c['speed'], 'range' => $c['range'], 'rate' => $c['rate'], 'color' => $c['color'],
            ];
            if ($c['side'] === Creature::SIDE_HERO) {
                $creatures['heroes'][$key] = $row + ['bounty' => $c['bounty'] ?? 0, 'dig' => $c['dig'] ?? null, 'big' => $c['big'] ?? false];
            } else {
                $creatures['creatures'][$key] = $row + [
                    'wage' => $c['wage'] ?? 0, 'vision' => $c['vision'] ?? 5, 'pool' => $c['pool'] ?? 0, 'attract' => $c['attract'] ?? [],
                ];
            }
        }

        $spells = [];
        foreach (GameData::spells() as $key => $s) {
            $spells[$key] = ['name' => $s['name'], 'research' => $s['research'], 'cost' => $s['cost']] + $s['params'];
        }

        return [
            'game' => GameData::settings(),
            'creatures' => $creatures,
            'rooms' => ['rooms' => GameData::rooms()],
            'spells' => ['spells' => $spells],
            'waves' => ['waves' => GameData::waves()],
            'assets' => GameData::assets(),
        ];
    }

    /** @return array<string, mixed> */
    private function build(): array
    {
        $game = [];
        foreach (Setting::query()->inConfigOrder()->get() as $setting) {
            $game[$setting->group][$setting->name()] = $setting->value;
        }

        $creatures = ['creatures' => [], 'heroes' => []];
        foreach (Creature::query()->inConfigOrder()->get() as $c) {
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
            $r = $rooms->get($key);
            if (! $r instanceof Room) {
                continue;
            }

            $roomRows[$key] = ['name' => $r->name, 'cost' => $r->cost, 'color' => $r->color, 'tip' => $r->tip];
        }

        $spells = [];
        foreach (Spell::query()->inConfigOrder()->get() as $s) {
            $spells[$s->key] = ['name' => $s->name, 'research' => $s->research, 'cost' => $s->cost] + ($s->params ?? []);
        }

        // Assets are managed in the project (config/underlord/assets.php), not in the database.
        $assets = array_map(static fn (array $files): array => array_map(asset(...), $files), GameData::assets());

        return [
            'game' => $game,
            'creatures' => $creatures,
            'rooms' => ['rooms' => $roomRows],
            'spells' => ['spells' => $spells],
            'waves' => ['waves' => Wave::query()->inConfigOrder()->pluck('heroes')->all()],
            'assets' => $assets,
        ];
    }
}
