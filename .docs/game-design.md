# Game design

Most numbers below come from `config/*.yml`, and the rest from `underlord.html`. When you change one, change it here too.

## World

- The map is a 40 × 26 tile grid (`W`, `H`). It is generated randomly each new game in `genMap()`.
- **Tile types:**

  | Code | Type | Behaviour |
  |---|---|---|
  | 0 | ROCK | Impenetrable. Only on the map border. |
  | 1 | EARTH | Takes 3 hits to dig. |
  | 2 | GOLD | Holds `ECON.seamGold` (300) gold, and each hit yields 20. Heroes need 5 hits to dig through. |
  | 3 | GEM | Yields 20 gold per hit forever and is never removed. |
  | 4 | FLOOR | Unclaimed. |
  | 5 | CLAIM | Your territory. Rooms can be built here. |
  | 6 | HEART | 3 × 3. Not walkable. |
  | 7 | PORTAL | Walkable. |
  | 8 | GATE | The heroes' entry point. |
  | 9 | LAVA | Not walkable, but you can see across it. |
| 10 | WALL | A reinforced wall. Imps build it from earth next to your territory. Takes 8 hits to dig, and heroes path through it at cost 12. |

- **Generation:**
  - Earth everywhere, with a border of rock.
  - `ECON.goldVeins` (13) gold veins, 2 small lava pools and 2 gems.
  - 5 hidden caverns.
  - The Heart sits at (5, H/2) inside a 7 × 7 claimed square, with a guaranteed gold vein 5 tiles to its right.
  - **Two portals**, each inside a 3 × 3 floor. One sits at x 14–18, about 7 rows above or below the Heart. The other sits at x 24–29 on the opposite side.
  - The **Hero Gate** is at x = W−4, with a 5 × 5 floor around it.
  - A map is only accepted when the heroes can reach the Heart (`dijkstra`), with up to 40 tries.
- **Caverns** are sorted by distance from the Heart, farthest first:
  1. Hero camp: a guarding Knight and Archer, plus a 600-gold chest.
  2. and 3. A neutral Goblin or Troll (side 2) plus a 200-gold chest. A neutral creature joins you when one of your units is within 3 tiles and its tile is explored.
  3. The rest: chests of 250 and 150 gold.
- Any unit of yours picks up a chest by walking onto it, as long as there is storage space for the gold.

## Fog of war ("shroud")

- `G.seen[i]`: once a tile is seen, it stays revealed.
- The 4.3-tile area around the Heart and the 3 × 3 area around each portal start revealed.
- Each of your units reveals tiles within radius 4 (imps) or 5 (creatures), with line of sight. Solid tiles block sight; floor, claimed ground, portal, gate, Heart and lava do not. A wall tile that is hit by a sightline is itself revealed. Vision runs every 0.25 s.
- What shows through the shroud:
  - gold and gem seams, dimmed and glinting;
  - your dig marks;
  - the portals, because they start revealed.
- Heroes and neutral creatures are only drawn on explored tiles.
- You can mark tiles for digging inside the shroud. The drag preview highlights every non-rock tile, so it does not give away where caverns are, but only real earth, gold and gem tiles get marked.

## Economy

All economy numbers live in the `ECON` object, so the planned difficulty setting can scale them: `startGold`, `heartCap`, `treasCap`, `seamGold`, `goldVeins`, `payInterval`, `wageMul`, `trainCost` and `bountyMul`.


- **Storage:** the Heart holds 1500 gold, and each Treasury tile adds 500. Any gold above that capacity is refused: imps keep carrying it, and selling rooms can make you lose some.
- You start with 1500 gold.
- **Room prices** (per tile; selling returns half):

  | Room | Cost | Effect |
  |---|---|---|
  | Treasury | 50 | +500 storage |
  | Lair | 75 | one bed per tile |
  | Hatchery | 100 | creatures eat here |
  | Training Room | 125 | XP; costs 1.5 gold/s per trainee |
  | Library | 125 | research |

- **Payday** comes every 180 s and pays each creature its wage (`wage(kind)` = base wage × `wageMul`). The header shows the countdown and the total due, in red when you can't pay it. A creature you cannot pay leaves through a portal.
- **Bounties:** killing heroes pays gold (see the table below).

## Imps

- You start with 4 imps. Create Imp costs 100 + 25 × the current number of imps.
- **Priorities**, in `impThink`:
  1. Deposit gold once carrying 100 or more, if there is room.
  2. Claim floor within 2 steps.
  3. Dig the nearest marked tile; at most 2 imps work one tile.
  4. Deposit any gold that is left.
  5. Claim any floor next to claimed ground.
  6. Fetch a nearby treasure chest.
  7. **Reinforce** an earth wall that borders claimed ground: stand on claimed ground next to it and hit 5 × 0.5 s, and it becomes WALL. Marked tiles are skipped.
  8. Wander.
- One dig hit takes 0.38 s and claiming a tile takes 1.2 s. A slapped imp works 1.8× faster and moves 1.7× faster for 10 s, and loses 4 HP.
- Imps flee to the Heart when a hero is within 3.2 tiles with line of sight.
- A portal is claimed like floor: it needs claimed ground next to it.

