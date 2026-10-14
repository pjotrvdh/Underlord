<?php

namespace App\Filament\Resources\Creatures\Pages;

use App\Filament\Resources\Creatures\CreatureResource;
use Filament\Resources\Pages\ListRecords;

class ListCreatures extends ListRecords
{
    protected static string $resource = CreatureResource::class;
}
