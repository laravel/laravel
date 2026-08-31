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

use Psy\Command\Command;

/**
 * Trait for building a command lookup map (name + aliases → Command).
 *
 * Used by completion sources and refiners that need to resolve a command
 * name or alias to its Command instance.
 */
trait CommandMapTrait
{
    /** @var array<string, Command> */
    private array $commandMap = [];

    /**
     * Set the available commands.
     *
     * @param Command[] $commands
     */
    public function setCommands(array $commands): void
    {
        $this->commandMap = [];

        foreach ($commands as $command) {
            $this->commandMap[$command->getName()] = $command;

            foreach ($command->getAliases() as $alias) {
                $this->commandMap[$alias] = $command;
            }
        }
    }
}
