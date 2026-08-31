<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Input;

use Psy\Readline\Interactive\Terminal;

/**
 * Input event queue with pushback/replay support.
 */
class InputQueue
{
    private Terminal $terminal;

    /** @var Key[] */
    private array $bufferedEvents = [];

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    /**
     * Read the next key event, replaying buffered events first.
     */
    public function read(): Key
    {
        if (!empty($this->bufferedEvents)) {
            return \array_shift($this->bufferedEvents);
        }

        return Key::normalized($this->terminal->readKey());
    }

    /**
     * Push an event back so it is replayed on the next read.
     */
    public function replay(Key $key): void
    {
        \array_unshift($this->bufferedEvents, Key::normalized($key));
    }
}
