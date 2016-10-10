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
 * Represents an error returned by Redis (replies identified by "-" in the
 * Redis response protocol) during the execution of an operation on the server.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ResponseErrorInterface extends ResponseObjectInterface
{
    /**
     * Returns the error message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Returns the error type (e.g. ERR, ASK, MOVED)
     *
     * @return string
     */
    public function getErrorType();
}
