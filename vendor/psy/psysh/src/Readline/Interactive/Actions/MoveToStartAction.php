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
 * Smart Home: toggle between first non-whitespace character and column zero (Ctrl-A, Home).
 */
class MoveToStartAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        $line = $buffer->getCurrentLineText();
        $cursorInLine = $buffer->getCursorPositionInLine();
        $lineStart = $buffer->getCursor() - $cursorInLine;

        // For whitespace-only lines, treat the whole line as having no indent
        $firstNonWhitespace = \strspn($line, " \t");
        if ($firstNonWhitespace === \strlen($line)) {
            $firstNonWhitespace = 0;
        }

        $targetColumn = $cursorInLine === $firstNonWhitespace ? 0 : $firstNonWhitespace;
        $buffer->setCursor($lineStart + $targetColumn);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'move-to-start';
    }
}
