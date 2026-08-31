<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI;

use function mt_srand;
use PHPUnit\Event;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\ResultCache\ResultCache;
use PHPUnit\Runner\TestSuiteSorter;
use PHPUnit\TextUI\Configuration\Configuration;
use Throwable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestRunner
{
    /**
     * @throws RuntimeException
     */
    public function run(Configuration $configuration, ResultCache $resultCache, TestSuite $suite): void
    {
        try {
            Event\Facade::emitter()->testRunnerStarted();

            if ($configuration->executionOrder() === TestSuiteSorter::ORDER_RANDOMIZED) {
                mt_srand($configuration->randomOrderSeed());
            }

            if ($configuration->executionOrder() !== TestSuiteSorter::ORDER_DEFAULT ||
                $configuration->executionOrderDefects() !== TestSuiteSorter::ORDER_DEFAULT ||
                $configuration->resolveDependencies()) {
                $resultCache->load();

                (new TestSuiteSorter($resultCache))->reorderTestsInSuite(
                    $suite,
                    $configuration->executionOrder(),
                    $configuration->resolveDependencies(),
                    $configuration->executionOrderDefects(),
                );

                Event\Facade::emitter()->testSuiteSorted(
                    $configuration->executionOrder(),
                    $configuration->executionOrderDefects(),
                    $configuration->resolveDependencies(),
                );
            }

            (new TestSuiteFilterProcessor)->process($configuration, $suite);

            Event\Facade::emitter()->testRunnerExecutionStarted(
                Event\TestSuite\TestSuiteBuilder::from($suite),
            );

            $suite->run();

            Event\Facade::emitter()->testRunnerExecutionFinished();
            Event\Facade::emitter()->testRunnerFinished();
        } catch (Throwable $t) {
            throw new RuntimeException(
                $t->getMessage(),
                (int) $t->getCode(),
                $t,
            );
        }
    }
}
