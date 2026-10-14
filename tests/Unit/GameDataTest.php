<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Filament\Support\DefaultValue;
use App\Support\GameData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GameDataTest extends TestCase
{
    public function test_number_turns_form_state_into_numbers(): void
    {
        $this->assertSame(1500, GameData::number('1500'));
        $this->assertSame(1.5, GameData::number('1.5'));
        $this->assertSame(7, GameData::number(7));
        $this->assertSame(0, GameData::number('abc'));
        $this->assertSame(0, GameData::number(null));
    }

    public function test_int_map_turns_key_value_state_into_integers(): void
    {
        $this->assertSame(['lair' => 9, 'hatchery' => 9], GameData::intMap(['lair' => '9', 'hatchery' => 9]));
        $this->assertSame([], GameData::intMap(null));
    }

    public function test_hero_rows_turn_repeater_rows_into_the_stored_map(): void
    {
        $rows = [['hero' => 'tunneller', 'count' => '2'], ['hero' => 'archer', 'count' => 1], ['count' => 3]];

        $this->assertSame(['tunneller' => 2, 'archer' => 1], GameData::heroRows($rows));
        $this->assertSame([], GameData::heroRows('nonsense'));
    }

    /** @return array<string, array{mixed, string}> */
    public static function formats(): array
    {
        return [
            'null' => [null, '—'],
            'empty string' => ['', '—'],
            'bool' => [true, 'yes'],
            'int' => [1500, '1500'],
            'float' => [1.50, '1.5'],
            'empty array' => [[], 'none'],
            'room sizes' => [['lair' => 9, 'hatchery' => 9], 'lair 9, hatchery 9'],
        ];
    }

    #[DataProvider('formats')]
    public function test_default_values_are_formatted_for_people(mixed $value, string $expected): void
    {
        $this->assertSame($expected, DefaultValue::format($value));
    }
}
