<?php

namespace App\Filament\Resources\Spells\Schemas;

use App\Filament\Schemas\FormLayout;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SpellForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormLayout::make(
                    main: [
                        TextInput::make('name')->withDefault()->label(__('resource.spell.fields.name'))->required()->columnSpanFull(),
                        TextInput::make('research')->withDefault()->label(__('resource.spell.fields.research'))->helperText(__('resource.spell.helpers.research'))->required()->numeric()->minValue(0),
                        TextInput::make('cost')->withDefault()->label(__('resource.spell.fields.cost'))->helperText(__('resource.spell.helpers.cost'))->required()->numeric()->minValue(0)->suffix('gold'),
                        KeyValue::make('params')->withDefault()
                            ->label(__('resource.spell.fields.params'))
                            ->helperText(__('resource.spell.helpers.params'))
                            ->addable(false)->deletable(false)->editableKeys(false)
                            ->columnSpanFull()
                            ->dehydrateStateUsing(fn ($state) => collect($state ?? [])->map(fn ($v) => $v + 0)->all()),
                    ],
                    aside: [
                        TextEntry::make('key')
                            ->label(__('resource.spell.fields.key'))
                            ->helperText(__('resource.spell.helpers.key'))
                            ->fontFamily('mono'),
                    ],
                ),
            ]);
    }
}
