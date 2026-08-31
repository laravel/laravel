<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Actions;

use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Readline;
use Psy\Readline\Interactive\Terminal;

/**
 * Clear the current input buffer (Ctrl-C).
 *
 *  - In single-line mode, clears the current line.
 *  - In multi-line mode, exits multi-line and clears entire buffer.
 *  - On empty line, beeps (or could show hint: "Use Ctrl-D to exit").
 */
class ClearBufferAction implements ActionInterface
{
    private static int $lastEmptyCtrlCTime = 0;
    private const HINT_TIMEOUT = 2; // seconds

    /**
     * Reset the Ctrl-C timer (for testing).
     */
    public static function resetTimer(): void
    {
        self::$lastEmptyCtrlCTime = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if ($readline->isMultilineMode()) {
            $buffer->clear();

            return true;
        }

        if ($buffer->isEmpty()) {
            $now = \time();
            $timeSinceLastCtrlC = $now - self::$lastEmptyCtrlCTime;

            // If Ctrl-C pressed twice within timeout, show exit hint
            if (self::$lastEmptyCtrlCTime > 0 && $timeSinceLastCtrlC <= self::HINT_TIMEOUT) {
                $terminal->write("\r");
                $terminal->clearToEndOfLine();
                $terminal->writeFormatted('<whisper>(Press Ctrl-D to exit, or type \'exit\')</whisper>');
                $terminal->write("\n");

                self::$lastEmptyCtrlCTime = 0; // Reset to avoid showing hint repeatedly
            } else {
                $terminal->bell();
                self::$lastEmptyCtrlCTime = $now;
            }

            return true;
        }

        $buffer->clear();
        self::$lastEmptyCtrlCTime = 0;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'clear-buffer';
    }
}
