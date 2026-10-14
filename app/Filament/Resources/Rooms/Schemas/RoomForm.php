<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Filament\Schemas\FormLayout;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormLayout::make(
                    main: [
                        TextInput::make('name')->withDefault()->label(__('resource.room.fields.name'))->required()
                            ->columnSpanFull(),
                        TextInput::make('cost')->withDefault()->label(__('resource.room.fields.cost'))->required()->numeric()->minValue(0)->suffix('gold per tile'),
                        TextInput::make('tip')->withDefault()->label(__('resource.room.fields.tip'))->required()->columnSpanFull(),
                    ],
                    aside: [
                        ColorEntry::make('color')
                            ->label(__('resource.room.fields.color')),
                        TextEntry::make('key')
                            ->label(__('resource.room.fields.key'))
                            ->helperText(__('resource.room.helpers.key'))
                            ->fontFamily('mono'),
                    ],
                ),
            ]);
    }
}
