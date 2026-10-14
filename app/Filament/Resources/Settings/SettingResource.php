<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Concerns\ManagedByApp;
use App\Filament\Resources\Resource;
use App\Filament\Resources\Settings\Pages\EditSetting;
use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Filament\Resources\Settings\Schemas\SettingForm;
use App\Filament\Resources\Settings\Tables\SettingsTable;
use App\Models\Setting;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Override;
use UnitEnum;

class SettingResource extends Resource
{
    use ManagedByApp;

    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::SlidersHorizontal;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::GameSettings;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'key';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
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
            'index' => ListSettings::route('/'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
