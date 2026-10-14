<?php

declare(strict_types=1);

/*
 * Default game settings. Each value is stored in the database the first time it is needed
 * (GameDefaults) and can then be changed in the admin panel; this file stays the default.
 * Descriptions for the admin panel: lang/en/resource.php (setting.descriptions).
 * Times are in seconds, gold in gold pieces, distances in tiles.
 */
return [
    'economy' => [
        'startGold' => 1500,
        'heartCap' => 1500,
        'treasCap' => 500,
        'seamGold' => 300,
        'goldVeins' => 13,
        'payInterval' => 180,
        'wageMul' => 1,
        'trainCost' => 1.5,
        'bountyMul' => 1,
    ],
    'imp' => [
        'baseCost' => 100,
        'perImp' => 25,
        'startCount' => 4,
        'vision' => 4,
    ],
    'heart' => [
        'hp' => 2500,
        'zapDamage' => 14,
        'zapInterval' => 0.8,
        'zapRange' => 2.9,
    ],
    'portal' => [
        'firstArrival' => 6,
        'interval' => 20,
        'creatureLimit' => 15,
    ],
    'waves' => [
        'first' => 300,
        'interval' => 165,
        'heroScalePerWave' => 0.12,
    ],
    'hand' => [
        'capacity' => 8,
        'dropStun' => 1.0,
        'workBoost' => 1.1,
    ],
    'assistant' => [
        'interval' => 1.5,
        'maxMarks' => 24,
        'reserveGold' => 300,
        'maxImps' => 12,
        'impsPerClaimed' => 35,
    ],
    'walls' => [
        'hp' => 8,
    ],
];
