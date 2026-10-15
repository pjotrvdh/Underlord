# Underlord

A browser game in the spirit of *Dungeon Keeper*. You are an evil overlord deep underground: dig into a cloud-covered map, claim territory, build rooms, attract creatures through portals and defend your Dungeon Heart against six invasions from the surface.

The game is a web page built with [Vite](https://vite.dev). All game data (settings, creatures, heroes, rooms, spells and waves) lives in plain JavaScript files under [`src/config`](src/config). Play it on [GitHub Pages](https://pjotrvdh.github.io/Underlord/).

## How to play

- **Dig:** click a block to mark it for digging, or drag to mark a whole rectangle. You can mark blocks inside the clouds too. Your imps dig the blocks out, carry the gold to the Heart or a Treasury, and claim the new floor.
- **Walls:** idle imps reinforce the earth walls around your territory. Heroes take much longer to tunnel through a reinforced wall.
- **Explore:** the map is hidden in clouds. Your units reveal what they can see, and walls block their sight. Gold seams and the two portals shine through the clouds. Hidden caverns hold treasure chests, wandering creatures that join you, and a camp of sleeping heroes.
- **Build:** you build rooms on claimed ground by clicking a tile or dragging a rectangle. The rooms are Treasury, Lair, Hatchery, Training Room and Library.
- **Creatures:** creatures only arrive through a portal you have claimed. Each one claims its own bed (a Lair tile) the first time it needs one; that tile is then locked for the others. Stronger creatures also need rooms of 9 or more tiles. Each map has a limited pool of creatures, and they want their wages on payday.
- **Hand:** left-click a creature to pick it up (you can hold up to 8), then left-click your territory to drop it.
  - A creature dropped into a Training Room, Library or Hatchery starts working there right away, with a small bonus.
  - Dropped on a free Lair tile, a creature moves its bed there and goes to sleep. Another creature's bed stays locked.
  - A dropped creature is dazed for a moment.
  - Dropping a creature on your own portal dismisses it.
  - Right-click slaps a creature; slapped imps work 25% faster.
- **Spells:** Create Imp, Call to Arms, Heal and Lightning, in the research order of Dungeon Keeper. You unlock the last three through research in the Library. Call to Arms is free on your own territory.
- **Control panel:** a minimap at the top (click it to jump there), then Info, Rooms, Spells and Creatures tabs. The Creatures tab shows, for each type, how many are idle, working and fighting. Click a number to pick one of them up, or right-click it to look at it.
- **Computer Assistant:** the **C** button on the minimap lets the computer play for you, in one of four modes: Aggressive, Defensive, Construction only or Move only.
- **Cheats:** type `iliketocheat` during the game to unlock the Cheats panel. A reload locks it again.
- **Saving:** games are saved in your browser (localStorage). The game also autosaves every two minutes.
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

**With [DDEV](https://ddev.com/):** `ddev start` installs the packages and runs the Vite dev server on https://underlord.ddev.site, with hot reloading. Run other commands inside DDEV too, for example `ddev npm run build`.

```bash
ddev start
```

**Without DDEV:** you need [Node.js](https://nodejs.org/) 24 or newer.

```bash
npm ci
npm run dev
```

`node_modules` is shared between your computer and DDEV, and Vite's bundler is different per platform. Stick to one of the two, or run `npm ci` again after switching (DDEV does that by itself on start).

| Command | What it does |
|---|---|
| `npm run dev` | start a dev server with hot reloading |
| `npm run build` | build the game into `dist/` |
| `npm run preview` | serve the build from `dist/` |

The build uses relative paths, so `dist/` works from any folder or sub-path. The `github pages` workflow builds and publishes it on every push to `main`. The commit hash and time of the build are shown below the Chronicle.

## Project layout

```
index.html              the game page
.ddev/                  DDEV: config.yaml and start-vite.sh, which runs the Vite dev server
public/                 copied as-is into the build
  assets/               SVG graphics (see public/assets/README.md)
  favicon.svg
src/
  main.js               entry point: styles, game and the version label
  game/underlord.js     the game itself
  styles/underlord.css
  config/               all game data, one file per item
    index.js            puts everything together for the game
    settings/           one file per group: economy, imp, heart, portal, waves, hand, assistant, walls
    creatures/          your creatures: imp.js, orc.js, warlock.js, troll.js, mistress.js
    heroes/             the invaders: tunneller.js, archer.js, wizard.js, knight.js, lord.js
    rooms/              treasury.js, lair.js, hatchery.js, training.js, library.js
    spells/             imp.js, bolt.js, heal.js, rally.js
    waves.js            the hero invasions, in order
    assets.js           which SVG the game draws for what
```

## Game data

- Every creature, hero, room and spell has its own file, and every value is documented in it.
- The `index.js` in each folder sets the order the game uses. To add something, add a file and list it there.
- Keys are fixed: the game logic, the waves and the assets refer to them.
- Only creatures and heroes from the original Dungeon Keeper (1997) belong in the game.
- See [`public/assets/README.md`](public/assets/README.md) for the size and orientation of the SVGs.

## Development

- **Checks:** GitHub Actions builds every pull request and runs `npm audit`.
- **AI agents:** the project rules for coding agents are in [`AGENTS.md`](AGENTS.md).
