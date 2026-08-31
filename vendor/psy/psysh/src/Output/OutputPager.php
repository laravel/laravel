<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Output;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * An output pager is much the same as a regular OutputInterface, but allows
 * the stream to be flushed to a pager periodically.
 */
interface OutputPager extends OutputInterface
{
    // TODO: Add doWrite to the OutputPager interface.
    // /**
    //  * Writes a message to the output.
    //  *
    //  * @todo tighten up param types?
    //  *
    //  * @param string $message A message to write to the output
    //  * @param bool   $newline Whether to add a newline or not
    //  */
    // public function doWrite($message, $newline): void;

    /**
     * Close the current pager process.
     */
    public function close();
}
