<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Filament\Schemas\FormLayout;
use App\Filament\Support\DefaultValue;
use App\Models\Setting;
use App\Support\GameData;
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
                        DefaultValue::on(TextInput::make('value'))
                            ->label(__('resource.setting.fields.value'))
                            ->helperText(fn (?Setting $record): ?string => $record?->description())
                            ->required()
                            ->numeric()
                            ->dehydrateStateUsing(GameData::number(...)),
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
