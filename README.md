# Underlord

A browser game in the spirit of *Dungeon Keeper*. You are an evil overlord deep underground: dig into a cloud-covered map, claim territory, build rooms, attract creatures through portals and defend your Dungeon Heart against six invasions from the surface.

The whole game is one self-contained file, [`underlord.html`](underlord.html). It has no build step, no dependencies and no server.

**Play it:** https://claude.ai/artifact/KpshEgP4ECCxr4pLyG13Rr (private artifact; saves go to your Claude account)

## Running locally

Open `underlord.html` in a browser. When it runs as a local file, saves go to the browser's `localStorage` and not to an account.

## How to play

- **Dig:** click a block to mark it for digging, or drag to mark a whole rectangle. You can mark blocks inside the clouds too. Your imps dig the blocks out, carry the gold to the Heart or a Treasury, and claim the new floor.
- **Explore:** the map is hidden in clouds. Your units reveal what they can see, and walls block their sight. Gold seams and the two portals shine through the clouds. Hidden caverns hold treasure chests, wandering creatures that join you, and a camp of sleeping heroes.
- **Build:** you build rooms on claimed ground by clicking a tile or dragging a rectangle. The rooms are Treasury, Lair, Hatchery, Training Room and Library.
- **Creatures:** creatures only arrive through a portal you have claimed. Each one needs a free bed in your Lair. Stronger creatures also need rooms of 9 or more tiles. Each map has a limited pool of creatures, and they want their wages on payday.
- **Hand:** pick creatures up and drop them where they are needed. If you drop one on your own portal, you dismiss it. Right-click slaps a creature, and slapped imps work faster.
- **Spells:** Create Imp, Lightning, Heal and Call to Arms. You unlock the last three through research in the Library.
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

## Development

```bash
node tools/sim.cjs 700
```

This runs the real game logic headless for 700 simulated seconds. It plays a scripted opening, prints status lines and checks that a save/load round trip works. See [`AGENTS.md`](AGENTS.md) for how to work on the project, and [`.docs/`](.docs) for the game design and the code architecture.
