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
class Swift_Mime_SimpleHeaderSet implements Swift_Mime_HeaderSet
{
    /** HeaderFactory */
    private $_factory;

    /** Collection of set Headers */
    private $_headers = array();

    /** Field ordering details */
    private $_order = array();

    /** List of fields which are required to be displayed */
    private $_required = array();

    /** The charset used by Headers */
    private $_charset;

    /**
     * Create a new SimpleHeaderSet with the given $factory.
     *
     * @param Swift_Mime_HeaderFactory $factory
     * @param string                   $charset
     */
    public function __construct(Swift_Mime_HeaderFactory $factory, $charset = null)
    {
        $this->_factory = $factory;
        if (isset($charset)) {
            $this->setCharset($charset);
        }
    }

    /**
     * Set the charset used by these headers.
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->_charset = $charset;
        $this->_factory->charsetChanged($charset);
        $this->_notifyHeadersOfCharset($charset);
    }

    /**
     * Add a new Mailbox Header with a list of $addresses.
     *
     * @param string       $name
     * @param array|string $addresses
     */
    public function addMailboxHeader($name, $addresses = null)
    {
        $this->_storeHeader($name,
        $this->_factory->createMailboxHeader($name, $addresses));
    }

    /**
     * Add a new Date header using $timestamp (UNIX time).
     *
     * @param string $name
     * @param int    $timestamp
     */
    public function addDateHeader($name, $timestamp = null)
    {
        $this->_storeHeader($name,
        $this->_factory->createDateHeader($name, $timestamp));
    }

    /**
     * Add a new basic text header with $name and $value.
     *
     * @param string $name
     * @param string $value
     */
    public function addTextHeader($name, $value = null)
    {
        $this->_storeHeader($name,
        $this->_factory->createTextHeader($name, $value));
    }

    /**
     * Add a new ParameterizedHeader with $name, $value and $params.
     *
     * @param string $name
     * @param string $value
     * @param array  $params
     */
    public function addParameterizedHeader($name, $value = null, $params = array())
    {
        $this->_storeHeader($name, $this->_factory->createParameterizedHeader($name, $value, $params));
    }

    /**
     * Add a new ID header for Message-ID or Content-ID.
     *
     * @param string       $name
     * @param string|array $ids
     */
    public function addIdHeader($name, $ids = null)
    {
        $this->_storeHeader($name, $this->_factory->createIdHeader($name, $ids));
    }

    /**
     * Add a new Path header with an address (path) in it.
     *
     * @param string $name
     * @param string $path
     */
    public function addPathHeader($name, $path = null)
    {
        $this->_storeHeader($name, $this->_factory->createPathHeader($name, $path));
    }

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
    public function has($name, $index = 0)
    {
        $lowerName = strtolower($name);

        return array_key_exists($lowerName, $this->_headers) && array_key_exists($index, $this->_headers[$lowerName]);
    }

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
    public function set(Swift_Mime_Header $header, $index = 0)
    {
        $this->_storeHeader($header->getFieldName(), $header, $index);
    }

    /**
     * Get the header with the given $name.
     *
     * If multiple headers match, the actual one may be specified by $index.
     * Returns NULL if none present.
     *
     * @param string $name
     * @param int    $index
     *
     * @return Swift_Mime_Header
     */
    public function get($name, $index = 0)
    {
        if ($this->has($name, $index)) {
            $lowerName = strtolower($name);

            return $this->_headers[$lowerName][$index];
        }
    }

    /**
     * Get all headers with the given $name.
     *
     * @param string $name
     *
     * @return array
     */
    public function getAll($name = null)
    {
        if (!isset($name)) {
            $headers = array();
            foreach ($this->_headers as $collection) {
                $headers = array_merge($headers, $collection);
            }

            return $headers;
        }

        $lowerName = strtolower($name);
        if (!array_key_exists($lowerName, $this->_headers)) {
            return array();
        }

        return $this->_headers[$lowerName];
    }

    /**
     * Return the name of all Headers.
     *
     * @return array
     */
    public function listAll()
    {
        $headers = $this->_headers;
        if ($this->_canSort()) {
            uksort($headers, array($this, '_sortHeaders'));
        }

        return array_keys($headers);
    }

