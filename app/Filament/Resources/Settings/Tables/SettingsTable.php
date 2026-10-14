<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Filament\Support\DefaultValue;
use App\Models\Setting;
use App\Support\GameData;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Groups and settings appear in the order of config/underlord/settings.php.
            ->defaultGroup(Group::make('group')->titlePrefixedWithLabel(false)->orderQueryUsing(Setting::orderByConfig(...)))
            ->modifyQueryUsing(Setting::orderByConfig(...))
            ->paginated(false)
            ->columns([
                TextColumn::make('key')
                    ->label(__('resource.setting.fields.key'))
                    ->formatStateUsing(fn (Setting $record): string => $record->name())
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Setting $record): ?string => $record->description()),
                TextInputColumn::make('value')
                    ->label(__('resource.setting.fields.value'))
                    ->rules(['required', 'numeric'])
                    ->updateStateUsing(function (Setting $record, $state) {
                        $record->update(['value' => GameData::number($state)]);

                        return $state;
                    }),
                TextColumn::make('default')
                    ->label(__('resource.setting.fields.default'))
                    ->state(fn (Setting $record): string => DefaultValue::format($record->defaultValue('value')))
                    ->color(fn (Setting $record): string => DefaultValue::changed($record, 'value', $record->value) ? 'warning' : 'gray')
                    ->icon(fn (Setting $record): ?string => DefaultValue::changed($record, 'value', $record->value) ? 'heroicon-m-pencil' : null)
                    ->tooltip(fn (Setting $record): ?string => DefaultValue::changed($record, 'value', $record->value) ? GameData::trans('resource.setting.changed') : null),
            ])
            ->recordActions([EditAction::make()]);
    }
}
