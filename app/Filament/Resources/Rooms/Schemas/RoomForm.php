<?php

namespace App\Filament\Resources\Rooms\Schemas;

use App\Filament\Schemas\FormLayout;
use App\Filament\Support\DefaultValue;
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
                        DefaultValue::on(TextInput::make('name'))->label(__('resource.room.fields.name'))->required()
                            ->columnSpanFull(),
                        DefaultValue::on(TextInput::make('cost'))->label(__('resource.room.fields.cost'))->required()->numeric()->minValue(0)->suffix('gold per tile'),
                        DefaultValue::on(TextInput::make('tip'))->label(__('resource.room.fields.tip'))->required()->columnSpanFull(),
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
