<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Command;

use const PHP_EOL;
use function printf;
use PHPUnit\TextUI\Configuration\CodeCoverageFilterRegistry;
use PHPUnit\TextUI\Configuration\Configuration;
use PHPUnit\TextUI\Configuration\NoCoverageCacheDirectoryException;
use SebastianBergmann\CodeCoverage\StaticAnalysis\CacheWarmer;
use SebastianBergmann\Timer\NoActiveTimerException;
use SebastianBergmann\Timer\Timer;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @codeCoverageIgnore
 */
final readonly class WarmCodeCoverageCacheCommand implements Command
{
    private Configuration $configuration;
    private CodeCoverageFilterRegistry $codeCoverageFilterRegistry;

    public function __construct(Configuration $configuration, CodeCoverageFilterRegistry $codeCoverageFilterRegistry)
    {
        $this->configuration              = $configuration;
        $this->codeCoverageFilterRegistry = $codeCoverageFilterRegistry;
    }

    /**
     * @throws NoActiveTimerException
     * @throws NoCoverageCacheDirectoryException
     */
    public function execute(): Result
    {
        if (!$this->configuration->hasCoverageCacheDirectory()) {
            return Result::from(
                'Cache for static analysis has not been configured' . PHP_EOL,
                Result::FAILURE,
            );
        }

        $this->codeCoverageFilterRegistry->init($this->configuration, true);

        if (!$this->codeCoverageFilterRegistry->configured()) {
            return Result::from(
                'Filter for code coverage has not been configured' . PHP_EOL,
                Result::FAILURE,
            );
        }

        $timer = new Timer;
        $timer->start();

        print 'Warming cache for static analysis ... ';

        (new CacheWarmer)->warmCache(
            $this->configuration->coverageCacheDirectory(),
            !$this->configuration->disableCodeCoverageIgnore(),
            $this->configuration->ignoreDeprecatedCodeUnitsFromCodeCoverage(),
            $this->codeCoverageFilterRegistry->get(),
        );

        printf(
            '[%s]%s',
            $timer->stop()->asString(),
            PHP_EOL,
        );

        return Result::from();
    }
}
