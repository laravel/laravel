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
 * Dedent leading indentation to the previous tab stop.
 *
 * Returns false when the cursor is not in leading indentation.
 */
class DedentLeadingIndentationAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if (!$readline->isMultilineMode()) {
            return false;
        }

        $line = $buffer->getCurrentLineText();
        $indentLength = \strspn($line, " \t");
        if ($indentLength === 0) {
            return false;
        }

        $cursorInLine = $buffer->getCursorPositionInLine();
        if ($cursorInLine > $indentLength) {
            return false;
        }

        // Cursor is at line start: forward-delete leading indentation.
        if ($cursorInLine === 0) {
            if ($line[0] === "\t") {
                return $buffer->deleteForward(1);
            }

            $leadingSpaces = \strspn($line, ' ');

            return $buffer->deleteForward($buffer->spacesToPreviousTabStop($leadingSpaces));
        }

        // Cursor is within leading whitespace: backward-delete to previous tab stop.
        if ($line[$cursorInLine - 1] === "\t") {
            return $buffer->deleteBackward(1);
        }

        $beforeCursor = \substr($line, 0, $cursorInLine);
        $trailingSpaces = \strlen($beforeCursor) - \strlen(\rtrim($beforeCursor, ' '));

        return $buffer->deleteBackward($buffer->spacesToPreviousTabStop($trailingSpaces));
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'dedent-leading-indentation';
    }
}
