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
 * Exception class that identifies server-side Redis errors.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerException extends PredisException implements ResponseErrorInterface
{
    /**
     * Gets the type of the error returned by Redis.
     *
     * @return string
     */
    public function getErrorType()
    {
        list($errorType, ) = explode(' ', $this->getMessage(), 2);

        return $errorType;
    }

    /**
     * Converts the exception to an instance of ResponseError.
     *
     * @return ResponseError
     */
    public function toResponseError()
    {
        return new ResponseError($this->getMessage());
    }
}
