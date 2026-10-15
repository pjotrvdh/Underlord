# Underlord

Underlord is a Dungeon Keeper-style browser game. The owner talks to you in **Dutch**, so reply in Dutch; code, UI text and docs are in English.

## Stack

- A static web page built with Vite. There is no server and no database; games are saved in localStorage.
- `index.html` is the page; `src/main.js` loads `src/styles/underlord.css` and `src/game/underlord.js`.
- `npm run dev` starts a dev server, `npm run build` builds into `dist/` with relative paths. The `github pages` workflow publishes `dist/` on every push to `main`.
- DDEV (default `ddev.site` TLD, no PHP or database): `ddev start` runs Vite through `.ddev/start-vite.sh` on https://underlord.ddev.site. Run npm commands as `ddev npm …` when DDEV is used.

## Game data

- All game data lives in `src/config`: one file per settings group, creature, hero, room and spell, plus `waves.js` and `assets.js`. Document every value in its file.
- The `index.js` in each folder sets the order the game uses. `src/config/index.js` combines everything into the object `applyConfig()` in the game reads.
- Keys are fixed: the game logic, the waves and the assets refer to them.
- **Only creatures and heroes from the original Dungeon Keeper (1997)** are in the game; don't introduce others.
- Assets are SVGs in `public/assets`, in a Plants vs. Zombies style (thin dark-brown outlines `#2b1b12`, muted airbrushed gradients). See `public/assets/README.md`.
- Typing `iliketocheat` unlocks the Cheats panel for the current page load only.

## Checks

- `npm run build` must succeed; CI builds every pull request and runs `npm audit`.
- After a change to the game, check it in the browser (`npm run dev` or `npm run preview`).

## Git

- Commit messages are a single short subject line, with no body and no co-author or attribution trailer.
- Pull requests are always created as **drafts**.
