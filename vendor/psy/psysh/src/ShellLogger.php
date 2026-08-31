<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

use Psy\Logger\CallbackLogger;

/**
 * Shell logger.
 *
 * Wraps a logger and provides typed methods for logging PsySH events.
 */
class ShellLogger
{
    private $logger;
    private array $levels;

    /**
     * @param \Psr\Log\LoggerInterface|CallbackLogger $logger Logger instance
     * @param array                                   $levels Log levels for each event type (input, command, execute)
     */
    public function __construct($logger, array $levels)
    {
        $this->logger = $logger;
        $this->levels = $levels;
    }

    /**
     * Log user input.
     *
     * @param string $input User code input
     */
    public function logInput(string $input): void
    {
        if (!$this->isInputDisabled()) {
            $this->logger->log($this->levels['input'], 'PsySH input', [
                'input' => $input,
            ]);
        }
    }

    /**
     * Log a PsySH command.
     *
     * @param string $command Command string (e.g., "ls", "doc array_map")
     */
    public function logCommand(string $command): void
    {
        if (!$this->isCommandDisabled()) {
            $this->logger->log($this->levels['command'], 'PsySH command', [
                'command' => $command,
            ]);
        }
    }

    /**
     * Log code about to be executed.
     *
     * @param string $code Cleaned PHP code
     */
    public function logExecute(string $code): void
    {
        if (!$this->isExecuteDisabled()) {
            $this->logger->log($this->levels['execute'], 'PsySH execute', [
                'code' => $code,
            ]);
        }
    }

    /**
     * Check if input logging is disabled.
     */
    public function isInputDisabled(): bool
    {
        return $this->levels['input'] === false;
    }

    /**
     * Check if command logging is disabled.
     */
    public function isCommandDisabled(): bool
    {
        return $this->levels['command'] === false;
    }

    /**
     * Check if execute logging is disabled.
     */
    public function isExecuteDisabled(): bool
    {
        return $this->levels['execute'] === false;
    }
}
