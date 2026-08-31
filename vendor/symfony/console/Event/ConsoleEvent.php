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
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Allows to inspect input and output of a command.
 *
 * @author Francesco Levorato <git@flevour.net>
 */
class ConsoleEvent extends Event
{
    public function __construct(
        protected ?Command $command,
        private InputInterface $input,
        private OutputInterface $output,
    ) {
    }

    /**
     * Gets the command that is executed.
     */
    public function getCommand(): ?Command
    {
        return $this->command;
    }

    /**
     * Gets the input instance.
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * Gets the output instance.
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }
}
