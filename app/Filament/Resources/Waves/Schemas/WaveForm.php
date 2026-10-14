<?php

namespace App\Filament\Resources\Waves\Schemas;

use App\Filament\Schemas\FormLayout;
use App\Filament\Support\DefaultValue;
use App\Models\Creature;
use App\Support\GameData;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WaveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormLayout::make(
                    main: [
                        // Stored as {"dwarf": 2, ...}; edited as a list of hero + count rows.
                        DefaultValue::on(Repeater::make('heroes'))
                            ->label(__('resource.wave.fields.heroes'))
                            ->columns(2)
                            ->minItems(1)
                            ->columnSpanFull()
                            ->formatStateUsing(static fn (mixed $state): array => collect(is_array($state) ? $state : [])->map(static fn (mixed $n, int|string $k): array => ['hero' => $k, 'count' => $n])->values()->all())
                            ->dehydrateStateUsing(GameData::heroRows(...))
                            ->schema([
                                Select::make('hero')
                                    ->label(__('resource.wave.fields.hero'))
                                    ->required()
                                    ->options(fn () => Creature::query()->where('side', Creature::SIDE_HERO)->inConfigOrder()->pluck('name', 'key')),
                                TextInput::make('count')
                                    ->label(__('resource.wave.fields.count'))
                                    ->required()->numeric()->minValue(1)->default(1),
                            ]),
                    ],
                    aside: [
                        TextEntry::make('number')
                            ->label(__('resource.wave.fields.number'))
                            ->helperText(__('resource.wave.helpers.number')),
                    ],
                ),
            ]);
    }
}