## Creatures (side 0)

| Kind | HP | Attack | Speed | Range | Wage | Attracted by |
|---|---|---|---|---|---|---|
| Goblin | 90 | 9 | 2.4 | melee | 40 | a free bed |
| Warlock | 70 | 13 | 2.0 | 4 | 70 | Library ≥ 9 tiles |
| Troll | 170 | 13 | 1.7 | melee | 60 | Lair ≥ 9 and Hatchery ≥ 9 tiles |
| Dark Mistress | 140 | 18 | 2.7 | melee | 110 | Training Room ≥ 9 tiles |

- **Portal rules**, after Dungeon Keeper:
  - Only portals you have claimed attract creatures.
  - One attempt every 20 s per dungeon (the first comes 6 s after your first claim). A second portal adds a spawn point, not more speed.
  - Every creature needs a free Lair bed, otherwise the portal stays quiet.
  - The dungeon limit is 15 creatures.
  - Each map has a finite **creature pool**: goblin 8, warlock 5, troll 5, mistress 3. Creatures that die, leave or are dismissed go back into the pool.
  - Imps never come through a portal.
  - Dropping a creature on your own portal dismisses it.
- **Needs:**
  - Hunger rises 1.5 per second. At 100 or more a creature goes to eat (2.5 s, heals 15%). At 200 it leaves, starving.
  - Below 50% HP it sleeps in the Lair and heals 6% per second.
- **Idle choices**, in `creatureThink`: answer a rally, then sleep, then eat, then (warlocks) study, then train (60%), then explore the edge of the shroud (35%), then study (50%), then loiter in the Lair.
- **Levels:** reaching the next level takes 80 × the current level in XP, up to level 10. Each level gives +12% max HP and +10% attack.
  - Training gives 9 XP/s (trolls 11).
  - Damage dealt gives 0.4 XP per point.
- **Research:** warlocks add 4 points per second, other creatures 1.2.

## Heroes (side 1)

| Kind | HP | Attack | Range | Dig hit (s) | Bounty |
|---|---|---|---|---|---|
| Dwarf | 80 | 8 | melee | 0.3 | 60 |
| Archer | 60 | 9 | 4.5 | 0.9 | 70 |
| Wizard | 70 | 15 | 4 | 0.9 | 100 |
| Knight | 230 | 18 | melee | 0.8 | 200 |
| Lord of the Land | 1000 | 32 | melee | 0.6 | 1000 |

- **Waves:** the first comes at 300 s, then one every 165 s, six in total. Hero stats grow by 12% per wave.
  1. 2 dwarves, 1 archer
  2. 3 dwarves, 2 archers, 1 wizard
  3. 2 dwarves, 2 archers, 2 wizards, 1 knight
  4. 3 dwarves, 3 archers, 2 wizards, 2 knights
  5. 4 dwarves, 3 archers, 3 wizards, 3 knights
  6. the Lord, 2 knights, 3 wizards, 3 archers, 2 dwarves
- Heroes spawn at the Gate 1.2 s apart.
- **March:** heroes take the cheapest route to the Heart. Dijkstra costs 1 for floor, 4 for earth and 6 for gold, so they tunnel through earth.
- Next to the Heart they attack it. They fight your units within 5 tiles when they have line of sight.
- Guards from the hero camp stay where they are until your units come close.
- **Heart:** 2500 HP. It shoots the nearest hero within 2.9 tiles for 14 damage every 0.8 s.

## Combat

- Units only attack and acquire targets with line of sight (`clearShot`). Walls block it, including a diagonal shot past a corner when both tiles beside the corner are solid. A unit without line of sight walks closer.
- Damage per hit is attack × 0.85–1.15.
- Creatures acquire enemies within 8 tiles, heroes within 5. A unit stops chasing at 13 tiles, or when the path is longer than 22 steps (then it ignores that target for 3 s).

## Spells

| Spell | Research | Cost | Effect |
|---|---|---|---|
| Create Imp | — | 100 + 25 × imps | a new imp on claimed ground |
| Lightning | 150 | 250 | 90 damage to heroes within 1.6 tiles; the spot must be explored |
| Heal | 400 | 200 | full heal for your units within 2.5 tiles |
| Call to Arms | 800 | 300 | all creatures gather at the spot for 20 s |

## Cheats

The **Cheats** button in the header (shortcut: the backtick key or `Ctrl+C`) opens a panel with:

- +500 or +5000 gold (this may go over storage capacity)
- Reveal map
- Finish research
- Restore Heart
- Free imp
- Summon a creature of each type (this skips the pool)
- Level up all
- Next wave now
- Defeat heroes

Every cheat is logged in the chronicle.

## Win and lose

- **Win:** all six waves have spawned and no marching heroes are left. Camp guards don't count.
- **Lose:** the Heart reaches 0 HP.

## Saving

- The game saves with the Save button and autosaves every 120 s of play. **Continue** and **Load** read the save back.
- The save goes to the `db` capability when it is available, and otherwise to `localStorage`. See the architecture doc.
