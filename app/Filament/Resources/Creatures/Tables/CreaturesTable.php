<?php

namespace App\Filament\Resources\Creatures\Tables;

use App\Models\Creature;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CreaturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(Creature::orderByConfig(...))
            ->paginated(false)
            ->columns([
                ImageColumn::make('asset')
                    ->label('')
                    ->state(fn (Creature $record): ?string => $record->assetUrl())
                    ->imageSize(44)
                    ->extraImgAttributes(['style' => 'background:#241c17;border-radius:6px;padding:3px']),
                ColorColumn::make('color'),
                TextColumn::make('name')->searchable()->weight('bold')->description(fn (Creature $r) => $r->key),
                TextColumn::make('side')->badge()->color(fn (string $state): string => $state === Creature::SIDE_HERO ? 'info' : 'danger'),
                TextColumn::make('hp')->label('Health')->numeric()->sortable(),
                TextColumn::make('attack')->numeric()->sortable(),
                TextColumn::make('speed')->numeric(1),
                TextColumn::make('range')->numeric(1),
                TextColumn::make('wage')->numeric()->sortable(),
                TextColumn::make('pool')->numeric(),
                TextColumn::make('attract')
                    ->state(fn (Creature $r) => $r->side === Creature::SIDE_HERO ? '—' : (collect($r->attract ?? [])->map(static fn (int $n, string $room): string => "{$room} {$n}+")->join(', ') ?: 'free bed'))
                    ->color('gray'),
                TextColumn::make('bounty')->numeric()->sortable(),
            ])
            ->filters([
                SelectFilter::make('side')->options([Creature::SIDE_CREATURE => 'Creatures', Creature::SIDE_HERO => 'Heroes']),
            ])
            ->recordActions([EditAction::make()]);
    }
}
