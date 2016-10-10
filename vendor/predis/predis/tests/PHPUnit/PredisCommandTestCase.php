<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command;

use PredisTestCase;
use Predis\Client;

/**
 *
 */
abstract class PredisCommandTestCase extends PredisTestCase
{
    /**
     * Returns the expected command.
     *
     * @return CommandInterface|string Instance or FQN of the expected command.
     */
    abstract protected function getExpectedCommand();

    /**
     * Returns the expected command ID.
     *
     * @return string
     */
    abstract protected function getExpectedId();

    /**
     * Returns a new command instance.
     *
     * @return CommandInterface
     */
    public function getCommand()
    {
        $command = $this->getExpectedCommand();

        return $command instanceof CommandInterface ? $command : new $command();
    }

    /**
     * Returns a new client instance.
     *
     * @param  bool   $flushdb Flush selected database before returning the client.
     * @return Client
     */
    public function getClient($flushdb = true)
    {
        $profile = $this->getProfile();

        if (!$profile->supportsCommand($id = $this->getExpectedId())) {
            $this->markTestSkipped(
                "The profile {$profile->getVersion()} does not support command {$id}"
            );
        }

        $client = $this->createClient(null, null, $flushdb);

        return $client;
    }

    /**
     * Returns wether the command is prefixable or not.
     *
     * @return bool
     */
    protected function isPrefixable()
    {
        return $this->getCommand() instanceof PrefixableCommandInterface;
    }

    /**
     * Returns a new command instance with the specified arguments.
     *
     * @param ... List of arguments for the command.
     * @return CommandInterface
     */
    protected function getCommandWithArguments(/* arguments */)
    {
        return $this->getCommandWithArgumentsArray(func_get_args());
    }

    /**
     * Returns a new command instance with the specified arguments.
     *
     * @param  array            $arguments Arguments for the command.
     * @return CommandInterface
     */
    protected function getCommandWithArgumentsArray(Array $arguments)
    {
        $command = $this->getCommand();
        $command->setArguments($arguments);

        return $command;
    }

    /**
     * @group disconnected
     */
    public function testCommandId()
    {
        $command = $this->getCommand();

        $this->assertInstanceOf('Predis\Command\CommandInterface', $command);
        $this->assertEquals($this->getExpectedId(), $command->getId());
    }

    /**
     * @group disconnected
     */
    public function testRawArguments()
    {
        $expected = array('1st', '2nd', '3rd', '4th');

        $command = $this->getCommand();
        $command->setRawArguments($expected);

        $this->assertSame($expected, $command->getArguments());
    }
}
