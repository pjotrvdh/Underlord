<?php

namespace App\Filament\Support;

use App\Models\Contracts\HasDefaults;
use App\Support\GameData;
use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Model;

/**
 * Shows the config default (config/underlord/*.php) next to a field, and colours it when the
 * stored value differs. Use DefaultValue::on(TextInput::make('hp')).
 */
class DefaultValue
{
    /**
     * Add the default hint to a field: DefaultValue::on(TextInput::make('hp'))->….
     *
     * @template TField of Field
     *
     * @param  TField  $field
     * @return TField
     */
    public static function on(Field $field, ?string $name = null): Field
    {
        $name ??= $field->getName();

        $field
            ->hint(self::hint($name))
            ->hintColor(self::color($name));

        return $field;
    }

    public static function hint(string $field): Closure
    {
        return static fn (?Model $record): ?string => self::defaults($record) instanceof HasDefaults
            ? GameData::trans('resource.default', ['value' => self::format(self::defaults($record)->defaultValue($field))])
            : null;
    }

    public static function color(string $field): Closure
    {
        return static fn (?Model $record, mixed $state): string => self::changed($record, $field, $state) ? 'warning' : 'gray';
    }

    /** True when the value differs from the default; used to highlight changes in tables. */
    public static function changed(?Model $record, string $field, mixed $value): bool
    {
        $defaults = self::defaults($record);

        return $defaults instanceof HasDefaults && ! self::same($value, $defaults->defaultValue($field));
    }

    public static function format(mixed $value): string
    {
        return match (true) {
            $value === null, $value === '' => '—',
            is_bool($value) => $value ? 'yes' : 'no',
            is_float($value) => rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.'),
            is_int($value), is_string($value) => (string) $value,
            is_array($value) => $value === [] ? 'none' : collect($value)->map(static fn (mixed $v, int|string $k): string => is_int($k) ? self::format($v) : $k.' '.self::format($v))->join(', '),
            default => '?',
        };
    }

    /** The record, when it is a game data record that has config defaults. */
    private static function defaults(?Model $record): ?HasDefaults
    {
        return $record instanceof HasDefaults && $record->configDefaults() !== null ? $record : null;
    }

    /** Form state is often a string ("1500") or a repeater list, so compare normalised values. */
    private static function same(mixed $state, mixed $default): bool
    {
        if (is_array($state) && array_is_list($state) && is_array($state[0] ?? null) && isset($state[0]['hero'])) {
            $state = GameData::heroRows($state);
        }

        if (is_array($state) && is_array($default)) {
            return array_map(static fn (mixed $v): mixed => is_numeric($v) ? $v + 0 : $v, $state) == $default;
        }

        if (is_numeric($state) && is_numeric($default)) {
            return (float) $state === (float) $default;
        }

        return self::format($state) === self::format($default);
    }
}
