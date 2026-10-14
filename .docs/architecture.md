# Architecture

`underlord.html` contains the markup, then one `<style>` block, then one IIFE `<script>`. Sections in the script are marked with `/* ---------- name ---------- */` comments, in this order:

| Section | Key functions |
|---|---|
| constants | `W H N`, tile codes (`ROCK`…`LAVA`), room codes (`TREAS`…`LIB`), `ROOMS`, `KINDS`, `WAVES`, `SPELLS`, `ATTRACT`, `PORTAL_LIMIT` |
| small helpers | `ix tx ty inb hash ti walk transp diggable nb adjHeart cap ownPortal anyPortal` |
| pathfinding | `bfs(start, goalFn, k, passFn, maxLen)` picks randomly among the first `k` matches; `dijkstra` is for heroes and treats earth and gold as passable at a cost |
| map | `genMap()`, `newGame()` |
| vision | `los()` for tile sightlines, `vision()` reveals tiles and recruits neutrals, `rebuildFog()` |
| entities | `spawn`, `reserve/release` for task reservations, `go/step/arrive` for movement, `impThink`, `doDig`, `doClaim`, `reinforceTarget`, `doReinforce`, `creatureThink`, `gainXP`, `research`, `leave`, `clearShot`, `nearestEnemy`, `attack`, `kill`, `fight`, `march`, `tick`, `toPool`, `tryAttract`, `payday`, `launchWave` |
| update | `update(dt)` advances the whole simulation by one step; `end(win)` |
| save / load | `serialize`, `deserialize`, `initStore`, `readSave`, `saveGame`, `loadGame`, `SAVE_V` |
| drawing | `drawTile`, `drawRoom`, `drawSpecials` (Heart, portals, Gate), `drawEnt`, `makeCloud`, `drawFog`, `drawShroudTile`, `draw` |
| camera | `setWorld`, `clampCam`, `zoomAt`, `centerOn` (these sit between the drawing helpers) |
| input | `affects`, `toolValid`, `entAt`, `slapAt`, `build`, `sell`, `applyRect`, `cast`, pointer, wheel and key handlers, `updateTip` |
| interface | `CHEATS` (cheats panel), `toggleCheats`, `TOOLDEFS` (builds the toolbox buttons), `setTool`, `ui()` (stats, info box, Portal panel), `setSpeed`, `resize`, `frame` |

## State

All game state lives in one object, `G`, created by `newGame()`. Per-tile data is kept in typed arrays of length `N`, indexed `i = y*W + x`:

- `t` holds the tile type and `room` the room code.
- `mark` holds dig marks, `seen` the explored tiles and `pown` the owned portals.
- `hp` holds dig hits left, `gold` the gold left in a seam and `loot` the gold in a chest.

The rest of `G`:

- **Economy:** `money`, `rc` (room tile counts per room type, refreshed by `recount()`).
- **Heart:** `heartHP`, `heartMax`, `hx`, `hy`.
- **Map features:** `portals[]` (tile indices), `gate`, `pool` (the creature pool).
- **Timers:** `time`, `nextWave`, `payT`, `portalT`, `zapT`, `visT`.
- **Waves:** `wave`, `spawnQ` (heroes queued to spawn).
- **Units:** `ents[]` (every unit on the map), `held[]` (units in your hand), `res` (a Map of tile → number of imps reserving it), `rally`.
- **Misc:** `research`, `fx[]` (visual effects), `msgCd` (cooldowns that throttle chronicle messages), `over`, `fogDirty`.

Outside `G`, the view state is `TS` (tile size in pixels), `BASE` (tile size at which the whole map fits), `Z` (zoom, 1–4), `cam` (screen offset of the map), `SW`/`SH` (canvas size), `tool`, `drag`, `hover`, `paused`, `speed` and `running`.

## Entities

There is one object shape for every unit (see `spawn`).

