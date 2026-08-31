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

use Monolog\LogRecord;

/**
 * No-op
 *
 * This handler handles anything, but does nothing, and does not stop bubbling to the rest of the stack.
 * This can be used for testing, or to disable a handler when overriding a configuration without
 * influencing the rest of the stack.
 *
 * @author Roel Harbers <roelharbers@gmail.com>
 */
class NoopHandler extends Handler
{
    /**
     * @inheritDoc
     */
    public function isHandling(LogRecord $record): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function handle(LogRecord $record): bool
    {
        return false;
    }
}
