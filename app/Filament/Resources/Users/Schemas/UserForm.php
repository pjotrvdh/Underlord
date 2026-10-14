<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Schemas\FormLayout;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormLayout::make(
                    main: [
                        Flex::make([
                            TextInput::make('name')->label(__('resource.user.fields.name'))->required(),
                            TextInput::make('email')->label(__('resource.user.fields.email'))->email()->required()->unique(ignoreRecord: true),
                        ])
                            ->from('md')
                            ->columnSpanFull(),
                        TextInput::make('password')
                            ->label(__('resource.user.fields.password'))
                            ->helperText(__('resource.user.helpers.password'))
                            ->password()->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn ($state): bool => filled($state)),
                    ],
                    aside: [
                        Toggle::make('is_admin')
                            ->label(__('resource.user.fields.is_admin'))
                            ->helperText(__('resource.user.helpers.is_admin')),
                    ],
                ),
            ]);
    }
}
