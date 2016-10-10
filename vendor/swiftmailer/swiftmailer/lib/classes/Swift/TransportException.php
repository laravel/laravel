<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * TransportException thrown when an error occurs in the Transport subsystem.
 *
 * @author Chris Corbyn
 */
class Swift_TransportException extends Swift_IoException
{
    /**
     * Create a new TransportException with $message.
     *
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
