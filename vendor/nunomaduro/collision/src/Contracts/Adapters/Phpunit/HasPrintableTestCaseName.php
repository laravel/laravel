<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Contracts\Adapters\Phpunit;

/**
 * @internal
 */
interface HasPrintableTestCaseName
{
    /**
     * The printable test case name.
     */
    public static function getPrintableTestCaseName(): string;

    /**
     * The printable test case method name.
     */
    public function getPrintableTestCaseMethodName(): string;

    /**
     * The "latest" printable test case method name.
     */
    public static function getLatestPrintableTestCaseMethodName(): string;
}
