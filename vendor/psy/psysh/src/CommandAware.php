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
 * CommandAware interface.
 *
 * This interface is used to keep completion sources and matchers up to date
 * when commands are added to the Shell.
 */
interface CommandAware
{
    /**
     * Set the available commands.
     *
     * @param Command[] $commands
     */
    public function setCommands(array $commands);
}
