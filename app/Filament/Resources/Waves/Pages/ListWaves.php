<?php

declare(strict_types=1);

namespace App\Filament\Resources\Waves\Pages;

use App\Filament\Resources\Waves\WaveResource;
use Filament\Resources\Pages\ListRecords;

class ListWaves extends ListRecords
{
    protected static string $resource = WaveResource::class;
}
