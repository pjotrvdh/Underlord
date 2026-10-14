<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Filament\Schemas\FormLayout;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormLayout::make(
                    main: [
                        Flex::make([
                            TextInput::make('name')->withDefault()->label(__('resource.room.fields.name'))->required(),
                            ColorPicker::make('color')->withDefault()->label(__('resource.room.fields.color'))->required()->grow(false),
                        ])
                            ->from('md')
                            ->columnSpanFull(),
                        TextInput::make('cost')->withDefault()->label(__('resource.room.fields.cost'))->required()->numeric()->minValue(0)->suffix('gold per tile'),
                        TextInput::make('tip')->withDefault()->label(__('resource.room.fields.tip'))->required()->columnSpanFull(),
                    ],
                    aside: [
                        TextEntry::make('key')
                            ->label(__('resource.room.fields.key'))
                            ->helperText(__('resource.room.helpers.key'))
                            ->fontFamily('mono'),
                    ],
                ),
            ]);
    }
}
