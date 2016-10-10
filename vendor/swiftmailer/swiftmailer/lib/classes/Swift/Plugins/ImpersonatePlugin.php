<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Replaces the sender of a message.
 *
 * @author Arjen Brouwer
 */
class Swift_Plugins_ImpersonatePlugin implements Swift_Events_SendListener
{
    /**
     * The sender to impersonate.
     *
     * @var String
     */
    private $_sender;

    /**
     * Create a new ImpersonatePlugin to impersonate $sender.
     *
     * @param string $sender address
     */
    public function __construct($sender)
    {
        $this->_sender = $sender;
    }

    /**
     * Invoked immediately before the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        $headers = $message->getHeaders();

        // save current recipients
        $headers->addPathHeader('X-Swift-Return-Path', $message->getReturnPath());

        // replace them with the one to send to
        $message->setReturnPath($this->_sender);
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();

        // restore original headers
        $headers = $message->getHeaders();

        if ($headers->has('X-Swift-Return-Path')) {
            $message->setReturnPath($headers->get('X-Swift-Return-Path')->getAddress());
            $headers->removeAll('X-Swift-Return-Path');
        }
    }
}
