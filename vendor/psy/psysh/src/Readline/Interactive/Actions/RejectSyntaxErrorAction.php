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
 * Reject submit when the buffer has an unrecoverable syntax error.
 *
 * Sets the input frame to error mode and rings the bell so the user can
 * fix the error in-place, rather than inserting a newline or submitting.
 */
class RejectSyntaxErrorAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        if (!$buffer->hasUnrecoverableSyntaxError()) {
            return false;
        }

        $line = $buffer->getText();
        if ($readline->isCommand($line) && !$readline->isInOpenStringOrComment($line)) {
            return false;
        }

        // Don't reject when there are truly unclosed brackets before
        // cursor (more opens than closes). In that case, the user is
        // still typing inside a bracket pair and the unclosed-brackets
        // action should handle continuation instead.
        if ($this->hasOpenBracketsBeforeCursor($buffer)) {
            return false;
        }

        $readline->setInputFrameError(true);
        $terminal->bell();

        return true;
    }

    /**
     * Check if text before cursor has more opening brackets than closing.
     */
    private function hasOpenBracketsBeforeCursor(Buffer $buffer): bool
    {
        $text = $buffer->getBeforeCursor();
        if (\trim($text) === '') {
            return false;
        }

        $tokens = @\token_get_all('<?php '.$text);
        $depth = 0;
        $pairs = ['(' => 1, ')' => -1, '[' => 1, ']' => -1, '{' => 1, '}' => -1];

        foreach ($tokens as $token) {
            if (\is_string($token) && isset($pairs[$token])) {
                $depth += $pairs[$token];
            }
        }

        return $depth > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'reject-syntax-error';
    }
}
