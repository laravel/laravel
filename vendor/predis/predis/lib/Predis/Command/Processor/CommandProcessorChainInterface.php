<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command\Processor;

/**
 * A command processor chain processes a command using multiple chained command
 * processor before it is sent to Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface CommandProcessorChainInterface extends CommandProcessorInterface, \IteratorAggregate, \Countable
{
    /**
     * Adds a command processor.
     *
     * @param CommandProcessorInterface $processor A command processor.
     */
    public function add(CommandProcessorInterface $processor);

    /**
     * Removes a command processor from the chain.
     *
     * @param CommandProcessorInterface $processor A command processor.
     */
    public function remove(CommandProcessorInterface $processor);

    /**
     * Returns an ordered list of the command processors in the chain.
     *
     * @return array
     */
    public function getProcessors();
}
