<?php

namespace App\Filament\Resources\Spells\Pages;

use App\Filament\Resources\Spells\SpellResource;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditSpell extends EditRecord
{
    protected static string $resource = SpellResource::class;

    #[Override]
    protected function getRedirectUrl(): ?string
    {
        $url = self::getResource()::getUrl('index');

        return is_string($url) ? $url : null;
    }
}
