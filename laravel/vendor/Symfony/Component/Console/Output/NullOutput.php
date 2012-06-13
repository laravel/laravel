<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Output;

/**
 * NullOutput suppresses all output.
 *
 *     $output = new NullOutput();
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class NullOutput extends Output
{
    /**
     * Writes a message to the output.
     *
     * @param string $message A message to write to the output
     * @param Boolean $newline Whether to add a newline or not
     */
    public function doWrite($message, $newline)
    {
    }
}
