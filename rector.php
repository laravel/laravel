<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use Worksome\CodingStyle\WorksomeRectorConfig;

return static function (RectorConfig $rectorConfig): void {
    WorksomeRectorConfig::setup($rectorConfig);

    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/routes',
    ]);

    // Define extra rule sets to be applied
    $rectorConfig->sets([
        SetList::PHP_84,
        SetList::CODE_QUALITY,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        SetList::DEAD_CODE,
    ]);

    // Register extra a single rules
    $rectorConfig->rule(DeclareStrictTypesRector::class);

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Carbon\\Carbon' => 'Carbon\\CarbonImmutable',
    ]);
};
