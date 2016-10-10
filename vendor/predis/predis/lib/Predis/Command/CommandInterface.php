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
 * Defines an abstraction representing a Redis command.
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface CommandInterface
{
    /**
     * Gets the ID of a Redis command.
     *
     * @return string
     */
    public function getId();

    /**
     * Set the hash for the command.
     *
     * @param int $hash Calculated hash.
     */
    public function setHash($hash);

    /**
     * Returns the hash of the command.
     *
     * @return int
     */
    public function getHash();

    /**
     * Sets the arguments for the command.
     *
     * @param array $arguments List of arguments.
     */
    public function setArguments(Array $arguments);

    /**
     * Sets the raw arguments for the command without processing them.
     *
     * @param array $arguments List of arguments.
     */
    public function setRawArguments(Array $arguments);

    /**
     * Gets the arguments of the command.
     *
     * @return array
     */
    public function getArguments();

    /**
     * Gets the argument of the command at the specified index.
     *
     * @param  int   $index Index of the desired argument.
     * @return mixed
     */
    public function getArgument($index);

    /**
     * Parses a reply buffer and returns a PHP object.
     *
     * @param  string $data Binary string containing the whole reply.
     * @return mixed
     */
    public function parseResponse($data);
}
