<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Profile;

use Predis\Command\CommandInterface;

/**
 * A server profile defines features and commands supported by certain
 * versions of Redis. Instances of Predis\Client should use a server
 * profile matching the version of Redis in use.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ServerProfileInterface
{
    /**
     * Gets a profile version corresponding to a Redis version.
     *
     * @return string
     */
    public function getVersion();

    /**
     * Checks if the profile supports the specified command.
     *
     * @param  string $command Command ID.
     * @return bool
     */
    public function supportsCommand($command);

    /**
     * Checks if the profile supports the specified list of commands.
     *
     * @param  array  $commands List of command IDs.
     * @return string
     */
    public function supportsCommands(Array $commands);

    /**
     * Creates a new command instance.
     *
     * @param  string           $method    Command ID.
     * @param  array            $arguments Arguments for the command.
     * @return CommandInterface
     */
    public function createCommand($method, $arguments = array());
}
