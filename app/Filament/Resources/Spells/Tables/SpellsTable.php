<?php

namespace App\Filament\Resources\Spells\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SpellsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->inConfigOrder())
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->weight('bold')->description(fn ($record) => $record->key),
                TextColumn::make('research')->numeric(),
                TextColumn::make('cost')->numeric()->suffix(' gold'),
                TextColumn::make('params')->label('Effect')->color('gray')
                    ->state(fn ($record) => collect($record->params)->map(fn ($v, $k) => "{$k} {$v}")->join(', ')),
            ])
            ->recordActions([EditAction::make()]);
    }
}
