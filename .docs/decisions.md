# Decisions and owner requests

The owner (pjotr, Dutch-speaking) asked for these things, in this order. Keep them unless the owner says otherwise.

1. **A Dungeon Keeper-like HTML game.** It started as a Dutch single-file canvas game.
2. **Digging like DK2:** a click marks one block; dragging selects a rectangular area.
3. **All game text in English.** The title is "Underlord".
4. **Explore the map with your minions:** fog of war drawn as clouds. The player discovers the level by digging and walking around in it.
5. **Saving progress:** Save, Load, Continue and autosave. Because this is per-player state that should outlive the browser, it uses the artifact `db` capability, with `localStorage` as the fallback.
6. **No attacking through walls,** for minions and enemies alike. This is the line-of-sight check in `clearShot`, which also blocks diagonal shots past corners.
7. **Zoom and scroll, inspired by the owner's Asphalt Tycoon** (`~/Games/asphalt-tycoon`): wheel and pinch zoom at the cursor, right- or middle-drag to pan, arrows and WASD, +/− buttons, a Look tool, a floating tooltip, `Space` for pause and `1`/`2`/`3` for speed.
8. **Unexplored areas must be selectable for digging, and gold must be visible in the shroud.**
9. **Portals are always visible and follow real DK rules.** We researched them (GameFAQs DK guide, the DK fandom wiki, GOG forum):
   - portals are pre-placed, are claimed by imps, and attract nothing until claimed;
   - attraction depends on rooms and their size (the DK Warlock needs a 9-tile Library);
   - with no free Lair bed the portal stops;
   - each map has a shared finite creature pool, and creatures that die go back into it;
   - a level has a creature limit;
   - imps only come from Create Imp;
   - you dismiss a creature by dropping it on the portal.

   **Invented:** the Troll needs a 9-tile Lair and Hatchery (modelled on the DK Bile Demon's 25 + 25), and the Dark Mistress needs a 9-tile Training Room. The pool sizes (8/5/5/3) and the limit of 15 are our own numbers.
10. **Impenetrable rock only on the world border.** Two small lava pools remain inside the map.

11. **Economy tuning**, with a difficulty setting planned later. All the knobs are in `ECON`. The earlier "gold runs out" finding came mostly from the sim, which mined no gold. Once the sim mines like a player, gold sits at the storage cap. We nudged the numbers anyway (seams hold 300, 13 veins, payday every 180 s, training 1.5 gold/s) and added a total of wages due to the header.
12. **A cheats menu** with +500 gold and more.
13. **Imps reinforce walls,** as in DK: idle imps fortify earth that borders your territory. Heroes can still break through, but slowly, while dwarves (0.3 s per hit) go through much faster. You can still dig your own walls (8 hits).

14. **Separate SVG assets** in `assets/`, so the owner can restyle units and tiles and raise the resolution. Every asset keeps a procedural fallback.
15. **YAML config files** for settings like start gold and for the creature definitions. YAML needs a parser, and a CDN library would not work offline from disk, so the game has its own small subset parser. The files are embedded in the HTML (synced by a tool), so the single file still works on its own and as an artifact.

16. **Picking up and dropping creatures as in Dungeon Keeper 2** (ludo.guide DK2 Hand of Evil guide): left-click to pick up and to drop, and you can carry several. A creature dropped in a room starts working there with a +10% bonus, and a creature landing is briefly stunned. We could not confirm whether DK only lets you pick up on your own territory, so pick-up works everywhere and dropping only on your own territory.

## Our own balancing decisions

These came out of the headless simulation:

- **The Dungeon Heart defends itself** (14 damage every 0.8 s, range 2.9). Without it, a player with no creatures yet lost to wave 1.
- **The first wave comes at 300 s,** not 240.
- **Imps flee from heroes.** In an early sim all the imps died after digging into the hero camp.
- **The hero camp goes in the cavern farthest from the Heart.**
- **Imps claim nearby floor (within 2 steps) before digging,** so claiming does not stall behind large dig orders.

## Known open points

- **Difficulty setting** (planned): it should scale the `ECON` values, plus the hero waves.
- **Browser testing is limited to one visual check,** done after the SVG work: tiles, Heart, portal, chests, walls and unit sprites render correctly. Input and zoom have not been exercised in depth.
