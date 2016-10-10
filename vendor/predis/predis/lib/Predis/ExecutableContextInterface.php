<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis;

/**
 * Defines the interface of a basic client object or abstraction that
 * can send commands to Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ExecutableContextInterface
{
    /**
     * Starts the execution of the context.
     *
     * @param  mixed $callable Optional callback for execution.
     * @return array
     */
    public function execute($callable = null);
}
