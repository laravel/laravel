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
 * Defines a command whose keys can be prefixed.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface PrefixableCommandInterface
{
    /**
     * Prefixes all the keys found in the arguments of the command.
     *
     * @param string $prefix String used to prefix the keys.
     */
    public function prefixKeys($prefix);
}
