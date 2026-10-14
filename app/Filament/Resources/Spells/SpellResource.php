<?php

declare(strict_types=1);

namespace App\Filament\Resources\Spells;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Concerns\ManagedByApp;
use App\Filament\Resources\Resource;
use App\Filament\Resources\Spells\Pages\EditSpell;
use App\Filament\Resources\Spells\Pages\ListSpells;
use App\Filament\Resources\Spells\Schemas\SpellForm;
use App\Filament\Resources\Spells\Tables\SpellsTable;
use App\Models\Spell;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class SpellResource extends Resource
{
    use ManagedByApp;

    protected static ?string $model = Spell::class;

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::Sparkles;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::GameSettings;

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return SpellForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return SpellsTable::configure($table);
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
            'index' => ListSpells::route('/'),
            'edit' => EditSpell::route('/{record}/edit'),
        ];
    }
}
