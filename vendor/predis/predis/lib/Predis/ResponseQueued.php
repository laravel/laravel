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
 * Represents a +QUEUED response returned by Redis as a reply to each command
 * executed inside a MULTI/ EXEC transaction.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseQueued implements ResponseObjectInterface
{
    /**
     * Converts the object to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return 'QUEUED';
    }

    /**
     * Returns the value of the specified property.
     *
     * @param  string $property Name of the property.
     * @return mixed
     */
    public function __get($property)
    {
        return $property === 'queued';
    }

    /**
     * Checks if the specified property is set.
     *
     * @param  string $property Name of the property.
     * @return bool
     */
    public function __isset($property)
    {
        return $property === 'queued';
    }
}
