<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Symfony73\Rector\Class_\GetFiltersAndFunctionsToAsTwigAttributeRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths(array_values(array_filter([
        is_dir(__DIR__.'/src') ? __DIR__.'/src' : null,
        is_dir(__DIR__.'/tests') ? __DIR__.'/tests' : null,
    ])))
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::TYPE_DECLARATION,
    ])
    ->withImportNames()
    ->withPhpVersion(PhpVersion::PHP_84)
    ->withComposerBased(symfony: true)
    ->withSkip([
        GetFiltersAndFunctionsToAsTwigAttributeRector::class,
        __DIR__.'/tests/Unit/Security/Authorization/Voter/AdminAdministratorsActionsVoterTest.php',
        __DIR__.'/tests/Unit/Security/Authorization/Voter/SwitchUserVoterTest.php',
    ]);
