<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util\PHP;

use PHPUnit\Event\Facade;
use PHPUnit\Framework\ChildProcessResultProcessor;
use PHPUnit\Framework\Test;
use PHPUnit\Runner\CodeCoverage;
use PHPUnit\TestRunner\TestResult\PassedTests;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class JobRunnerRegistry
{
    private static ?JobRunner $runner = null;

    public static function run(Job $job): Result
    {
        return self::runner()->run($job);
    }

    /**
     * @param non-empty-string $processResultFile
     */
    public static function runTestJob(Job $job, string $processResultFile, Test $test): void
    {
        self::runner()->runTestJob($job, $processResultFile, $test);
    }

    public static function set(JobRunner $runner): void
    {
        self::$runner = $runner;
    }

    private static function runner(): JobRunner
    {
        if (self::$runner === null) {
            self::$runner = new JobRunner(
                new ChildProcessResultProcessor(
                    Facade::instance(),
                    Facade::emitter(),
                    PassedTests::instance(),
                    CodeCoverage::instance(),
                ),
            );
        }

        return self::$runner;
    }
}
