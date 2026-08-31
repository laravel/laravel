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

use Symfony\Component\Console\Output\StreamOutput;

/**
 * ProcOutputPager class.
 *
 * A ProcOutputPager instance wraps a regular StreamOutput's stream. Rather
 * than writing directly to the stream, it shells out to a pager process and
 * gives that process the stream as stdout. This means regular *nix commands
 * like `less` and `more` can be used to page large amounts of output.
 */
class ProcOutputPager extends StreamOutput implements OutputPager
{
    /** @var ?resource */
    private $proc = null;
    /** @var ?resource */
    private $pipe = null;
    /** @var resource */
    private $stream;
    private string $cmd;
    private bool $closedEarly = false;

    /**
     * Constructor.
     *
     * @param StreamOutput $output
     * @param string       $cmd    Pager process command (default: 'less -R -F -X')
     */
    public function __construct(StreamOutput $output, string $cmd = 'less -R -F -X')
    {
        $this->stream = $output->getStream();
        $this->cmd = $cmd;
    }

    /**
     * Writes a message to the output.
     *
     * @param string $message A message to write to the output
     * @param bool   $newline Whether to add a newline or not
     *
     * @throws \RuntimeException When unable to write output (should never happen)
     */
    public function doWrite($message, $newline): void
    {
        // If the pager was closed early (user quit), don't reopen it.
        if ($this->closedEarly) {
            return;
        }

        $pipe = $this->getPipe();
        if (false === @\fwrite($pipe, $message.($newline ? \PHP_EOL : ''))) {
            // @codeCoverageIgnoreStart
            // When the message is sufficiently long, writing to the pipe fails
            // if the pager process is closed before the entire message is read.
            //
            // This is a normal condition, so we close the pipe and ignore any
            // further writes until the next paging session.
            $this->close();
            $this->closedEarly = true;

            return;
            // @codeCoverageIgnoreEnd
        }

        \fflush($pipe);
    }

    /**
     * Close the current pager process.
     */
    public function close()
    {
        if (\is_resource($this->pipe)) {
            \fclose($this->pipe);
        }

        if (\is_resource($this->proc)) {
            $exit = \proc_close($this->proc);
            if ($exit !== 0) {
                throw new \RuntimeException('Error closing output stream');
            }
        }

        $this->pipe = null;
        $this->proc = null;
        $this->closedEarly = false;
    }

    /**
     * Get a pipe for paging output.
     *
     * If no active pager process exists, fork one and return its input pipe.
     */
    private function getPipe()
    {
        if (!isset($this->pipe) || !isset($this->proc)) {
            $desc = [['pipe', 'r'], $this->stream, \fopen('php://stderr', 'w')];
            $this->proc = \proc_open($this->cmd, $desc, $pipes);

            if (!\is_resource($this->proc)) {
                throw new \RuntimeException('Error opening output stream');
            }

            $this->pipe = $pipes[0];
        }

        return $this->pipe;
    }
}
