<?php

namespace App\Filament\Resources\Creatures\Schemas;

use App\Filament\Schemas\FormLayout;
use App\Models\Creature;
use App\Models\Room;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class CreatureForm
{
    public static function configure(Schema $schema): Schema
    {
        $isHero = fn (?Creature $record): bool => $record?->side === Creature::SIDE_HERO;

        return $schema
            ->components([
                FormLayout::make(
                    main: [
                        TextInput::make('name')->withDefault()
                            ->label(__('resource.creature.fields.name'))
                            ->required()
                            ->columnSpanFull(),
                        Fieldset::make(__('resource.creature.fields.combat'))
                            ->columns(['default' => 1, 'xl' => 3])
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('hp')->withDefault()->label(__('resource.creature.fields.hp'))->required()->numeric()->minValue(1),
                                TextInput::make('attack')->withDefault()->label(__('resource.creature.fields.attack'))->required()->numeric()->minValue(0),
                                TextInput::make('speed')->withDefault()->label(__('resource.creature.fields.speed'))->helperText(__('resource.creature.helpers.speed'))->required()->numeric()->step(0.1),
                                TextInput::make('range')->withDefault()->label(__('resource.creature.fields.range'))->helperText(__('resource.creature.helpers.range'))->required()->numeric()->step(0.5),
                                TextInput::make('rate')->withDefault()->label(__('resource.creature.fields.rate'))->helperText(__('resource.creature.helpers.rate'))->required()->numeric()->step(0.1)->suffix('s'),
                                TextInput::make('vision')->withDefault()->label(__('resource.creature.fields.vision'))->numeric()->suffix('tiles')->hidden($isHero),
                            ]),
                        Fieldset::make(__('resource.creature.fields.your_creature'))
                            ->columns(['default' => 1, 'xl' => 2])
                            ->columnSpanFull()
                            ->hidden($isHero)
                            ->schema([
                                TextInput::make('wage')->withDefault()->label(__('resource.creature.fields.wage'))->helperText(__('resource.creature.helpers.wage'))->numeric()->suffix('gold'),
                                TextInput::make('pool')->withDefault()->label(__('resource.creature.fields.pool'))->helperText(__('resource.creature.helpers.pool'))->numeric(),
                                KeyValue::make('attract')->withDefault()
                                    ->label(__('resource.creature.fields.attract'))
                                    ->helperText(__('resource.creature.helpers.attract', ['rooms' => implode(', ', Room::KEYS)]))
                                    ->keyLabel('Room')->valueLabel('Minimum tiles')
                                    ->columnSpanFull()
                                    ->dehydrateStateUsing(fn ($state) => collect($state ?? [])->map(fn ($v) => (int) $v)->all()),
                            ]),
                        Fieldset::make(__('resource.creature.fields.hero'))
                            ->columns(['default' => 1, 'xl' => 3])
                            ->columnSpanFull()
                            ->visible($isHero)
                            ->schema([
                                TextInput::make('bounty')->withDefault()->label(__('resource.creature.fields.bounty'))->helperText(__('resource.creature.helpers.bounty'))->numeric()->suffix('gold'),
                                TextInput::make('dig')->withDefault()->label(__('resource.creature.fields.dig'))->helperText(__('resource.creature.helpers.dig'))->numeric()->step(0.1)->suffix('s'),
                                Toggle::make('big')->withDefault()->label(__('resource.creature.fields.big'))->inline(false),
                            ]),
                    ],
                    aside: [
                        ImageEntry::make('asset')
                            ->hiddenLabel()
                            ->state(fn (?Creature $record): ?string => $record?->assetUrl())
                            ->imageSize(128)
                            ->extraImgAttributes(['style' => 'background:#241c17;border-radius:8px;padding:8px'])
                            ->visible(fn (?Creature $record): bool => $record?->assetUrl() !== null),
                        ColorEntry::make('color')
                            ->label(__('resource.creature.fields.color')),
                        TextEntry::make('key')
                            ->label(__('resource.creature.fields.key'))
                            ->helperText(__('resource.creature.helpers.key'))
                            ->fontFamily('mono'),
                        TextEntry::make('side')
                            ->label(__('resource.creature.fields.side'))
                            ->badge()
                            ->color(fn (string $state): string => $state === Creature::SIDE_HERO ? 'info' : 'danger')
                            ->formatStateUsing(fn (string $state): string => __("resource.creature.sides.{$state}")),
                    ],
                ),
            ]);
    }
}
