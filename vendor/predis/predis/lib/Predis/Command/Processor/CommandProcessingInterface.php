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
 * Defines an object that can process commands using command processors.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface CommandProcessingInterface
{
    /**
     * Associates a command processor.
     *
     * @param CommandProcessorInterface $processor The command processor.
     */
    public function setProcessor(CommandProcessorInterface $processor);

    /**
     * Returns the associated command processor.
     *
     * @return CommandProcessorInterface
     */
    public function getProcessor();
}
