<?php

namespace App\Filament\Resources\Spells\Tables;

use App\Filament\Support\DefaultValue;
use App\Models\Spell;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpellsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(Spell::orderByConfig(...))
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->weight('bold')->description(fn (Spell $record): string => $record->key),
                TextColumn::make('research')->numeric(),
                TextColumn::make('cost')->numeric()->suffix(' gold'),
                TextColumn::make('params')->label('Effect')->color('gray')
                    ->state(fn (Spell $record): string => DefaultValue::format($record->params ?? [])),
            ])
            ->recordActions([EditAction::make()]);
    }
}
