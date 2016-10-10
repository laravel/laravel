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

use Predis\Command\CommandInterface;
use Predis\Command\PrefixableCommandInterface;

/**
 * Command processor that is used to prefix the keys contained in the arguments
 * of a Redis command.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyPrefixProcessor implements CommandProcessorInterface
{
    private $prefix;

    /**
     * @param string $prefix Prefix for the keys.
     */
    public function __construct($prefix)
    {
        $this->setPrefix($prefix);
    }

    /**
     * Sets a prefix that is applied to all the keys.
     *
     * @param string $prefix Prefix for the keys.
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Gets the current prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CommandInterface $command)
    {
        if ($command instanceof PrefixableCommandInterface && $command->getArguments()) {
            $command->prefixKeys($this->prefix);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getPrefix();
    }
}
