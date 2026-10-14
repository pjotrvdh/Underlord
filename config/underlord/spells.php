<?php

declare(strict_types=1);

/*
 * Default spells. research = points needed to unlock, cost = gold per cast, params = the
 * spell's effect (Lightning: damage and radius; Heal: radius; Call to Arms: duration).
 * Create Imp's cost comes from the imp settings (baseCost, perImp). Keys are fixed.
 */
return [
    'imp' => [
        'name' => 'Create Imp',
        'research' => 0,
        'cost' => 0,
        'params' => [],
    ],
    'bolt' => [
        'name' => 'Lightning',
        'research' => 150,
        'cost' => 250,
        'params' => ['damage' => 90, 'radius' => 1.6],
    ],
    'heal' => [
        'name' => 'Heal',
        'research' => 400,
        'cost' => 200,
        'params' => ['radius' => 2.5],
    ],
    'rally' => [
        'name' => 'Call to Arms',
        'research' => 800,
        'cost' => 300,
        'params' => ['duration' => 20],
    ],
];
