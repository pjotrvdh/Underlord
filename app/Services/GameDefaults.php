<?php

namespace App\Services;

use App\Models\Creature;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Spell;
use App\Models\Wave;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Puts the game's built-in data (config/underlord/*.php) into the database: settings,
 * creatures, rooms, spells and waves. Colours stay in config only; they are not editable. The admin panel can only edit these records, never create
 * them, so this is the one place they come from.
 *
 * sync() adds whatever is missing and leaves edited records alone; sync(reset: true) puts every
 * record back to its default. It runs after every `artisan migrate` (see AppServiceProvider).
 */
class GameDefaults
{
    private bool $reset = false;

    public function sync(bool $reset = false): void
    {
        $this->reset = $reset;

        foreach (config('underlord.settings') as $group => $values) {
            foreach ($values as $key => $value) {
                $this->put(Setting::class, ['key' => "{$group}.{$key}"], ['group' => $group, 'value' => $value]);
            }
        }

        foreach (config('underlord.creatures') as $key => $c) {
            $this->put(Creature::class, ['key' => $key], [
                'side' => $c['side'],
                'name' => $c['name'],
                'hp' => $c['hp'],
                'attack' => $c['attack'],
                'speed' => $c['speed'],
                'range' => $c['range'],
                'rate' => $c['rate'],
                'wage' => $c['wage'] ?? 0,
                'vision' => $c['vision'] ?? 5,
                'pool' => $c['pool'] ?? 0,
                'attract' => $c['attract'] ?? [],
                'bounty' => $c['bounty'] ?? 0,
                'dig' => $c['dig'] ?? null,
                'big' => $c['big'] ?? false,
            ]);
        }

        foreach (config('underlord.rooms') as $key => $r) {
            $this->put(Room::class, ['key' => $key], Arr::except($r, 'color'));
        }

        foreach (config('underlord.spells') as $key => $s) {
            $this->put(Spell::class, ['key' => $key], $s);
        }

        foreach (config('underlord.waves') as $n => $heroes) {
            $this->put(Wave::class, ['number' => $n + 1], ['heroes' => $heroes]);
        }
    }

    /**
     * Create the record if it is missing; overwrite it only when resetting.
     *
     * @param  class-string<Model>  $model
     * @param  array<string, mixed>  $keys
     * @param  array<string, mixed>  $values
     */
    private function put(string $model, array $keys, array $values): void
    {
        // Defaults are not a change: updated_at/updated_by stay empty until someone edits the record.
        Model::withoutTimestamps(function () use ($model, $keys, $values): void {
            if (! $this->reset) {
                $model::firstOrCreate($keys, $values);

                return;
            }

            $record = $model::firstOrNew($keys)->fill($values);
            $record->forceFill(['updated_at' => null]);
            $record->save();
        });
    }
}
