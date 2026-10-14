<?php

namespace App\Filament\Resources\Waves\Tables;

use App\Filament\Support\DefaultValue;
use App\Models\Creature;
use App\Models\Wave;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WavesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(Wave::orderByConfig(...))
            ->paginated(false)
            ->columns([
                TextColumn::make('number')->label('Wave')->weight('bold'),
                TextColumn::make('heroes')->state(function (Wave $record): string {
                    $names = Creature::query()->pluck('name', 'key');

                    return collect($record->heroes)->map(static fn (int $n, string $k): string => $n.' × '.DefaultValue::format($names->get($k, $k)))->join(', ');
                }),
            ])
            ->recordActions([EditAction::make()]);
    }
}
