<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * DependencyException gets thrown when a requested dependency is missing.
 *
 * @author Chris Corbyn
 */
class Swift_DependencyException extends Swift_SwiftException
{
    /**
     * Create a new DependencyException with $message.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
