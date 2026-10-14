<?php

namespace App\Filament\Resources\Creatures\Pages;

use App\Filament\Resources\Creatures\CreatureResource;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditCreature extends EditRecord
{
    protected static string $resource = CreatureResource::class;

    #[Override]
    protected function getRedirectUrl(): ?string
    {
        $url = self::getResource()::getUrl('index');

        return is_string($url) ? $url : null;
    }
}
