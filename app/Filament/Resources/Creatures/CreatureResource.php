<?php

declare(strict_types=1);

namespace App\Filament\Resources\Creatures;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Concerns\ManagedByApp;
use App\Filament\Resources\Creatures\Pages\EditCreature;
use App\Filament\Resources\Creatures\Pages\ListCreatures;
use App\Filament\Resources\Creatures\Schemas\CreatureForm;
use App\Filament\Resources\Creatures\Tables\CreaturesTable;
use App\Filament\Resources\Resource;
use App\Models\Creature;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class CreatureResource extends Resource
{
    use ManagedByApp;

    protected static ?string $model = Creature::class;

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::Skull;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::GameSettings;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return CreatureForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return CreaturesTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListCreatures::route('/'),
            'edit' => EditCreature::route('/{record}/edit'),
        ];
    }
}
