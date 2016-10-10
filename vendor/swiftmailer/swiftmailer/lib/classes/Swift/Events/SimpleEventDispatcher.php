<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The EventDispatcher which handles the event dispatching layer.
 *
 * @author Chris Corbyn
 */
class Swift_Events_SimpleEventDispatcher implements Swift_Events_EventDispatcher
{
    /** A map of event types to their associated listener types */
    private $_eventMap = array();

    /** Event listeners bound to this dispatcher */
    private $_listeners = array();

    /** Listeners queued to have an Event bubbled up the stack to them */
    private $_bubbleQueue = array();

    /**
     * Create a new EventDispatcher.
     */
    public function __construct()
    {
        $this->_eventMap = array(
            'Swift_Events_CommandEvent' => 'Swift_Events_CommandListener',
            'Swift_Events_ResponseEvent' => 'Swift_Events_ResponseListener',
            'Swift_Events_SendEvent' => 'Swift_Events_SendListener',
            'Swift_Events_TransportChangeEvent' => 'Swift_Events_TransportChangeListener',
            'Swift_Events_TransportExceptionEvent' => 'Swift_Events_TransportExceptionListener',
            );
    }

    /**
     * Create a new SendEvent for $source and $message.
     *
     * @param Swift_Transport $source
     * @param Swift_Mime_Message
     *
     * @return Swift_Events_SendEvent
     */
    public function createSendEvent(Swift_Transport $source, Swift_Mime_Message $message)
    {
        return new Swift_Events_SendEvent($source, $message);
    }

    /**
     * Create a new CommandEvent for $source and $command.
     *
     * @param Swift_Transport $source
     * @param string          $command      That will be executed
     * @param array           $successCodes That are needed
     *
     * @return Swift_Events_CommandEvent
     */
    public function createCommandEvent(Swift_Transport $source, $command, $successCodes = array())
    {
        return new Swift_Events_CommandEvent($source, $command, $successCodes);
    }

    /**
     * Create a new ResponseEvent for $source and $response.
     *
     * @param Swift_Transport $source
     * @param string          $response
     * @param bool            $valid    If the response is valid
     *
     * @return Swift_Events_ResponseEvent
     */
    public function createResponseEvent(Swift_Transport $source, $response, $valid)
    {
        return new Swift_Events_ResponseEvent($source, $response, $valid);
    }

    /**
     * Create a new TransportChangeEvent for $source.
     *
     * @param Swift_Transport $source
     *
     * @return Swift_Events_TransportChangeEvent
     */
    public function createTransportChangeEvent(Swift_Transport $source)
    {
        return new Swift_Events_TransportChangeEvent($source);
    }

    /**
     * Create a new TransportExceptionEvent for $source.
     *
     * @param Swift_Transport          $source
     * @param Swift_TransportException $ex
     *
     * @return Swift_Events_TransportExceptionEvent
     */
    public function createTransportExceptionEvent(Swift_Transport $source, Swift_TransportException $ex)
    {
        return new Swift_Events_TransportExceptionEvent($source, $ex);
    }

    /**
     * Bind an event listener to this dispatcher.
     *
     * @param Swift_Events_EventListener $listener
     */
    public function bindEventListener(Swift_Events_EventListener $listener)
    {
        foreach ($this->_listeners as $l) {
            // Already loaded
            if ($l === $listener) {
                return;
            }
        }
        $this->_listeners[] = $listener;
    }

    /**
     * Dispatch the given Event to all suitable listeners.
     *
     * @param Swift_Events_EventObject $evt
     * @param string                   $target method
     */
    public function dispatchEvent(Swift_Events_EventObject $evt, $target)
    {
        $this->_prepareBubbleQueue($evt);
        $this->_bubble($evt, $target);
    }

    /** Queue listeners on a stack ready for $evt to be bubbled up it */
    private function _prepareBubbleQueue(Swift_Events_EventObject $evt)
    {
        $this->_bubbleQueue = array();
        $evtClass = get_class($evt);
        foreach ($this->_listeners as $listener) {
            if (array_key_exists($evtClass, $this->_eventMap)
                && ($listener instanceof $this->_eventMap[$evtClass])) {
                $this->_bubbleQueue[] = $listener;
            }
        }
    }

    /** Bubble $evt up the stack calling $target() on each listener */
    private function _bubble(Swift_Events_EventObject $evt, $target)
    {
        if (!$evt->bubbleCancelled() && $listener = array_shift($this->_bubbleQueue)) {
            $listener->$target($evt);
            $this->_bubble($evt, $target);
        }
    }
}
