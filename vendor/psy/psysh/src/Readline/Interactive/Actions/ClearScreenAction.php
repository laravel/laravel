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
 * Clear the screen, keeping the current input buffer (Ctrl-L).
 */
class ClearScreenAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Buffer $buffer, Terminal $terminal, Readline $readline): bool
    {
        $readline->clearPreviousLines();

        // Move cursor to top-left and clear entire screen
        $terminal->write("\033[H\033[2J");
        $terminal->invalidateFrame(true);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'clear-screen';
    }
}
