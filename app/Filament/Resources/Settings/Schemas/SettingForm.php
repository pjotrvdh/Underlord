<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Filament\Schemas\FormLayout;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormLayout::make(
                    main: [
                        TextInput::make('value')->withDefault()
                            ->label(__('resource.setting.fields.value'))
                            ->helperText(fn ($record) => $record?->description())
                            ->required()
                            ->numeric()
                            ->dehydrateStateUsing(fn ($state) => $state + 0),
                    ],
                    aside: [
                        TextEntry::make('group')->label(__('resource.setting.fields.group'))->badge(),
                        TextEntry::make('key')->label(__('resource.setting.fields.key'))->fontFamily('mono'),
                    ],
                    mainColumns: 1,
                ),
            ]);
    }
}
