<?php

namespace App\Filament\Resources\Rooms\Pages;

use App\Filament\Resources\Rooms\RoomResource;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditRoom extends EditRecord
{
    protected static string $resource = RoomResource::class;

    #[Override]
    protected function getRedirectUrl(): ?string
    {
        $url = self::getResource()::getUrl('index');

        return is_string($url) ? $url : null;
    }
}
