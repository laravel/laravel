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

use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 * OutputInterface is the interface implemented by all Output classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
interface OutputInterface
{
    const VERBOSITY_QUIET   = 0;
    const VERBOSITY_NORMAL  = 1;
    const VERBOSITY_VERBOSE = 2;

    const OUTPUT_NORMAL = 0;
    const OUTPUT_RAW = 1;
    const OUTPUT_PLAIN = 2;

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages The message as an array of lines of a single string
     * @param Boolean      $newline  Whether to add a newline or not
     * @param integer      $type     The type of output
     *
     * @throws \InvalidArgumentException When unknown output type is given
     *
     * @api
     */
    function write($messages, $newline = false, $type = 0);

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array $messages The message as an array of lines of a single string
     * @param integer      $type     The type of output
     *
     * @api
     */
    function writeln($messages, $type = 0);

    /**
     * Sets the verbosity of the output.
     *
     * @param integer $level The level of verbosity
     *
     * @api
     */
    function setVerbosity($level);

    /**
     * Gets the current verbosity of the output.
     *
     * @return integer The current level of verbosity
     *
     * @api
     */
    function getVerbosity();

    /**
     * Sets the decorated flag.
     *
     * @param Boolean $decorated Whether to decorate the messages or not
     *
     * @api
     */
    function setDecorated($decorated);

    /**
     * Gets the decorated flag.
     *
     * @return Boolean true if the output will decorate messages, false otherwise
     *
     * @api
     */
    function isDecorated();

    /**
     * Sets output formatter.
     *
     * @param OutputFormatterInterface $formatter
     *
     * @api
     */
    function setFormatter(OutputFormatterInterface $formatter);

    /**
     * Returns current output formatter instance.
     *
     * @return  OutputFormatterInterface
     *
     * @api
     */
    function getFormatter();
}
