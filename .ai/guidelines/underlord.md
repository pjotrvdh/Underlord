# Underlord

Underlord is a Dungeon Keeper-style browser game on Laravel. The owner talks to you in **Dutch**, so reply in Dutch; code, UI text and docs are in English.

## Stack

- Laravel 13, PHP 8.4, MySQL 8, Filament 5 (admin panel at `/admin`) and Vite, running on DDEV at https://underlord.ddev.site.
- The game client is `resources/js/underlord.js` plus `resources/css/underlord.css`, rendered by `resources/views/game.blade.php`. Laravel puts the game config into `window.UNDERLORD`. After a frontend change, run `ddev exec npm run build`.

## Game data

- **Only creatures and heroes from the original Dungeon Keeper (1997)** are in the game; don't introduce others.
- **Defaults live in config:** `config/underlord/{settings,creatures,rooms,spells,waves}.php`. The config order is the order used everywhere.
- **The database holds the current values,** keyed by handle: `troll`, `lair`, `bolt`, `economy.startGold`; waves use their number.
- `App\Services\GameDefaults` adds missing defaults after every `artisan migrate`, without overwriting edits. `ddev exec artisan underlord:defaults --reset` restores everything.
- **The admin panel only edits game data.** It never creates or deletes it. New game data goes into config.
- **Game data tables have no `created_at`.** They have `updated_at` and `updated_by`, which is set from the logged-in user.
- **Assets** (SVGs) and the **colours** of creatures and rooms are managed in the project, not in the database: `config/underlord/assets.php`, `public/assets/` and the `color` entries in the config. The admin panel shows them read-only.
- `App\Services\GameConfig` builds and caches the object the game reads. Model events clear that cache.

## Checks

These are the same checks CI runs (`.github/workflows`):

- `ddev composer test`: the test suite, on in-memory SQLite.
- `ddev composer analyse`: PHPStan/Larastan at the max level.
- `ddev composer format-check`: Rector (dry run) and Pint. `ddev composer format` applies both.

## Git

- Commit messages are a single short subject line, with no body and no co-author or attribution trailer.
- Pull requests are always created as **drafts**.
