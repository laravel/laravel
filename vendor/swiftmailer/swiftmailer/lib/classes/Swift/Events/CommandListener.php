<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Listens for Transports to send commands to the server.
 *
 * @author Chris Corbyn
 */
interface Swift_Events_CommandListener extends Swift_Events_EventListener
{
    /**
     * Invoked immediately following a command being sent.
     *
     * @param Swift_Events_CommandEvent $evt
     */
    public function commandSent(Swift_Events_CommandEvent $evt);
}
