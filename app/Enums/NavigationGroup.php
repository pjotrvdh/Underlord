<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup: string implements HasLabel
{
    case GameSettings = 'game-settings';
    case Players = 'players';

    public function getLabel(): string
    {
        return match ($this) {
            self::GameSettings => 'Game settings',
            self::Players => 'Players',
        };
    }
}