    /**
     * Remove the header with the given $name if it's set.
     *
     * If multiple headers match, the actual one may be specified by $index.
     *
     * @param string $name
     * @param int    $index
     */
    public function remove($name, $index = 0)
    {
        $lowerName = strtolower($name);
        unset($this->_headers[$lowerName][$index]);
    }

    /**
     * Remove all headers with the given $name.
     *
     * @param string $name
     */
    public function removeAll($name)
    {
        $lowerName = strtolower($name);
        unset($this->_headers[$lowerName]);
    }

    /**
     * Create a new instance of this HeaderSet.
     *
     * @return Swift_Mime_HeaderSet
     */
    public function newInstance()
    {
        return new self($this->_factory);
    }

    /**
     * Define a list of Header names as an array in the correct order.
     *
     * These Headers will be output in the given order where present.
     *
     * @param array $sequence
     */
    public function defineOrdering(array $sequence)
    {
        $this->_order = array_flip(array_map('strtolower', $sequence));
    }

    /**
     * Set a list of header names which must always be displayed when set.
     *
     * Usually headers without a field value won't be output unless set here.
     *
     * @param array $names
     */
    public function setAlwaysDisplayed(array $names)
    {
        $this->_required = array_flip(array_map('strtolower', $names));
    }

    /**
     * Notify this observer that the entity's charset has changed.
     *
     * @param string $charset
     */
    public function charsetChanged($charset)
    {
        $this->setCharset($charset);
    }

    /**
     * Returns a string with a representation of all headers.
     *
     * @return string
     */
    public function toString()
    {
        $string = '';
        $headers = $this->_headers;
        if ($this->_canSort()) {
            uksort($headers, array($this, '_sortHeaders'));
        }
        foreach ($headers as $collection) {
            foreach ($collection as $header) {
                if ($this->_isDisplayed($header) || $header->getFieldBody() != '') {
                    $string .= $header->toString();
                }
            }
        }

        return $string;
    }

    /**
     * Returns a string representation of this object.
     *
     * @return string
     *
     * @see toString()
     */
    public function __toString()
    {
        return $this->toString();
    }

    /** Save a Header to the internal collection */
    private function _storeHeader($name, Swift_Mime_Header $header, $offset = null)
    {
        if (!isset($this->_headers[strtolower($name)])) {
            $this->_headers[strtolower($name)] = array();
        }
        if (!isset($offset)) {
            $this->_headers[strtolower($name)][] = $header;
        } else {
            $this->_headers[strtolower($name)][$offset] = $header;
        }
    }

    /** Test if the headers can be sorted */
    private function _canSort()
    {
        return count($this->_order) > 0;
    }

    /** uksort() algorithm for Header ordering */
    private function _sortHeaders($a, $b)
    {
        $lowerA = strtolower($a);
        $lowerB = strtolower($b);
        $aPos = array_key_exists($lowerA, $this->_order)
            ? $this->_order[$lowerA]
            : -1;
        $bPos = array_key_exists($lowerB, $this->_order)
            ? $this->_order[$lowerB]
            : -1;

        if ($aPos == -1) {
            return 1;
        } elseif ($bPos == -1) {
            return -1;
        }

        return ($aPos < $bPos) ? -1 : 1;
    }

    /** Test if the given Header is always displayed */
    private function _isDisplayed(Swift_Mime_Header $header)
    {
        return array_key_exists(strtolower($header->getFieldName()), $this->_required);
    }

    /** Notify all Headers of the new charset */
    private function _notifyHeadersOfCharset($charset)
    {
        foreach ($this->_headers as $headerGroup) {
            foreach ($headerGroup as $header) {
                $header->setCharset($charset);
            }
        }
    }

    /**
     * Make a deep copy of object.
     */
    public function __clone()
    {
        $this->_factory = clone $this->_factory;
        foreach ($this->_headers as $groupKey => $headerGroup) {
            foreach ($headerGroup as $key => $header) {
                $this->_headers[$groupKey][$key] = clone $header;
            }
        }
    }
}
