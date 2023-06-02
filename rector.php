<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Worksome\CodingStyle\WorksomeRectorConfig;

return static function (RectorConfig $rectorConfig): void {
    WorksomeRectorConfig::setup($rectorConfig);

    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ]);

    // Define extra rule sets to be applied
    $rectorConfig->sets([
        // SetList::DEAD_CODE,
    ]);

    // Register extra a single rules
    // $rectorConfig->rule(ClassOnObjectRector::class);
};
