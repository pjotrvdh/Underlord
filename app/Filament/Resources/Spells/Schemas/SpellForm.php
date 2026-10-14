<?php

namespace App\Filament\Resources\Spells\Schemas;

use App\Filament\Schemas\FormLayout;
use App\Filament\Support\DefaultValue;
use App\Support\GameData;
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
                        DefaultValue::on(TextInput::make('name'))->label(__('resource.spell.fields.name'))->required()->columnSpanFull(),
                        DefaultValue::on(TextInput::make('research'))->label(__('resource.spell.fields.research'))->helperText(__('resource.spell.helpers.research'))->required()->numeric()->minValue(0),
                        DefaultValue::on(TextInput::make('cost'))->label(__('resource.spell.fields.cost'))->helperText(__('resource.spell.helpers.cost'))->required()->numeric()->minValue(0)->suffix('gold'),
                        DefaultValue::on(KeyValue::make('params'))
                            ->label(__('resource.spell.fields.params'))
                            ->helperText(__('resource.spell.helpers.params'))
                            ->addable(false)->deletable(false)->editableKeys(false)
                            ->columnSpanFull()
                            ->dehydrateStateUsing(static fn (mixed $state): array => array_map(GameData::number(...), is_array($state) ? $state : [])),
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
