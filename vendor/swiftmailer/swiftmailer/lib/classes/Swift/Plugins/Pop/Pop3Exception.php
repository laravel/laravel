<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Pop3Exception thrown when an error occurs connecting to a POP3 host.
 *
 * @author Chris Corbyn
 */
class Swift_Plugins_Pop_Pop3Exception extends Swift_IoException
{
    /**
     * Create a new Pop3Exception with $message.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
