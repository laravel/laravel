<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use RectorLaravel\Set\LaravelSetList;
use Worksome\CodingStyle\WorksomeRectorConfig;

return static function (RectorConfig $rectorConfig): void {
    WorksomeRectorConfig::setup($rectorConfig);

    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/routes',
        __DIR__ . '/config',
    ]);

    // Define extra rule sets to be applied
    $rectorConfig->sets([
        SetList::PHP_84,
        SetList::PHP_85,
        SetList::CODE_QUALITY,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        SetList::DEAD_CODE,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
        LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,
        LaravelSetList::LARAVEL_TYPE_DECLARATIONS,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_TESTING,
        LaravelSetList::LARAVEL_COLLECTION,
    ]);

    // Register extra a single rules
    $rectorConfig->rule(DeclareStrictTypesRector::class);

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Carbon\\Carbon' => 'Carbon\\CarbonImmutable',
    ]);
};
