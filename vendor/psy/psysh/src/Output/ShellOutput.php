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

use Psy\Formatter\LinkFormatter;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * A ConsoleOutput subclass specifically for Psy Shell output.
 */
class ShellOutput extends ConsoleOutput
{
    const NUMBER_LINES = 128;

    private int $paging = 0;
    private OutputPager $pager;
    private Theme $theme;
    private bool $visibleOutputWritten = false;

    /**
     * Construct a ShellOutput instance.
     *
     * @param mixed                         $verbosity (default: self::VERBOSITY_NORMAL)
     * @param bool|null                     $decorated (default: null)
     * @param OutputFormatterInterface|null $formatter (default: null)
     * @param string|OutputPager|null       $pager     (default: null)
     */
    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, ?OutputFormatterInterface $formatter = null, $pager = null, $theme = null)
    {
        parent::__construct($verbosity, $decorated, $formatter);

        $this->theme = $theme ?? new Theme('modern');
        $this->initFormatters();

        $this->pager = $this->createPager($pager);
    }

    /**
     * Page multiple lines of output.
     *
     * The output pager is started
     *
     * If $messages is callable, it will be called, passing this output instance
     * for rendering. Otherwise, all passed $messages are paged to output.
     *
     * Upon completion, the output pager is flushed.
     *
     * @param string|array|\Closure $messages A string, array of strings or a callback
     * @param int                   $type     (default: 0)
     */
    public function page($messages, int $type = 0)
    {
        if (\is_string($messages)) {
            // Split on newlines to avoid O(n^2) performance in Symfony's OutputFormatter
            // when processing large strings with many style tags.
            $messages = \explode("\n", $messages);
        }

        if (!\is_array($messages) && !\is_callable($messages)) {
            throw new \InvalidArgumentException('Paged output requires a string, array or callback');
        }

        $this->startPaging();

        if (\is_callable($messages)) {
            $messages($this);
        } else {
            $this->write($messages, true, $type);
        }

        $this->stopPaging();
    }

    /**
     * Set a listener invoked whenever visible output is written.
     *
     * @deprecated No longer used. ShellOutput tracks visible writes internally.
     */
    public function setWriteListener(?callable $listener): void
    {
    }

    /**
     * Start sending output to the output pager.
     */
    public function startPaging()
    {
        $this->paging++;
    }

    /**
     * Stop paging output and flush the output pager.
     */
    public function stopPaging()
    {
        $this->paging--;
        $this->closePager();
    }

    /**
     * Writes a message to the output.
     *
     * Optionally, pass `$type | self::NUMBER_LINES` as the $type parameter to
     * number the lines of output.
     *
     * @throws \InvalidArgumentException When unknown output type is given
     *
     * @param string|array $messages The message as an array of lines or a single string
     * @param bool         $newline  Whether to add a newline or not
     * @param int          $type     The type of output
     */
    public function write($messages, $newline = false, $type = 0): void
    {
        if ($this->getVerbosity() === self::VERBOSITY_QUIET) {
            return;
        }

        $messages = (array) $messages;

        if ($type & self::NUMBER_LINES) {
            $pad = \strlen((string) \count($messages));
            $template = $this->isDecorated() && $this->getFormatter()->hasStyle('whisper')
                ? "<whisper>%{$pad}s:</whisper> %s"
                : "%{$pad}s: %s";

            if ($type & self::OUTPUT_RAW) {
                $messages = \array_map([OutputFormatter::class, 'escape'], $messages);
            }

            $indent = \str_repeat(' ', $pad + 2); // Indent continuation lines to align with text

            foreach ($messages as $i => $line) {
                // Check if line contains newlines (multi-line entry)
                if (\strpos($line, "\n") !== false) {
                    // Split into lines and indent continuation lines
                    $lines = \explode("\n", $line);
                    $firstLine = \array_shift($lines);
                    $indentedLines = \array_map(function ($l) use ($indent) {
                        return $indent.$l;
                    }, $lines);

                    $messages[$i] = \sprintf($template, $i, $firstLine);
                    if (!empty($indentedLines)) {
                        $messages[$i] .= "\n".\implode("\n", $indentedLines);
                    }
                } else {
                    $messages[$i] = \sprintf($template, $i, $line);
                }
            }

            // clean this up for super.
            $type = $type & ~self::NUMBER_LINES & ~self::OUTPUT_RAW;
        }

        parent::write($messages, $newline, $type);
    }

    /**
     * Writes a message to the output.
     *
     * Handles paged output, or writes directly to the output stream.
     *
     * @param string $message A message to write to the output
     * @param bool   $newline Whether to add a newline or not
     */
    public function doWrite($message, $newline): void
    {
        $this->visibleOutputWritten = true;

        if ($this->paging > 0) {
            // @todo Update OutputPager interface to require doWrite
            if ($this->pager instanceof ProcOutputPager || $this->pager instanceof PassthruPager || $this->pager instanceof BuiltinOutputPager) {
                $this->pager->doWrite($message, $newline);
            } else {
                $this->pager->write($message, $newline, self::OUTPUT_RAW);
            }
        } else {
            parent::doWrite($message, $newline);
        }
    }

    /**
     * Reset visible output tracking and return whether output was written.
     */
    public function consumeVisibleOutputWritten(): bool
    {
        $written = $this->visibleOutputWritten;
        $this->visibleOutputWritten = false;

        return $written;
    }

    /**
     * Set the output Theme.
     */
    public function setTheme(Theme $theme)
    {
        $this->theme = $theme;
        $this->initFormatters();
    }

    /**
     * Replace the output pager used for future paging operations.
     *
     * @param string|OutputPager|null $pager
     */
    public function setPager($pager): void
    {
        $this->closePager();
        $this->paging = 0;
        $this->pager = $this->createPager($pager);
    }

    /**
     * Flush and close the output pager.
     */
    private function closePager()
    {
        if ($this->paging <= 0) {
            $this->pager->close();
        }
    }

    /**
     * @param string|OutputPager|null $pager
     */
    private function createPager($pager): OutputPager
    {
        if ($pager === null) {
            return new PassthruPager($this);
        }

        if (\is_string($pager)) {
            return new ProcOutputPager($this, $pager);
        }

        if ($pager instanceof OutputPager) {
            return $pager;
        }

        throw new \InvalidArgumentException('Unexpected pager parameter: '.$pager);
    }

    /**
     * Initialize output formatter styles.
     */
    private function initFormatters()
    {
        $useGrayFallback = !Theme::grayExists($this->getFormatter());
        $this->theme->applyStyles($this->getFormatter(), $useGrayFallback);
        $this->theme->applyErrorStyles($this->getErrorOutput()->getFormatter(), $useGrayFallback);

        // Set inline styles for hyperlinks
        LinkFormatter::setStyles($this->theme->getInlineStyles($useGrayFallback));
    }
}
