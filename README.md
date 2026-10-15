# Underlord

A browser game in the spirit of *Dungeon Keeper*. You are an evil overlord deep underground: dig into a cloud-covered map, claim territory, build rooms, attract creatures through portals and defend your Dungeon Heart against six invasions from the surface.

The game runs on Laravel. Its settings (economy, creatures, rooms, spells and waves) live in the database, and admins edit them in a Filament admin panel. The original single-file version is on the [`legacy`](../../tree/legacy) branch.

## How to play

- **Dig:** click a block to mark it for digging, or drag to mark a whole rectangle. You can mark blocks inside the clouds too. Your imps dig the blocks out, carry the gold to the Heart or a Treasury, and claim the new floor.
- **Walls:** idle imps reinforce the earth walls around your territory. Heroes take much longer to tunnel through a reinforced wall.
- **Explore:** the map is hidden in clouds. Your units reveal what they can see, and walls block their sight. Gold seams and the two portals shine through the clouds. Hidden caverns hold treasure chests, wandering creatures that join you, and a camp of sleeping heroes.
- **Build:** you build rooms on claimed ground by clicking a tile or dragging a rectangle. The rooms are Treasury, Lair, Hatchery, Training Room and Library.
- **Creatures:** creatures only arrive through a portal you have claimed. Each one needs a free bed in your Lair. Stronger creatures also need rooms of 9 or more tiles. Each map has a limited pool of creatures, and they want their wages on payday.
- **Hand:** left-click a creature to pick it up (you can hold up to 8), then left-click your territory to drop it.
  - A creature dropped into a Training Room, Library, Lair or Hatchery starts working there right away, with a small bonus.
  - A dropped creature is dazed for a moment.
  - Dropping a creature on your own portal dismisses it.
  - Right-click slaps a creature; slapped imps work faster.
- **Spells:** Create Imp, Lightning, Heal and Call to Arms. You unlock the last three through research in the Library.
- **Control panel:** a minimap at the top (click it to jump there), then Info, Rooms, Spells and Creatures tabs. The Creatures tab shows, for each type, how many are idle, working and fighting. Click a number to pick one of them up, or right-click it to look at it.
- **Computer Assistant:** the **C** button on the minimap lets the computer play for you, in one of four modes: Aggressive, Defensive, Construction only or Move only.
- **Cheats:** type `iliketocheat` during the game to unlock the Cheats panel. It works for everyone, and a reload locks it again.
- **Saving:** logged-in players save to their account; guests save in their browser. The game also autosaves every two minutes.
- **Win:** survive all six hero waves. The last wave is led by the Lord of the Land. You lose when your Dungeon Heart is destroyed.

### Controls

| Action | Mouse / touch | Keyboard |
|---|---|---|
| Mark for digging / build | click or drag | |
| Slap | right-click | |
| Scroll the map | right- or middle-drag, the **Look** tool, two-finger drag | arrow keys / WASD |
| Zoom | mouse wheel, pinch, **+ / − / Fit** buttons | `+` / `-` |
| Pause | **Pause** button | `Space` / `P` |
| Game speed | **1×** button | `1` `2` `3` |
| Back to the hand tool | | `Esc` |
| Cheats panel (after typing `iliketocheat`) | **Cheats** button | `` ` `` |

## Getting started

You need [Docker](https://www.docker.com/) and [DDEV](https://ddev.com/). Everything else runs inside DDEV.

```bash
ddev start
ddev composer install
ddev artisan migrate:fresh --seed
ddev npm ci
ddev npm run build
```

- **Game:** https://underlord.ddev.site
- **Admin panel:** https://underlord.ddev.site/admin. Locally, a developer login is shown on the login page. The seeder creates `admin@underlord.test` with the password `password`.
- **Front-end development:** run `ddev npm run dev` instead of `build` for hot reloading.

## Game data

The game's data has its defaults in code, and its current values in the database:

| What | Defaults | Edited in |
|---|---|---|
| Settings (economy, Heart, portals, waves, …) | `config/underlord/settings.php` | admin panel → Settings |
| Creatures and heroes | `config/underlord/creatures.php` | admin panel → Creatures |
| Rooms | `config/underlord/rooms.php` | admin panel → Rooms |
| Spells | `config/underlord/spells.php` | admin panel → Spells |
| Hero waves | `config/underlord/waves.php` | admin panel → Waves |
| SVG graphics | `config/underlord/assets.php` and `public/assets/` | the project only |

- After every `migrate`, missing defaults are copied into the database. Records you edited in the admin panel are left alone.
- To put everything back to the defaults, run `ddev artisan underlord:defaults --reset`.
- In the admin panel, every field shows its default and turns orange when it has been changed.
- Game data can only be edited there. New creatures, rooms or settings are added in `config/underlord/`.
- The colours of creatures and rooms, and the SVGs, are managed in the project only.
- See [`public/assets/README.md`](public/assets/README.md) for the size and orientation of the SVGs.

## Admin panel

- Only users with **Admin** switched on can open `/admin`.
- Admins manage players under **Players** and can play as a player (impersonate) to check their game and saves.

## Static version (GitHub Pages)

`ddev artisan underlord:export-static` builds a static copy of the game in `build/static` (run `ddev npm run build` first). It uses the default settings from `config/underlord/`, needs no database or server, and saves games in the browser's localStorage. All paths are relative, so it also works from a sub-path.

- **Publishing:** the `github pages` workflow publishes it on every push to `main`.
- **Cheats:** the cheat code works here too.

## Development

| Command | What it does |
|---|---|
| `ddev composer test` | the test suite (PHPUnit, in-memory SQLite) |
| `ddev composer analyse` | PHPStan/Larastan at the max level |
| `ddev composer format-check` | Rector (dry run) and Pint |
| `ddev composer format` | apply Rector and Pint |

GitHub Actions runs the same checks on every pull request, plus a Composer and npm security audit.

- **Migrations** only go forward: there are no `down()` methods, and mistakes are fixed with a new migration.
- **AI agents:** the project uses [Laravel Boost](https://laravel.com/docs/ai). After cloning, run `ddev artisan boost:install` to generate the agent files (`AGENTS.md`, `CLAUDE.md`, MCP config). The project's own agent rules are in [`.ai/guidelines`](.ai/guidelines) and [`.ai/rules`](.ai/rules).
