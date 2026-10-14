<?php

declare(strict_types=1);

namespace App\Filament\Resources\Rooms;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Concerns\ManagedByApp;
use App\Filament\Resources\Resource;
use App\Filament\Resources\Rooms\Pages\EditRoom;
use App\Filament\Resources\Rooms\Pages\ListRooms;
use App\Filament\Resources\Rooms\Schemas\RoomForm;
use App\Filament\Resources\Rooms\Tables\RoomsTable;
use App\Models\Room;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class RoomResource extends Resource
{
    use ManagedByApp;

    protected static ?string $model = Room::class;

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::LayoutGrid;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::GameSettings;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return RoomForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return RoomsTable::configure($table);
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
            'index' => ListRooms::route('/'),
            'edit' => EditRoom::route('/{record}/edit'),
        ];
    }
}
