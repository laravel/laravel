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

use Psy\Readline\Interactive\Input\Buffer;
use Psy\Readline\Interactive\Input\Key;

/**
 * A pushable input/render layer for interactive readline.
 *
 * When a mode is at the top of Readline's mode stack it intercepts every
 * key and owns rendering for the duration of its activity. Modes pop off
 * the stack when their handleKey() returns a value other than true.
 */
interface ReadlineMode
{
    /**
     * Handle a single key while this mode is the top of the stack.
     *
     * @return bool|null
     *                   true:  key consumed, stay in mode
     *                   false: key consumed, pop mode after handling
     *                   null:  not consumed by this mode; pop and replay the key
     */
    public function handleKey(Key $key, Buffer $buffer): ?bool;

    /**
     * Render this mode's view of the terminal.
     *
     * The current buffer is passed so modes whose visual depends on input
     * state (e.g. a completion menu) can reuse Readline's display chain.
     * Modes that ignore the buffer (e.g. reverse-i-search, which replaces
     * the input area entirely) are free to discard the argument.
     */
    public function display(Buffer $buffer): void;

    /**
     * Cleanup called by Readline immediately after this mode is popped.
     *
     * Restore any rendering side effects (overlay, etc.) and reset
     * per-activation state so a subsequent push starts fresh.
     */
    public function onExit(): void;
}
