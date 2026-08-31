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
 * Kill text from cursor to end of line (Ctrl-K).
 */
class KillLineAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        $buffer->deleteToEnd();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'kill-line';
    }
}
