<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Driver;

use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
use SebastianBergmann\Environment\Runtime;

final class Selector
{
    /**
     * @throws NoCodeCoverageDriverAvailableException
     * @throws PcovNotAvailableException
     * @throws XdebugNotAvailableException
     * @throws XdebugNotEnabledException
     */
    public function forLineCoverage(Filter $filter): Driver
    {
        $runtime = new Runtime;

        if ($runtime->hasPCOV()) {
            return new PcovDriver($filter);
        }

        if ($runtime->hasXdebug()) {
            $driver = new XdebugDriver($filter);

            $driver->enableDeadCodeDetection();

            return $driver;
        }

        throw new NoCodeCoverageDriverAvailableException;
    }

    /**
     * @throws NoCodeCoverageDriverWithPathCoverageSupportAvailableException
     * @throws XdebugNotAvailableException
     * @throws XdebugNotEnabledException
     */
    public function forLineAndPathCoverage(Filter $filter): Driver
    {
        if ((new Runtime)->hasXdebug()) {
            $driver = new XdebugDriver($filter);

            $driver->enableDeadCodeDetection();
            $driver->enableBranchAndPathCoverage();

            return $driver;
        }

        throw new NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
    }
}
