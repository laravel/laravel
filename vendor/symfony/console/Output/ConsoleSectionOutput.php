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
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Terminal;

/**
 * @author Pierre du Plessis <pdples@gmail.com>
 * @author Gabriel Ostrolucký <gabriel.ostrolucky@gmail.com>
 */
class ConsoleSectionOutput extends StreamOutput
{
    private array $content = [];
    private int $lines = 0;
    private array $sections;
    private Terminal $terminal;
    private int $maxHeight = 0;

    /**
     * @param resource               $stream
     * @param ConsoleSectionOutput[] $sections
     */
    public function __construct($stream, array &$sections, int $verbosity, bool $decorated, OutputFormatterInterface $formatter)
    {
        parent::__construct($stream, $verbosity, $decorated, $formatter);
        array_unshift($sections, $this);
        $this->sections = &$sections;
        $this->terminal = new Terminal();
    }

    /**
     * Defines a maximum number of lines for this section.
     *
     * When more lines are added, the section will automatically scroll to the
     * end (i.e. remove the first lines to comply with the max height).
     */
    public function setMaxHeight(int $maxHeight): void
    {
        // when changing max height, clear output of current section and redraw again with the new height
        $previousMaxHeight = $this->maxHeight;
        $this->maxHeight = $maxHeight;
        $existingContent = $this->popStreamContentUntilCurrentSection($previousMaxHeight ? min($previousMaxHeight, $this->lines) : $this->lines);

        parent::doWrite($this->getVisibleContent(), false);
        parent::doWrite($existingContent, false);
    }

    /**
     * Clears previous output for this section.
     *
     * @param int $lines Number of lines to clear. If null, then the entire output of this section is cleared
     */
    public function clear(?int $lines = null): void
    {
        if (!$this->content || !$this->isDecorated()) {
            return;
        }

        if ($lines) {
            array_splice($this->content, -$lines);
        } else {
            $lines = $this->lines;
            $this->content = [];
        }

        $this->lines -= $lines;

        parent::doWrite($this->popStreamContentUntilCurrentSection($this->maxHeight ? min($this->maxHeight, $lines) : $lines), false);
    }

    /**
     * Overwrites the previous output with a new message.
     */
    public function overwrite(string|iterable $message): void
    {
        if (!$this->content || !$this->isDecorated()) {
            $this->writeln($message);

            return;
        }

        // Replace own content and write everything in a single cursor-up + erase
        // pass, to avoid the flicker (and the line-eating artifacts on some
        // terminals) caused by calling clear() then writeln() back-to-back.
        $linesCleared = $this->lines;
        $this->content = [];
        $this->lines = 0;

        if (!is_iterable($message)) {
            $message = [$message];
        }

        foreach ($message as $line) {
            $this->addContent($this->getFormatter()->format($line) ?? '', true);
        }

        $erasedContent = $this->popStreamContentUntilCurrentSection($this->maxHeight ? min($this->maxHeight, $linesCleared) : $linesCleared);

        parent::doWrite($this->getVisibleContent(), false);
        parent::doWrite($erasedContent, false);
    }

    public function getContent(): string
    {
        return implode('', $this->content);
    }

    public function getVisibleContent(): string
    {
        if (0 === $this->maxHeight) {
            return $this->getContent();
        }

        return implode('', \array_slice($this->content, -$this->maxHeight));
    }

