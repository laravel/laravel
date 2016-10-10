<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Creates MIME headers.
 *
 * @author Chris Corbyn
 */
interface Swift_Mime_HeaderFactory extends Swift_Mime_CharsetObserver
{
    /**
     * Create a new Mailbox Header with a list of $addresses.
     *
     * @param string       $name
     * @param array|string $addresses
     *
     * @return Swift_Mime_Header
     */
    public function createMailboxHeader($name, $addresses = null);

    /**
     * Create a new Date header using $timestamp (UNIX time).
     *
     * @param string $name
     * @param int    $timestamp
     *
     * @return Swift_Mime_Header
     */
    public function createDateHeader($name, $timestamp = null);

    /**
     * Create a new basic text header with $name and $value.
     *
     * @param string $name
     * @param string $value
     *
     * @return Swift_Mime_Header
     */
    public function createTextHeader($name, $value = null);

    /**
     * Create a new ParameterizedHeader with $name, $value and $params.
     *
     * @param string $name
     * @param string $value
     * @param array  $params
     *
     * @return Swift_Mime_ParameterizedHeader
     */
    public function createParameterizedHeader($name, $value = null, $params = array());

    /**
     * Create a new ID header for Message-ID or Content-ID.
     *
     * @param string       $name
     * @param string|array $ids
     *
     * @return Swift_Mime_Header
     */
    public function createIdHeader($name, $ids = null);

    /**
     * Create a new Path header with an address (path) in it.
     *
     * @param string $name
     * @param string $path
     *
     * @return Swift_Mime_Header
     */
    public function createPathHeader($name, $path = null);
}
