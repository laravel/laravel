<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generated when a response is received on a SMTP connection.
 *
 * @author Chris Corbyn
 */
class Swift_Events_ResponseEvent extends Swift_Events_EventObject
{
    /**
     * The overall result.
     *
     * @var bool
     */
    private $_valid;

    /**
     * The response received from the server.
     *
     * @var string
     */
    private $_response;

    /**
     * Create a new ResponseEvent for $source and $response.
     *
     * @param Swift_Transport $source
     * @param string          $response
     * @param bool            $valid
     */
    public function __construct(Swift_Transport $source, $response, $valid = false)
    {
        parent::__construct($source);
        $this->_response = $response;
        $this->_valid = $valid;
    }

    /**
     * Get the response which was received from the server.
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Get the success status of this Event.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->_valid;
    }
}
