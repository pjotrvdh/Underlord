# Assets

All graphics are SVG files. You can restyle the game by editing them in any vector editor (Inkscape, Figma, Illustrator) or by hand. The game rasterises each SVG at the exact on-screen size, so the graphics stay sharp at every zoom level.

[`config/assets.yml`](../config/assets.yml) decides which file is used for what. Point an entry at a different file to swap it. Remove an entry to fall back to the built-in procedural drawing.

## Conventions

| Folder | viewBox | Notes |
|---|---|---|
| `units/` | `0 0 64 64` | Facing **right**; the game mirrors them when a unit walks left. Feet at about y = 58, body centred on x = 32. Drawn at roughly 2.9 × the unit's radius: imps are smaller and the Lord is bigger. |
| `tiles/` | `0 0 64 64` | They must tile seamlessly. Earth, rock, floor, gold and gem are mirrored at random for variety, so avoid content that only reads one way. Shadows from neighbouring walls are added by the game. |
| `props/` | `0 0 64 64`, except `heart.svg` (`0 0 192 192`, covering the 3 × 3 Heart) | Drawn on top of tiles. Portals rotate around their centre. The treasury pile is scaled by how full your treasuries are. The chicken faces right. |

- Keep a dark outline (`#120b08`, about 2.5 px at the 64 grid), so sprites read well against the floor.
- Use only plain SVG, with no external images, fonts or scripts. Browsers block those when they draw an SVG to a canvas.
- Lava, the clouds, the Heart's pulse, health bars and all effects are still drawn in code.

## Files

- `units/`: imp, goblin, warlock, troll, mistress, dwarf, archer, wizard, knight, lord
- `tiles/`: earth, gold, gem, rock, floor, claimed, wall
- `props/`: heart, portal, portal-unclaimed, gate, chest, treasury-pile, lair-nest, hatchery-chicken, training-dummy, library-shelf
