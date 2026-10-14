<?php

namespace App\Filament\Resources\Rooms\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->inConfigOrder())
            ->paginated(false)
            ->columns([
                ColorColumn::make('color'),
                TextColumn::make('name')->weight('bold')->description(fn ($record) => $record->key),
                TextColumn::make('cost')->numeric()->suffix(' gold'),
                TextColumn::make('tip')->color('gray')->wrap(),
            ])
            ->recordActions([EditAction::make()]);
    }
}