- **Position and stats:** `x`, `y` are floats in tile units, with tile centres at +0.5. `side` is 0 for yours, 1 for heroes and 2 for neutrals. `hp`, `maxhp`, `atk`, `lvl`, `xp`.
- **AI:** `state`, `path[]` (tile indices), `next`/`nextT` (the state and timer to switch to on arrival), `timer`, `cd` (attack cooldown), `target`, `repath`, `scan`, `ignoreT`.
- **Needs and work:** `carry` (gold), `hunger`, `slap`, `resv` (reserved tile), `task`.
- **Flags:** `guard`, `held`, `dead`.

`tick(e, dt)` is the state machine. A unit in state `think` calls its brain (`impThink`, `creatureThink`, or `march` for heroes). The brain picks a goal with `bfs` and calls `go(e, path, nextState, timer)`. When the walk ends, `arrive` switches to `nextState`. The work states are `dig`, `claim`, `sleep`, `eat`, `train` and `study`; they check that they are still valid every frame and fall back to `think` when they are not. `fight` re-plans its path every 0.6 s. Enemy scanning interrupts most states every 0.5 s.

Units are removed by setting `dead = true`; `update()` filters them out. Held units are left out of `tick` and `draw`.

## Rendering

- The canvas fills `.stage`. Drawing happens in world units after calling `setWorld()`, which applies DPR scaling plus the `cam` offset. Only tiles inside the visible range are drawn.
- **Frame order:**
  1. background
  2. explored tiles (`drawTile` returns early for unexplored tiles)
  3. Heart, portals and Gate
  4. units, sorted by y
  5. visual effects
  6. **fog**
  7. on top of the fog, through `drawShroudTile`: gold, gems and marks
  8. rally flag, drag rectangle or hover outline, and the units in your hand at the cursor
- **Fog:** `fogC` is a 40 × 26 alpha mask (255 means unexplored). It is drawn scaled up with smoothing into `layer`, which is canvas-sized, giving soft edges. A tiling cloud texture (`cloud`, pre-rendered in `makeCloud` on every resize) is then drawn into `layer` with `source-in` composite, drifting with time and following the camera. Finally `layer` is drawn onto the main canvas.

## Input

- **Pointer events,** handled by the `ptrs` Map:
  - Two pointers make a `pinch`, which zooms and pans together.
  - The right or middle button, or the Look tool, makes a `pan`. A right-click without movement slaps instead.
  - With the hand tool or a room tool, a press starts `drag` (a tile rectangle, with `mode` set to mark, unmark, sell or a room code). On pointerup it is applied with `applyRect`. A click is simply a 1 × 1 rectangle.
  - The hand tool first tries to pick up a unit, then to drop a held unit (or dismiss it on your own portal), and only then starts a mark drag.
- **Keys:** arrows and WASD pan (they are polled in `frame`), `+`/`-` zoom, `Space`/`P` pause, `1`–`3` set the speed, `Esc` selects the hand.

## Saving

- `serialize()` produces plain JSON: the typed arrays as arrays, the scalars, and the units as `SAVE_KEYS` fields plus `held`. Paths, targets and reservations are not saved. On load every unit restarts in `think`, `guard`, `march` or `neutral`.
- `deserialize()` calls `newGame()` and then overwrites `G`.
- **Storage:** `initStore()` asks `claude.use('db')` and `claude.use('user')`. With both available, the save is written to the document `data/users/<id>/saves/slot1`, as `{json, savedAt, wave}`. `localStorage['underlord-save']` is always written as well and serves as the fallback.
- **Versioning:** `SAVE_V` (currently 2) is checked in `loadGame`.
- A save is about 15–20 KB, well under the 256 KiB document limit.

## Testing

`tools/sim.cjs` pulls the script out of `underlord.html`. It stubs `document` and the canvas with no-op proxies and makes the internals available by rewriting the startup line. It then runs `update()` directly. Because it imports nothing from the game, it breaks only if that startup line changes.
