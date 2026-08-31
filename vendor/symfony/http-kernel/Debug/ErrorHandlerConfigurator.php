<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Debug;

use Psr\Log\LoggerInterface;
use Symfony\Component\ErrorHandler\ErrorHandler;

/**
 * Configures the error handler.
 *
 * @final
 *
 * @internal
 */
class ErrorHandlerConfigurator
{
    private array|int|null $levels;
    private ?int $throwAt;

    /**
     * @param array|int|null $levels  An array map of E_* to LogLevel::* or an integer bit field of E_* constants
     * @param int|null       $throwAt Thrown errors in a bit field of E_* constants, or null to keep the current value
     * @param bool           $scream  Enables/disables screaming mode, where even silenced errors are logged
     * @param bool           $scope   Enables/disables scoping mode
     */
    public function __construct(
        private ?LoggerInterface $logger = null,
        array|int|null $levels = \E_ALL,
        ?int $throwAt = \E_ALL,
        private bool $scream = true,
        private bool $scope = true,
        private ?LoggerInterface $deprecationLogger = null,
    ) {
        $this->levels = $levels ?? \E_ALL;
        $this->throwAt = \is_int($throwAt) ? $throwAt : (null === $throwAt ? null : ($throwAt ? \E_ALL : null));
    }

    /**
     * Configures the error handler.
     */
    public function configure(ErrorHandler $handler): void
    {
        if ($this->logger || $this->deprecationLogger) {
            $this->setDefaultLoggers($handler);
            if (\is_array($this->levels)) {
                $levels = 0;
                foreach ($this->levels as $type => $log) {
                    $levels |= $type;
                }
            } else {
                $levels = $this->levels;
            }

            if ($this->scream) {
                $handler->screamAt($levels);
            }
            if ($this->scope) {
                $handler->scopeAt($levels & ~\E_USER_DEPRECATED & ~\E_DEPRECATED);
            } else {
                $handler->scopeAt(0, true);
            }
            $this->logger = $this->deprecationLogger = $this->levels = null;
        }
        if (null !== $this->throwAt) {
            $handler->throwAt($this->throwAt, true);
        }
    }

    private function setDefaultLoggers(ErrorHandler $handler): void
    {
        if (\is_array($this->levels)) {
            $levelsDeprecatedOnly = [];
            $levelsWithoutDeprecated = [];
            foreach ($this->levels as $type => $log) {
                if (\E_DEPRECATED == $type || \E_USER_DEPRECATED == $type) {
                    $levelsDeprecatedOnly[$type] = $log;
                } else {
                    $levelsWithoutDeprecated[$type] = $log;
                }
            }
        } else {
            $levelsDeprecatedOnly = $this->levels & (\E_DEPRECATED | \E_USER_DEPRECATED);
            $levelsWithoutDeprecated = $this->levels & ~\E_DEPRECATED & ~\E_USER_DEPRECATED;
        }

        $defaultLoggerLevels = $this->levels;
        if ($this->deprecationLogger && $levelsDeprecatedOnly) {
            $handler->setDefaultLogger($this->deprecationLogger, $levelsDeprecatedOnly);
            $defaultLoggerLevels = $levelsWithoutDeprecated;
        }

        if ($this->logger && $defaultLoggerLevels) {
            $handler->setDefaultLogger($this->logger, $defaultLoggerLevels);
        }
    }
}
