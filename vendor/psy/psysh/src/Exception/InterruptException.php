<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Exception;

/**
 * An interrupt exception, used when Ctrl-C interrupts running code.
 *
 * Unlike BreakException, this does not exit the REPL, it only cancels
 * the current execution and returns to the prompt.
 */
class InterruptException extends \Exception implements Exception
{
    /**
     * Return a raw (unformatted) version of the error message.
     */
    public function getRawMessage(): string
    {
        return $this->getMessage();
    }
}
