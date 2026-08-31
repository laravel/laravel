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
 * Move cursor forward by PHP token.
 *
 * Provides semantic, token-based navigation that understands PHP syntax.
 * Stops at each token boundary (variables, operators, method names, etc.)
 * instead of generic word boundaries.
 */
class MoveTokenRightAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        $tokenStart = $buffer->findNextToken();
        $buffer->setCursor($tokenStart);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'move-token-right';
    }
}
