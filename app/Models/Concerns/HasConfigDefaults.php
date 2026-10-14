<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Game data whose defaults live in config/underlord/*.php. The config decides the order the
 * records are listed in, and holds the default for every field.
 */
trait HasConfigDefaults
{
    /**
     * The config entry for this record, e.g. config('underlord.creatures.goblin').
     *
     * @return array<string, mixed>|null
     */
    abstract public function configDefaults(): ?array;

    /**
     * Keys in config order, as [column => value] conditions; used to sort records like the config.
     *
     * @return list<array<string, string|int>>
     */
    abstract public static function configOrder(): array;

    public function defaultValue(string $field): mixed
    {
        return data_get($this->configDefaults(), $field);
    }

    /** @param  Builder<static>  $query */
    public function scopeInConfigOrder(Builder $query): void
    {
        $cases = [];
        $bindings = [];
        foreach (static::configOrder() as $position => $conditions) {
            $cases[] = 'WHEN '.implode(' AND ', array_map(fn (string $column): string => $query->getGrammar()->wrap($column).' = ?', array_keys($conditions))).' THEN '.$position;
            array_push($bindings, ...array_values($conditions));
        }

        if ($cases) {
            $query->orderByRaw('CASE '.implode(' ', $cases).' ELSE '.count($cases).' END', $bindings);
        }
    }
}