    /**
     * @internal
     */
    public function addContent(string $input, bool $newline = true): int
    {
        $width = $this->terminal->getWidth();
        $lines = explode(\PHP_EOL, $input);
        $linesAdded = 0;
        $count = \count($lines) - 1;
        foreach ($lines as $i => $lineContent) {
            // re-add the line break (that has been removed in the above `explode()` for
            // - every line that is not the last line
            // - if $newline is required, also add it to the last line
            if ($i < $count || $newline) {
                $lineContent .= \PHP_EOL;
            }

            // skip line if there is no text (or newline for that matter)
            if ('' === $lineContent) {
                continue;
            }

            // For the first line, check if the previous line (last entry of `$this->content`)
            // needs to be continued (i.e. does not end with a line break).
            if (0 === $i
                && (false !== $lastLine = end($this->content))
                && !str_ends_with($lastLine, \PHP_EOL)
            ) {
                // deduct the line count of the previous line
                $this->lines -= (int) ceil($this->getDisplayLength($lastLine) / $width) ?: 1;
                // concatenate previous and new line
                $lineContent = $lastLine.$lineContent;
                // replace last entry of `$this->content` with the new expanded line
                array_splice($this->content, -1, 1, $lineContent);
            } else {
                // otherwise just add the new content
                $this->content[] = $lineContent;
            }

            $linesAdded += (int) ceil($this->getDisplayLength($lineContent) / $width) ?: 1;
        }

        $this->lines += $linesAdded;

        return $linesAdded;
    }

    /**
     * @internal
     */
    public function addNewLineOfInputSubmit(): void
    {
        $this->content[] = \PHP_EOL;
        ++$this->lines;
    }

    protected function doWrite(string $message, bool $newline): void
    {
        // Simulate newline behavior for consistent output formatting, avoiding extra logic
        if (!$newline && str_ends_with($message, \PHP_EOL)) {
            $message = substr($message, 0, -\strlen(\PHP_EOL));
            $newline = true;
        }

        if (!$this->isDecorated()) {
            parent::doWrite($message, $newline);

            return;
        }

        // Check if the previous line (last entry of `$this->content`) needs to be continued
        // (i.e. does not end with a line break). In which case, it needs to be erased first.
        $linesToClear = $deleteLastLine = ($lastLine = end($this->content) ?: '') && !str_ends_with($lastLine, \PHP_EOL) ? 1 : 0;

        $linesAdded = $this->addContent($message, $newline);

        if ($lineOverflow = $this->maxHeight > 0 && $this->lines > $this->maxHeight) {
            // on overflow, clear the whole section and redraw again (to remove the first lines)
            $linesToClear = $this->maxHeight;
        }

        $erasedContent = $this->popStreamContentUntilCurrentSection($linesToClear);

        if ($lineOverflow) {
            // redraw existing lines of the section
            $previousLinesOfSection = \array_slice($this->content, $this->lines - $this->maxHeight, $this->maxHeight - $linesAdded);
            parent::doWrite(implode('', $previousLinesOfSection), false);
        }

        // if the last line was removed, re-print its content together with the new content.
        // otherwise, just print the new content.
        parent::doWrite($deleteLastLine ? $lastLine.$message : $message, true);
        parent::doWrite($erasedContent, false);
    }

    /**
     * At initial stage, cursor is at the end of stream output. This method makes cursor crawl upwards until it hits
     * current section. Then it erases content it crawled through. Optionally, it erases part of current section too.
     */
    private function popStreamContentUntilCurrentSection(int $numberOfLinesToClearFromCurrentSection = 0): string
    {
        $numberOfLinesToClear = $numberOfLinesToClearFromCurrentSection;
        $erasedContent = [];

        foreach ($this->sections as $section) {
            if ($section === $this) {
                break;
            }

            $numberOfLinesToClear += $section->maxHeight ? min($section->lines, $section->maxHeight) : $section->lines;
            if ('' !== $sectionContent = $section->getVisibleContent()) {
                if (!str_ends_with($sectionContent, \PHP_EOL)) {
                    $sectionContent .= \PHP_EOL;
                }
                $erasedContent[] = $sectionContent;
            }
        }

        if ($numberOfLinesToClear > 0) {
            // move cursor up n lines
            parent::doWrite(\sprintf("\x1b[%dA", $numberOfLinesToClear), false);
            // erase to end of screen
            parent::doWrite("\x1b[0J", false);
        }

        return implode('', array_reverse($erasedContent));
    }

    private function getDisplayLength(string $text): int
    {
        return Helper::width(Helper::removeDecoration($this->getFormatter(), str_replace("\t", '        ', $text)));
    }
}
