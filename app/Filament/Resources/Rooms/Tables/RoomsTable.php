<?php

namespace App\Filament\Resources\Rooms\Tables;

use App\Models\Room;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(Room::orderByConfig(...))
            ->paginated(false)
            ->columns([
                ColorColumn::make('color'),
                TextColumn::make('name')->weight('bold')->description(fn (Room $record): string => $record->key),
                TextColumn::make('cost')->numeric()->suffix(' gold'),
                TextColumn::make('tip')->color('gray')->wrap(),
            ])
            ->recordActions([EditAction::make()]);
    }
}
