<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Support;

use PHPUnit\TestRunner\TestResult\TestResult;

/**
 * @internal
 */
final class ResultReflection
{
    /**
     * The number of processed tests.
     */
    public static function numberOfTests(TestResult $testResult): int
    {
        return (fn () => $this->numberOfTests)->call($testResult);
    }
}
