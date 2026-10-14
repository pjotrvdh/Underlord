<?php

namespace App\Filament\Resources\Waves\Pages;

use App\Filament\Resources\Waves\WaveResource;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditWave extends EditRecord
{
    protected static string $resource = WaveResource::class;

    #[Override]
    protected function getRedirectUrl(): ?string
    {
        $url = self::getResource()::getUrl('index');

        return is_string($url) ? $url : null;
    }
}
