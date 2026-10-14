<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Filament\Support\DefaultValue;
use App\Models\Setting;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Groups and settings appear in the order of config/underlord/settings.php.
            ->defaultGroup(Group::make('group')->titlePrefixedWithLabel(false)->orderQueryUsing(fn (Builder $query) => $query->inConfigOrder()))
            ->modifyQueryUsing(fn (Builder $query) => $query->inConfigOrder())
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
                        $record->update(['value' => $state + 0]);

                        return $state;
                    }),
                TextColumn::make('default')
                    ->label(__('resource.setting.fields.default'))
                    ->state(fn (Setting $record): string => DefaultValue::format($record->defaultValue('value')))
                    ->color(fn (Setting $record): string => DefaultValue::changed($record, 'value', $record->value) ? 'warning' : 'gray')
                    ->icon(fn (Setting $record): ?string => DefaultValue::changed($record, 'value', $record->value) ? 'heroicon-m-pencil' : null)
                    ->tooltip(fn (Setting $record): ?string => DefaultValue::changed($record, 'value', $record->value) ? __('resource.setting.changed') : null),
            ])
            ->recordActions([EditAction::make()]);
    }
}
