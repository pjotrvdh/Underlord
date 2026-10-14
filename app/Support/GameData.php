<?php

namespace App\Support;

/**
 * Typed access to the game's default data in config/underlord/*.php.
 *
 * @phpstan-type CreatureDefaults array{side: string, name: string, hp: int, attack: int, speed: float|int, range: float|int, rate: float|int, wage?: int, vision?: int, pool?: int, attract?: array<string, int>, bounty?: int, dig?: float|int, big?: bool, color: string}
 * @phpstan-type RoomDefaults array{name: string, cost: int, color: string, tip: string}
 * @phpstan-type SpellDefaults array{name: string, research: int, cost: int, params: array<string, float|int>}
 */
final class GameData
{
    /** @return array<string, array<string, float|int>> */
    public static function settings(): array
    {
        /** @var array<string, array<string, float|int>> */
        return config()->array('underlord.settings');
    }

    /** @return array<string, CreatureDefaults> */
    public static function creatures(): array
    {
        /** @var array<string, CreatureDefaults> */
        return config()->array('underlord.creatures');
    }

    /** @return array<string, RoomDefaults> */
    public static function rooms(): array
    {
        /** @var array<string, RoomDefaults> */
        return config()->array('underlord.rooms');
    }

    /** @return array<string, SpellDefaults> */
    public static function spells(): array
    {
        /** @var array<string, SpellDefaults> */
        return config()->array('underlord.spells');
    }

    /** @return list<array<string, int>> */
    public static function waves(): array
    {
        /** @var list<array<string, int>> */
        return config()->array('underlord.waves');
    }

    /** @return array<string, array<string, string>> */
    public static function assets(): array
    {
        /** @var array<string, array<string, string>> */
        return config()->array('underlord.assets');
    }

    /** Turn form state (often a numeric string) into a number. */
    public static function number(mixed $value): int|float
    {
        return is_numeric($value) ? $value + 0 : 0;
    }

    /**
     * Turn a key-value form state into a map of integers, e.g. {"library": "9"} → ["library" => 9].
     *
     * @return array<string, int>
     */
    public static function intMap(mixed $state): array
    {
        $out = [];
        foreach (is_array($state) ? $state : [] as $key => $value) {
            $out[(string) $key] = (int) self::number($value);
        }

        return $out;
    }

    /**
     * Turn a hero repeater's rows into the stored map: [['hero' => 'tunneller', 'count' => '2']] → ['tunneller' => 2].
     *
     * @return array<string, int>
     */
    public static function heroRows(mixed $state): array
    {
        $out = [];
        foreach (is_array($state) ? $state : [] as $row) {
            if (is_array($row) && is_string($row['hero'] ?? null)) {
                $out[$row['hero']] = (int) self::number($row['count'] ?? 0);
            }
        }

        return $out;
    }

    /**
     * A translation that is always a string.
     *
     * @param  array<string, string|int|float>  $replace
     */
    public static function trans(string $key, array $replace = []): string
    {
        $text = __($key, $replace);

        return is_string($text) ? $text : $key;
    }
}
