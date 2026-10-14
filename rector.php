<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Rector\Class_\AddHasFactoryToModelsRector;
use RectorLaravel\Set\LaravelSetList;

// Same setup as the SWIS project wherermeorders.
return RectorConfig::configure()
    ->withComposerBased(laravel: true)
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/bootstrap',
        __DIR__.'/config',
        __DIR__.'/database/migrations',
        __DIR__.'/lang',
        __DIR__.'/public',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withSkip([
        __DIR__.'/bootstrap/cache/*',
        // Game data comes from config (GameDefaults) and saves from the game; neither uses factories.
        AddHasFactoryToModelsRector::class => [
            __DIR__.'/app/Models/Creature.php',
            __DIR__.'/app/Models/Room.php',
            __DIR__.'/app/Models/Save.php',
            __DIR__.'/app/Models/Setting.php',
            __DIR__.'/app/Models/Spell.php',
            __DIR__.'/app/Models/Wave.php',
            __DIR__.'/tests',
        ],
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
    )
    ->withSets([
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_TESTING,
        LaravelSetList::LARAVEL_TYPE_DECLARATIONS,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
        LaravelSetList::LARAVEL_IF_HELPERS,
        LaravelSetList::LARAVEL_FACTORIES,
    ])
    ->withImportNames();
