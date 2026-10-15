---
paths:
  - 'app/Filament/**'
---

# Filament

## Panel conventions
The panel is configured in `AdminPanelProvider`: SPA mode, Gravatar avatars, Lucide icons for actions and navigation, the `App\Enums\NavigationGroup` enum, and the plugins for the environment indicator, developer logins, impersonation, light switch and backgrounds. Resources extend `App\Filament\Resources\Resource`, which takes labels from `lang/en/resource.php`. Edit pages redirect to the index after saving.

## Forms
Build forms with `App\Filament\Schemas\FormLayout`: a wide main section, and an aside with the fixed facts plus the updated at/by timestamps. Wrap every game-data field in `DefaultValue::on(...)`, so it shows its config default and is flagged when changed.

## Game data is edit-only
Game data resources use the `ManagedByApp` trait: no create or delete pages, actions or bulk actions. Handles (key, side, wave number) are shown, not editable.

## Access
Only users with `is_admin` can open the panel. The in-game Cheats menu is not tied to admin: anyone unlocks it with the cheat code. Admins can impersonate players.
