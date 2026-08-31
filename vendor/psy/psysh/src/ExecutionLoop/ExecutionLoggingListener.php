<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\ExecutionLoop;

use Psy\Shell;
use Psy\ShellLogger;

/**
 * Execution logging listener.
 *
 * Logs code about to be executed to a ShellLogger.
 */
class ExecutionLoggingListener extends AbstractListener
{
    private ShellLogger $logger;

    /**
     * @param ShellLogger $logger
     */
    public function __construct(ShellLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function isSupported(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onExecute(Shell $shell, string $code)
    {
        $this->logger->logExecute($code);

        return null;
    }
}
