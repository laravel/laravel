<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * An OutputByteStream which specifically reads from a file.
 *
 * @author Chris Corbyn
 */
interface Swift_FileStream extends Swift_OutputByteStream
{
    /**
     * Get the complete path to the file.
     *
     * @return string
     */
    public function getPath();
}
