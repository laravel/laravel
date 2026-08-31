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

final class ConsoleAlarmEvent extends ConsoleEvent
{
    public function __construct(
        Command $command,
        InputInterface $input,
        OutputInterface $output,
        private int|false $exitCode = 0,
    ) {
        parent::__construct($command, $input, $output);
    }

    public function setExitCode(int $exitCode): void
    {
        if ($exitCode < 0 || $exitCode > 255) {
            throw new \InvalidArgumentException('Exit code must be between 0 and 255.');
        }

        $this->exitCode = $exitCode;
    }

    public function abortExit(): void
    {
        $this->exitCode = false;
    }

    public function getExitCode(): int|false
    {
        return $this->exitCode;
    }
}
