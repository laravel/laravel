<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Processes bytes as they pass through a buffer and replaces sequences in it.
 *
 * @author Chris Corbyn
 */
class Swift_StreamFilters_StringReplacementFilter implements Swift_StreamFilter
{
    /** The needle(s) to search for */
    private $_search;

    /** The replacement(s) to make */
    private $_replace;

    /**
     * Create a new StringReplacementFilter with $search and $replace.
     *
     * @param string|array $search
     * @param string|array $replace
     */
    public function __construct($search, $replace)
    {
        $this->_search = $search;
        $this->_replace = $replace;
    }

    /**
     * Returns true if based on the buffer passed more bytes should be buffered.
     *
     * @param string $buffer
     *
     * @return bool
     */
    public function shouldBuffer($buffer)
    {
        $endOfBuffer = substr($buffer, -1);
        foreach ((array) $this->_search as $needle) {
            if (false !== strpos($needle, $endOfBuffer)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Perform the actual replacements on $buffer and return the result.
     *
     * @param string $buffer
     *
     * @return string
     */
    public function filter($buffer)
    {
        return str_replace($this->_search, $this->_replace, $buffer);
    }
}
