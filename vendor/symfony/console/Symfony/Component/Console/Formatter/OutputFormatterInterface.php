<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Formatter;

/**
 * Formatter interface for console output.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @api
 */
interface OutputFormatterInterface
{
    /**
     * Sets the decorated flag.
     *
     * @param bool    $decorated Whether to decorate the messages or not
     *
     * @api
     */
    public function setDecorated($decorated);

    /**
     * Gets the decorated flag.
     *
     * @return bool    true if the output will decorate messages, false otherwise
     *
     * @api
     */
    public function isDecorated();

    /**
     * Sets a new style.
     *
     * @param string                        $name  The style name
     * @param OutputFormatterStyleInterface $style The style instance
     *
     * @api
     */
    public function setStyle($name, OutputFormatterStyleInterface $style);

    /**
     * Checks if output formatter has style with specified name.
     *
     * @param string $name
     *
     * @return bool
     *
     * @api
     */
    public function hasStyle($name);

    /**
     * Gets style options from style with specified name.
     *
     * @param string $name
     *
     * @return OutputFormatterStyleInterface
     *
     * @api
     */
    public function getStyle($name);

    /**
     * Formats a message according to the given styles.
     *
     * @param string $message The message to style
     *
     * @return string The styled message
     *
     * @api
     */
    public function format($message);
}
