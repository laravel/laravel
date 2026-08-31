<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Level;
use Psr\Log\LogLevel;
use Monolog\Logger;
use Monolog\LogRecord;

/**
 * Blackhole
 *
 * Any record it can handle will be thrown away. This can be used
 * to put on top of an existing stack to override it temporarily.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class NullHandler extends Handler
{
    private Level $level;

    /**
     * @param string|int|Level $level The minimum logging level at which this handler will be triggered
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $level
     */
    public function __construct(string|int|Level $level = Level::Debug)
    {
        $this->level = Logger::toMonologLevel($level);
    }

    /**
     * @inheritDoc
     */
    public function isHandling(LogRecord $record): bool
    {
        return $record->level->value >= $this->level->value;
    }

    /**
     * @inheritDoc
     */
    public function handle(LogRecord $record): bool
    {
        return $record->level->value >= $this->level->value;
    }
}
