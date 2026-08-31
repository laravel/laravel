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

use Psy\Readline\Interactive\Pager;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * OutputPager backed by the userland interactive Pager.
 *
 * Buffers writes by line during a paging session and hands the collected
 * string[] to Pager::page() on close. Pager itself handles the inline-vs-
 * interactive decision (small content prints inline, large content engages
 * the interactive viewport).
 */
class BuiltinOutputPager extends StreamOutput implements OutputPager
{
    private Pager $pager;

    /** @var string[] Buffered styled lines for the current paging session. */
    private array $buffer = [];

    /** Partial trailing line (write() without a final newline). */
    private string $partialLine = '';

    public function __construct(StreamOutput $output, Pager $pager)
    {
        parent::__construct($output->getStream());
        $this->pager = $pager;
    }

    /**
     * {@inheritdoc}
     */
    public function doWrite($message, $newline): void
    {
        $combined = $this->partialLine.$message;
        if ($newline) {
            $combined .= "\n";
        }

        // Normalize CRLF and bare CR so a stray \r doesn't slip through and
        // render as a cursor-to-column-1 jump when the line is painted.
        $combined = \strtr($combined, ["\r\n" => "\n", "\r" => "\n"]);

        $segments = \explode("\n", $combined);
        // Last segment is whatever follows the final \n: empty if newline-terminated,
        // otherwise a partial line we save for the next write.
        $this->partialLine = (string) \array_pop($segments);

        foreach ($segments as $line) {
            $this->buffer[] = $line;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if ($this->partialLine !== '') {
            $this->buffer[] = $this->partialLine;
            $this->partialLine = '';
        }

        $lines = $this->buffer;
        $this->buffer = [];

        if (empty($lines)) {
            return;
        }

        try {
            $this->pager->page($lines);
        } catch (\Throwable $e) {
            // The interactive pager failed (terminal lost, render error,
            // raw mode unavailable, etc.). Don't swallow the content:
            // fall back to writing it inline through the underlying stream
            // so the user still sees their output.
            $this->emitInline($lines);
        }
    }

    /**
     * Write buffered lines directly to the underlying stream as a last-
     * resort fallback when the interactive pager can't run.
     *
     * @param string[] $lines
     */
    private function emitInline(array $lines): void
    {
        $stream = $this->getStream();
        foreach ($lines as $line) {
            @\fwrite($stream, $line."\n");
        }
    }
}
