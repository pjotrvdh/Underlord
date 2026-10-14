<?php

namespace App\Providers\Filament;

use App\Enums\NavigationGroup;
use App\Filament\Support\DefaultValue;
use App\Models\User;
use Awcodes\Gravatar\GravatarPlugin;
use Awcodes\Gravatar\GravatarProvider;
use Awcodes\LightSwitch\LightSwitchPlugin;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use DutchCodingCompany\FilamentDeveloperLogins\FilamentDeveloperLoginsPlugin;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup as FilamentNavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;

/**
 * Panel setup follows the SWIS conventions used in wherermeorders.
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Underlord')
            ->unsavedChangesAlerts()
            ->defaultAvatarProvider(GravatarProvider::class)
            ->spa()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->registration(null)
            ->passwordReset()
            ->profile(isSimple: false)
            ->maxContentWidth('full')
            ->sidebarWidth('fit-content')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                FilamentNavigationGroup::make(NavigationGroup::GameSettings->getLabel())->collapsible(false),
                FilamentNavigationGroup::make(NavigationGroup::Players->getLabel())->collapsible(false),
            ])
            ->navigationItems([
                NavigationItem::make('Play Underlord')
                    ->url(fn (): string => route('game'))
                    ->icon(LucideIcon::Castle)
                    ->sort(-1),
            ])
            ->colors([
                'primary' => Color::Red,
                'secondary' => Color::Neutral,
                'success' => Color::Green,
                'danger' => Color::Rose,
                'warning' => Color::Orange,
                'info' => Color::Teal,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ], isPersistent: true)
            ->plugins([
                EnvironmentIndicatorPlugin::make()
                    ->color(static fn (): array => match (app()->environment()) {
                        'production' => Color::Red,
                        'staging' => Color::Orange,
                        default => Color::Amber,
                    })
                    ->showGitBranch(),
                FilamentBackgroundsPlugin::make(),
                FilamentDeveloperLoginsPlugin::make()
                    ->enabled(app()->environment('local'))
                    ->switchable(false)
                    ->users(fn () => User::query()->where('is_admin', true)->pluck('email', 'name')->toArray()),
                GravatarPlugin::make(),
                LightSwitchPlugin::make(),
            ]);
    }

    public function boot(): void
    {
        FilamentColor::register(Color::all());
        DefaultValue::register();

        Select::configureUsing(static fn (Select $select): Select => $select->native(false));
        SelectFilter::configureUsing(static fn (SelectFilter $selectFilter): SelectFilter => $selectFilter->native(false));
        Table::configureUsing(static fn (Table $table): Table => $table
            ->stackedOnMobile()
            ->deferFilters(false)
            ->deferColumnManager(false)
            ->paginationPageOptions([10, 25, 50])
            ->defaultPaginationPageOption(25));
        CreateAction::configureUsing(static fn (CreateAction $createAction): CreateAction => $createAction->icon(LucideIcon::SquarePlus));
        ViewAction::configureUsing(static fn (ViewAction $viewAction): ViewAction => $viewAction->icon(LucideIcon::Eye));
        EditAction::configureUsing(static fn (EditAction $editAction): EditAction => $editAction->icon(LucideIcon::SquarePen));
        DeleteAction::configureUsing(static fn (DeleteAction $deleteAction): DeleteAction => $deleteAction->icon(LucideIcon::Trash2));
        AttachAction::configureUsing(static fn (AttachAction $attachAction): AttachAction => $attachAction->icon(LucideIcon::Link));
        DetachAction::configureUsing(static fn (DetachAction $detachAction): DetachAction => $detachAction->icon(LucideIcon::Unlink));
    }
}
