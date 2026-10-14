<?php

declare(strict_types=1);

/*
 * Default rooms, in toolbox order. The keys are fixed (the game logic depends on them);
 * name, cost (gold per tile), color and tip can be changed in the admin panel.
 */
return [
    'treasury' => [
        'name' => 'Treasury',
        'cost' => 50,
        'color' => '#e3b23c',
        'tip' => 'Stores extra gold per tile',
    ],
    'lair' => [
        'name' => 'Lair',
        'cost' => 75,
        'color' => '#9b6bf0',
        'tip' => 'One bed per creature',
    ],
    'hatchery' => [
        'name' => 'Hatchery',
        'cost' => 100,
        'color' => '#d8b04a',
        'tip' => 'Chickens for hungry creatures',
    ],
    'training' => [
        'name' => 'Training Room',
        'cost' => 125,
        'color' => '#e0483c',
        'tip' => 'Creatures gain levels here (costs gold)',
    ],
    'library' => [
        'name' => 'Library',
        'cost' => 125,
        'color' => '#5f9be0',
        'tip' => 'Research unlocks spells',
    ],
];
