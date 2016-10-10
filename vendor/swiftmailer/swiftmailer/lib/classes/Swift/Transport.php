<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sends Messages via an abstract Transport subsystem.
 *
 * @author Chris Corbyn
 */
interface Swift_Transport
{
    /**
     * Test if this Transport mechanism has started.
     *
     * @return bool
     */
    public function isStarted();

    /**
     * Start this Transport mechanism.
     */
    public function start();

    /**
     * Stop this Transport mechanism.
     */
    public function stop();

    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * @param Swift_Mime_Message $message
     * @param string[]           $failedRecipients An array of failures by-reference
     *
     * @return int
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null);

    /**
     * Register a plugin in the Transport.
     *
     * @param Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin);
}
