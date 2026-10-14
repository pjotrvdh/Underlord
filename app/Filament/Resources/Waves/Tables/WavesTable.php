<?php

namespace App\Filament\Resources\Waves\Tables;

use App\Models\Creature;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WavesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->inConfigOrder())
            ->paginated(false)
            ->columns([
                TextColumn::make('number')->label('Wave')->weight('bold'),
                TextColumn::make('heroes')->state(function ($record) {
                    $names = Creature::pluck('name', 'key');

                    return collect($record->heroes)->map(fn ($n, $k) => "{$n} × ".($names[$k] ?? $k))->join(', ');
                }),
            ])
            ->recordActions([EditAction::make()]);
    }
}
