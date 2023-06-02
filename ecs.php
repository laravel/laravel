<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Worksome\CodingStyle\WorksomeEcsConfig;


return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/tests',
        __DIR__ . '/config',
    ]);

    WorksomeEcsConfig::setup($ecsConfig);
};
