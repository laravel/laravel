<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive;

use Psy\Readline\Interactive\Input\Key;
use Psy\Readline\Interactive\Input\StdinReader;
use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Terminal as SymfonyTerminal;

/**
 * Terminal abstraction for cursor control and I/O.
 *
 * Uses Symfony Console components for terminal control, capability detection,
 * and themed output formatting.
 */
class Terminal
{
    private ?string $originalSttyMode = null;
    private StdinReader $input;
    private StreamOutput $output;
    private bool $bracketedPasteEnabled = false;
    private bool $altScreenEnabled = false;
    private bool $mouseReportingEnabled = false;
    private bool $useUnicode = true;
    private SymfonyTerminal $symfonyTerminal;
    private Cursor $cursor;

    /**
     * Dirty flags let FrameRenderer detect out-of-band writes
     * that invalidate its cached frame or cursor position.
     */
    private bool $dirty = false;
    private bool $cursorRowUnknown = false;

    private int $frameRenderDepth = 0;

    /**
     * Create a new Terminal instance.
     */
    public function __construct(StdinReader $input, StreamOutput $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->symfonyTerminal = new SymfonyTerminal();
        $this->cursor = new Cursor($output);
    }

    /**
     * Check whether any out-of-band write has occurred since the last frame.
     */
    public function isDirty(): bool
    {
        return $this->dirty;
    }

    /**
     * Check whether cursor row tracking has been invalidated.
     */
    public function isCursorRowUnknown(): bool
    {
        return $this->cursorRowUnknown;
    }

    /**
     * Begin a frame render, clearing dirty flags.
     *
     * Writes during a frame render don't set the dirty flags.
     */
    public function beginFrameRender(): void
    {
        $this->frameRenderDepth++;
        $this->dirty = false;
        $this->cursorRowUnknown = false;
    }

    /**
     * End a frame render.
     */
    public function endFrameRender(): void
    {
        $this->frameRenderDepth = \max(0, $this->frameRenderDepth - 1);
    }

    /**
     * Write text to the terminal and flush.
     *
     * Writes directly to the stream, bypassing the output formatter.
     *
     * By default, out-of-band writes (outside a frame render) mark the
     * frame dirty and check for newlines that would move the cursor row.
     * Pass $visible = false for writes that don't affect screen content
     * or cursor position (e.g. BEL, mode-change escape sequences).
     */
    public function write(string $text, bool $visible = true): void
    {
        if ($visible) {
            $movesRow = \strpbrk($text, "\n\v\f") !== false;
            $this->noteOutOfBandWrite($movesRow);
        }

        $this->output->write($text, false, OutputInterface::OUTPUT_RAW);
    }

    /**
     * Flush the output stream.
     */
    public function flush(): void
    {
        \fflush($this->output->getStream());
    }

    /**
     * Enable or disable Unicode in PsySH-owned terminal UI.
     */
    public function setUseUnicode(bool $useUnicode): void
    {
        $this->useUnicode = $useUnicode;
    }

    /**
     * Check whether PsySH-owned terminal UI should use Unicode.
     */
    public function useUnicode(): bool
    {
        return $this->useUnicode;
    }

    /**
     * Invalidate frame renderer caches before the next render pass.
     *
     * Useful when a mode switch should repaint the full input frame.
     */
    public function invalidateFrame(bool $cursorRowUnknown = false): void
    {
        $this->dirty = true;
        if ($cursorRowUnknown) {
            $this->cursorRowUnknown = true;
        }
    }

    /**
     * Get the output formatter.
     *
     * Provides access to the formatter for cases where direct formatting control is needed.
     */
    public function getFormatter(): OutputFormatterInterface
    {
        return $this->output->getFormatter();
    }

    /**
     * Format text using the output formatter.
     *
     * Applies theme styles like <whisper>, <input_highlight>, etc.
     *
     * @return string Formatted text with ANSI codes
     */
    public function format(string $text): string
    {
        return $this->getFormatter()->format($text);
    }

    /**
     * Write formatted text to the terminal.
     *
     * Text is processed through the output formatter, applying theme styles
     * like <whisper>, <input_highlight>, etc.
     */
    public function writeFormatted(string $text): void
    {
        $this->write($this->format($text));
    }

    /**
     * Move cursor to a specific column (1-indexed, terminal convention).
     */
    public function moveCursorToColumn(int $column): void
    {
        $this->noteOutOfBandWrite();
        $this->cursor->moveToColumn($column);
    }

    /**
     * Move cursor up.
     */
    public function moveCursorUp(int $count = 1): void
    {
        if ($count > 0) {
            $this->noteOutOfBandWrite(true);
            $this->cursor->moveUp($count);
        }
    }

    /**
     * Move cursor down by specified number of lines.
     */
    public function moveCursorDown(int $count = 1): void
    {
        if ($count > 0) {
            $this->noteOutOfBandWrite(true);
            $this->cursor->moveDown($count);
        }
    }

    /**
     * Clear entire current line.
     */
    public function clearLine(): void
    {
        $this->noteOutOfBandWrite();
        $this->cursor->clearLine();
    }

    /**
     * Clear from cursor to end of line.
     */
    public function clearToEndOfLine(): void
    {
        $this->noteOutOfBandWrite();
        $this->cursor->clearLineAfter();
    }

