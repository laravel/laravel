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

/**
 * Base class used to implement an higher level abstraction for "virtual"
 * commands based on EVAL.
 *
 * @link http://redis.io/commands/eval
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class ScriptedCommand extends ServerEvalSHA
{
    /**
     * Gets the body of a Lua script.
     *
     * @return string
     */
    abstract public function getScript();

    /**
     * Specifies the number of arguments that should be considered as keys.
     *
     * The default behaviour for the base class is to return 0 to indicate that
     * all the elements of the arguments array should be considered as keys, but
     * subclasses can enforce a static number of keys.
     *
     * @return int
     */
    protected function getKeysCount()
    {
        return 0;
    }

    /**
     * Returns the elements from the arguments that are identified as keys.
     *
     * @return array
     */
    public function getKeys()
    {
        return array_slice($this->getArguments(), 2, $this->getKeysCount());
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (($numkeys = $this->getKeysCount()) && $numkeys < 0) {
            $numkeys = count($arguments) + $numkeys;
        }

        return array_merge(array(sha1($this->getScript()), (int) $numkeys), $arguments);
    }

    /**
     * @return array
     */
    public function getEvalArguments()
    {
        $arguments = $this->getArguments();
        $arguments[0] = $this->getScript();

        return $arguments;
    }
}
