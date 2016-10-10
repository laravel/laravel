<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Processes bytes as they pass through a stream and performs filtering.
 *
 * @author Chris Corbyn
 */
interface Swift_StreamFilter
{
    /**
     * Based on the buffer given, this returns true if more buffering is needed.
     *
     * @param mixed $buffer
     *
     * @return bool
     */
    public function shouldBuffer($buffer);

    /**
     * Filters $buffer and returns the changes.
     *
     * @param mixed $buffer
     *
     * @return mixed
     */
    public function filter($buffer);
}