    /**
     * Clear from cursor to end of screen.
     */
    public function clearToEndOfScreen(): void
    {
        $this->noteOutOfBandWrite();
        $this->cursor->clearOutput();
    }

    /**
     * Save cursor position.
     */
    public function saveCursor(): void
    {
        $this->cursor->savePosition();
    }

    /**
     * Restore cursor position.
     */
    public function restoreCursor(): void
    {
        $this->noteOutOfBandWrite(true);
        $this->cursor->restorePosition();
    }

    /**
     * Read a single key press from input.
     */
    public function readKey(): Key
    {
        return $this->input->readKey();
    }

    /**
     * Get terminal width in columns.
     */
    public function getWidth(): int
    {
        return $this->symfonyTerminal->getWidth();
    }

    /**
     * Get terminal height in rows.
     */
    public function getHeight(): int
    {
        return $this->symfonyTerminal->getHeight();
    }

    /**
     * Ring the terminal bell.
     */
    public function bell(): void
    {
        $this->write("\x07", false); // BEL character
    }

    /**
     * Enable raw mode for terminal input.
     *
     * Disables canonical mode, echo, and signal processing so we can
     * read characters one at a time and handle control characters.
     */
    public function enableRawMode(): bool
    {
        // Only works on Unix-like systems with stty
        if (\DIRECTORY_SEPARATOR === '\\') {
            return false;
        }

        $stty = \shell_exec('stty -g');
        if ($stty === null || $stty === false) {
            return false;
        }

        $this->originalSttyMode = \trim($stty);

        // Set raw mode: -icanon (disable canonical mode), -echo (disable echo)
        // -isig (disable signal chars like Ctrl-C), -ixon (disable Ctrl-S/Q)
        \shell_exec('stty -icanon -echo -isig -ixon');

        return true;
    }

    /**
     * Disable raw mode and restore original terminal settings.
     */
    public function disableRawMode(): void
    {
        if ($this->originalSttyMode !== null) {
            \shell_exec('stty '.\escapeshellarg($this->originalSttyMode));
            $this->originalSttyMode = null;
        }
    }

    /**
     * Enable bracketed paste mode.
     *
     * When enabled, pasted text is wrapped with special escape sequences
     * (\033[200~ / \033[201~) to distinguish it from typed content.
     */
    public function enableBracketedPaste(): void
    {
        if (!$this->bracketedPasteEnabled) {
            $this->write("\033[?2004h", false);
            $this->bracketedPasteEnabled = true;
        }
    }

    /**
     * Disable bracketed paste mode.
     */
    public function disableBracketedPaste(): void
    {
        if ($this->bracketedPasteEnabled) {
            $this->write("\033[?2004l", false);
            $this->bracketedPasteEnabled = false;
        }
    }

    /**
     * Check if bracketed paste mode is enabled.
     */
    public function isBracketedPasteEnabled(): bool
    {
        return $this->bracketedPasteEnabled;
    }

    /**
     * Switch to the alternate screen buffer, saving cursor position.
     *
     * Terminals expose one alternate buffer. Treat it as a mode bit rather
     * than a stack so repeated enable/disable calls are idempotent.
     */
    public function enableAltScreen(): void
    {
        if (!$this->altScreenEnabled) {
            $this->write("\033[?1049h", false);
            $this->altScreenEnabled = true;
            // Cursor row tracking is meaningless across an alt-screen switch.
            $this->invalidateFrame(true);
        }
    }

    /**
     * Return to the primary screen buffer, restoring the saved cursor.
     */
    public function disableAltScreen(): void
    {
        if ($this->altScreenEnabled) {
            $this->write("\033[?1049l", false);
            $this->altScreenEnabled = false;
            $this->invalidateFrame(true);
        }
    }

    /**
     * Check if alternate screen mode is active.
     */
    public function isAltScreenEnabled(): bool
    {
        return $this->altScreenEnabled;
    }

    /**
     * Enable button-and-wheel mouse reporting in SGR (1006) mode.
     *
     * Pager turns this on while it owns the screen so wheel events become
     * keys.
     */
    public function enableMouseReporting(): void
    {
        if (!$this->mouseReportingEnabled) {
            // 1000: button press/release events.
            // 1006: SGR extended format (the only one the parser handles).
            $this->write("\033[?1000h\033[?1006h", false);
            $this->mouseReportingEnabled = true;
        }
    }

    /**
     * Disable mouse reporting.
     */
    public function disableMouseReporting(): void
    {
        if ($this->mouseReportingEnabled) {
            $this->write("\033[?1006l\033[?1000l", false);
            $this->mouseReportingEnabled = false;
        }
    }

    /**
     * Check if mouse reporting is enabled.
     */
    public function isMouseReportingEnabled(): bool
    {
        return $this->mouseReportingEnabled;
    }

    /**
     * Flag the terminal as dirty when writing outside a frame render.
     */
    private function noteOutOfBandWrite(bool $movesRow = false): void
    {
        if ($this->frameRenderDepth === 0) {
            $this->dirty = true;
            if ($movesRow) {
                $this->cursorRowUnknown = true;
            }
        }
    }
}
