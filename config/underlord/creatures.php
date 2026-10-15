<?php

declare(strict_types=1);

/*
 * Default creatures and heroes, in the order the game and the admin panel list them.
 * Only creatures and heroes from the original Dungeon Keeper (1997) belong here.
 *   side     creature (comes through your portal) or hero (invades)
 *   speed    tiles per second; range in tiles (1 = melee); rate = seconds between attacks
 *   wage     gold per payday; vision in tiles; pool = how many can come through portals per map
 *   attract  minimum room sizes before it comes (every creature also needs a free Lair bed)
 *   bounty   gold for killing it; dig = seconds per hit when tunnelling; big = drawn larger
 * Keys are fixed: waves and config/underlord/assets.php refer to them.
 */
return [
    'imp' => [
        'side' => 'creature',
        'name' => 'Imp',
        'hp' => 40,
        'attack' => 3,
        'speed' => 3.0,
        'range' => 1,
        'rate' => 1,
        'wage' => 0,
        'color' => '#c8552f',
    ],
    // In Dungeon Keeper 1 the Orc needs a Barracks, which this game does not have (yet), so it only
    // needs a free bed, like the basic fighter it replaces.
    'orc' => [
        'side' => 'creature',
        'name' => 'Orc',
        'hp' => 140,
        'attack' => 13,
        'speed' => 2.3,
        'range' => 1,
        'rate' => 0.9,
        'wage' => 55,
        'vision' => 5,
        'pool' => 8,
        'attract' => [],
        'color' => '#b0457a',
    ],
    'warlock' => [
        'side' => 'creature',
        'name' => 'Warlock',
        'hp' => 70,
        'attack' => 13,
        'speed' => 2.0,
        'range' => 4,
        'rate' => 1.3,
        'wage' => 70,
        'vision' => 5,
        'pool' => 5,
        'attract' => ['library' => 9],
        'color' => '#7c4dd8',
    ],
    'troll' => [
        'side' => 'creature',
        'name' => 'Troll',
        'hp' => 110,
        'attack' => 9,
        'speed' => 1.7,
        'range' => 1,
        'rate' => 1.1,
        'wage' => 30,
        'vision' => 5,
        'pool' => 5,
        'attract' => ['lair' => 9, 'hatchery' => 9],
        'color' => '#8a7350',
    ],
    'mistress' => [
        'side' => 'creature',
        'name' => 'Dark Mistress',
        'hp' => 140,
        'attack' => 18,
        'speed' => 2.7,
        'range' => 1,
        'rate' => 0.8,
        'wage' => 105,
        'vision' => 5,
        'pool' => 3,
        'attract' => ['training' => 9],
        'color' => '#c43d7e',
    ],
    // The Dungeon Keeper 1 Tunneller: the dwarf hero that digs its way to your dungeon.
    'tunneller' => [
        'side' => 'hero',
        'name' => 'Tunneller',
        'hp' => 80,
        'attack' => 8,
        'speed' => 2.0,
        'range' => 1,
        'rate' => 1,
        'bounty' => 60,
        'dig' => 0.3,
        'color' => '#b9824a',
    ],
    'archer' => [
        'side' => 'hero',
        'name' => 'Archer',
        'hp' => 60,
        'attack' => 9,
        'speed' => 2.2,
        'range' => 4.5,
        'rate' => 1.2,
        'bounty' => 70,
        'dig' => 0.9,
        'color' => '#3f8f6b',
    ],
    'wizard' => [
        'side' => 'hero',
        'name' => 'Wizard',
        'hp' => 70,
        'attack' => 15,
        'speed' => 1.9,
        'range' => 4,
        'rate' => 1.5,
        'bounty' => 100,
        'dig' => 0.9,
        'color' => '#3a6fd8',
    ],
    'knight' => [
        'side' => 'hero',
        'name' => 'Knight',
        'hp' => 230,
        'attack' => 18,
        'speed' => 2.0,
        'range' => 1,
        'rate' => 1.1,
        'bounty' => 200,
        'dig' => 0.8,
        'color' => '#9aa6b8',
    ],
    'lord' => [
        'side' => 'hero',
        'name' => 'Lord of the Land',
        'hp' => 1000,
        'attack' => 32,
        'speed' => 1.8,
        'range' => 1,
        'rate' => 1.1,
        'bounty' => 1000,
        'dig' => 0.6,
        'big' => true,
        'color' => '#e2c46a',
    ],
];
