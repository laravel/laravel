<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A collection of MIME headers.
 *
 * @author Chris Corbyn
 */
interface Swift_Mime_HeaderSet extends Swift_Mime_CharsetObserver
{
    /**
     * Add a new Mailbox Header with a list of $addresses.
     *
     * @param string       $name
     * @param array|string $addresses
     */
    public function addMailboxHeader($name, $addresses = null);

    /**
     * Add a new Date header using $timestamp (UNIX time).
     *
     * @param string $name
     * @param int    $timestamp
     */
    public function addDateHeader($name, $timestamp = null);

    /**
     * Add a new basic text header with $name and $value.
     *
     * @param string $name
     * @param string $value
     */
    public function addTextHeader($name, $value = null);

    /**
     * Add a new ParameterizedHeader with $name, $value and $params.
     *
     * @param string $name
     * @param string $value
     * @param array  $params
     */
    public function addParameterizedHeader($name, $value = null, $params = array());

    /**
     * Add a new ID header for Message-ID or Content-ID.
     *
     * @param string       $name
     * @param string|array $ids
     */
    public function addIdHeader($name, $ids = null);

    /**
     * Add a new Path header with an address (path) in it.
     *
     * @param string $name
     * @param string $path
     */
    public function addPathHeader($name, $path = null);

    /**
     * Returns true if at least one header with the given $name exists.
     *
     * If multiple headers match, the actual one may be specified by $index.
     *
     * @param string $name
     * @param int    $index
     *
     * @return bool
     */
    public function has($name, $index = 0);

    /**
     * Set a header in the HeaderSet.
     *
     * The header may be a previously fetched header via {@link get()} or it may
     * be one that has been created separately.
     *
     * If $index is specified, the header will be inserted into the set at this
     * offset.
     *
     * @param Swift_Mime_Header $header
     * @param int               $index
     */
    public function set(Swift_Mime_Header $header, $index = 0);

    /**
     * Get the header with the given $name.
     * If multiple headers match, the actual one may be specified by $index.
     * Returns NULL if none present.
     *
     * @param string $name
     * @param int    $index
     *
     * @return Swift_Mime_Header
     */
    public function get($name, $index = 0);

    /**
     * Get all headers with the given $name.
     *
     * @param string $name
     *
     * @return array
     */
    public function getAll($name = null);

    /**
     * Return the name of all Headers.
     *
     * @return array
     */
    public function listAll();

    /**
     * Remove the header with the given $name if it's set.
     *
     * If multiple headers match, the actual one may be specified by $index.
     *
     * @param string $name
     * @param int    $index
     */
    public function remove($name, $index = 0);

    /**
     * Remove all headers with the given $name.
     *
     * @param string $name
     */
    public function removeAll($name);

    /**
     * Create a new instance of this HeaderSet.
     *
     * @return Swift_Mime_HeaderSet
     */
    public function newInstance();

    /**
     * Define a list of Header names as an array in the correct order.
     *
     * These Headers will be output in the given order where present.
     *
     * @param array $sequence
     */
    public function defineOrdering(array $sequence);

    /**
     * Set a list of header names which must always be displayed when set.
     *
     * Usually headers without a field value won't be output unless set here.
     *
     * @param array $names
     */
    public function setAlwaysDisplayed(array $names);

    /**
     * Returns a string with a representation of all headers.
     *
     * @return string
     */
    public function toString();
}
