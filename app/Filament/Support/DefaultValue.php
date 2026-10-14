<?php

namespace App\Filament\Support;

use App\Models\Concerns\HasConfigDefaults;
use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Model;

/**
 * Shows the config default (config/underlord/*.php) next to a field, and colours it when the
 * stored value differs. Registered as the ->withDefault() macro in AdminPanelProvider.
 */
class DefaultValue
{
    public static function register(): void
    {
        Field::macro('withDefault', function (?string $field = null): Field {
            /** @var Field $this */
            $field ??= $this->getName();

            return $this
                ->hint(DefaultValue::hint($field))
                ->hintColor(DefaultValue::color($field));
        });
    }

    public static function hint(string $field): Closure
    {
        return fn (?Model $record): ?string => self::has($record)
            ? __('resource.default', ['value' => self::format($record->defaultValue($field))])
            : null;
    }

    public static function color(string $field): Closure
    {
        return fn (?Model $record, mixed $state): string => self::has($record) && ! self::same($state, $record->defaultValue($field))
            ? 'warning'
            : 'gray';
    }

    /** True when the value differs from the default; used to highlight changes in tables. */
    public static function changed(Model $record, string $field, mixed $value): bool
    {
        return self::has($record) && ! self::same($value, $record->defaultValue($field));
    }

    public static function format(mixed $value): string
    {
        return match (true) {
            $value === null, $value === '' => '—',
            is_bool($value) => $value ? 'yes' : 'no',
            is_float($value) => rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.'),
            is_array($value) => $value === [] ? 'none' : collect($value)->map(fn ($v, $k) => is_int($k) ? self::format($v) : "{$k} ".self::format($v))->join(', '),
            default => (string) $value,
        };
    }

    private static function has(?Model $record): bool
    {
        return $record !== null
            && in_array(HasConfigDefaults::class, class_uses_recursive($record), true)
            && $record->configDefaults() !== null;
    }

    /** Form state is often a string ("1500") or a repeater list, so compare normalised values. */
    private static function same(mixed $state, mixed $default): bool
    {
        if (is_array($state) && array_is_list($state) && isset($state[0]['hero'])) {
            $state = collect($state)->mapWithKeys(fn ($row) => [$row['hero'] => (int) $row['count']])->all();
        }
        if (is_array($state) && is_array($default)) {
            return collect($state)->map(fn ($v) => is_numeric($v) ? $v + 0 : $v)->all() == $default;
        }
        if (is_numeric($state) && is_numeric($default)) {
            return (float) $state === (float) $default;
        }

        return (string) self::format($state) === (string) self::format($default);
    }
}
