<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Allows to handle throwables thrown while running a command.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
final class ConsoleErrorEvent extends ConsoleEvent
{
    private int $exitCode;

    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        private \Throwable $error,
        ?Command $command = null,
    ) {
        parent::__construct($command, $input, $output);
    }

    public function getError(): \Throwable
    {
        return $this->error;
    }

    public function setError(\Throwable $error): void
    {
        $this->error = $error;
    }

    public function setExitCode(int $exitCode): void
    {
        $this->exitCode = $exitCode;

        $r = new \ReflectionProperty($this->error, 'code');
        $r->setValue($this->error, $this->exitCode);
    }

    public function getExitCode(): int
    {
        return $this->exitCode ?? (\is_int($this->error->getCode()) && 0 !== $this->error->getCode() ? $this->error->getCode() : 1);
    }
}
