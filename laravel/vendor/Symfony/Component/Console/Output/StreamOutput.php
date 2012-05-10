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
 * StreamOutput writes the output to a given stream.
 *
 * Usage:
 *
 * $output = new StreamOutput(fopen('php://stdout', 'w'));
 *
 * As `StreamOutput` can use any stream, you can also use a file:
 *
 * $output = new StreamOutput(fopen('/path/to/output.log', 'a', false));
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class StreamOutput extends Output
{
    private $stream;

    /**
     * Constructor.
     *
     * @param mixed           $stream    A stream resource
     * @param integer         $verbosity The verbosity level (self::VERBOSITY_QUIET, self::VERBOSITY_NORMAL,
     *                                   self::VERBOSITY_VERBOSE)
     * @param Boolean         $decorated Whether to decorate messages or not (null for auto-guessing)
     * @param OutputFormatter $formatter Output formatter instance
     *
     * @throws \InvalidArgumentException When first argument is not a real stream
     *
     * @api
     */
    public function __construct($stream, $verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null)
    {
        if (!is_resource($stream) || 'stream' !== get_resource_type($stream)) {
            throw new \InvalidArgumentException('The StreamOutput class needs a stream as its first argument.');
        }

        $this->stream = $stream;

        if (null === $decorated) {
            $decorated = $this->hasColorSupport($decorated);
        }

        parent::__construct($verbosity, $decorated, $formatter);
    }

    /**
     * Gets the stream attached to this StreamOutput instance.
     *
     * @return resource A stream resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Writes a message to the output.
     *
     * @param string  $message A message to write to the output
     * @param Boolean $newline Whether to add a newline or not
     *
     * @throws \RuntimeException When unable to write output (should never happen)
     */
    public function doWrite($message, $newline)
    {
        if (false === @fwrite($this->stream, $message.($newline ? PHP_EOL : ''))) {
            // @codeCoverageIgnoreStart
            // should never happen
            throw new \RuntimeException('Unable to write output.');
            // @codeCoverageIgnoreEnd
        }

        fflush($this->stream);
    }

    /**
     * Returns true if the stream supports colorization.
     *
     * Colorization is disabled if not supported by the stream:
     *
     *  -  windows without ansicon
     *  -  non tty consoles
     *
     * @return Boolean true if the stream supports colorization, false otherwise
     */
    protected function hasColorSupport()
    {
        // @codeCoverageIgnoreStart
        if (DIRECTORY_SEPARATOR == '\\') {
            return false !== getenv('ANSICON');
        }

        return function_exists('posix_isatty') && @posix_isatty($this->stream);
        // @codeCoverageIgnoreEnd
    }
}
