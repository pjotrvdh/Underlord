<?php

namespace App\Filament\Resources\Waves;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Concerns\ManagedByApp;
use App\Filament\Resources\Resource;
use App\Filament\Resources\Waves\Pages\EditWave;
use App\Filament\Resources\Waves\Pages\ListWaves;
use App\Filament\Resources\Waves\Schemas\WaveForm;
use App\Filament\Resources\Waves\Tables\WavesTable;
use App\Models\Wave;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class WaveResource extends Resource
{
    use ManagedByApp;

    protected static ?string $model = Wave::class;

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::Swords;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::GameSettings;

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'number';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return WaveForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return WavesTable::configure($table);
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
            'index' => ListWaves::route('/'),
            'edit' => EditWave::route('/{record}/edit'),
        ];
    }
}
