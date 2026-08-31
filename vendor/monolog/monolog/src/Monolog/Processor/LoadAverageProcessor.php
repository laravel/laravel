<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

use Monolog\LogRecord;

/**
 * Injects sys_getloadavg in all records @see https://www.php.net/manual/en/function.sys-getloadavg.php
 *
 * @author Johan Vlaar <johan.vlaar.1994@gmail.com>
 */
class LoadAverageProcessor implements ProcessorInterface
{
    public const LOAD_1_MINUTE = 0;
    public const LOAD_5_MINUTE = 1;
    public const LOAD_15_MINUTE = 2;

    private const AVAILABLE_LOAD = [
        self::LOAD_1_MINUTE,
        self::LOAD_5_MINUTE,
        self::LOAD_15_MINUTE,
    ];

    /**
     * @var int
     */
    protected $avgSystemLoad;

    /**
     * @param self::LOAD_* $avgSystemLoad
     */
    public function __construct(int $avgSystemLoad = self::LOAD_1_MINUTE)
    {
        if (!\in_array($avgSystemLoad, self::AVAILABLE_LOAD, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid average system load: `%s`', $avgSystemLoad));
        }
        $this->avgSystemLoad = $avgSystemLoad;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        if (!\function_exists('sys_getloadavg')) {
            return $record;
        }
        $usage = sys_getloadavg();
        if (false === $usage) {
            return $record;
        }

        $record->extra['load_average'] = $usage[$this->avgSystemLoad];

        return $record;
    }
}
