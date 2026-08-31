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

/**
 * Closure-valued key binding map.
 *
 * Lighter sibling of KeyBindings. Instead of dispatching to ActionInterface
 * (which assumes a Buffer + Readline context), each binding is a
 * `callable(Key)`. Useful anywhere a small TUI surface needs its own input
 * dispatch without the full Action machinery: Pager today, future
 * interactive widgets later.
 *
 * The variadic bind() makes it easy to attach the same closure to several
 * key patterns, which is the common case (j/Down/Ctrl-N all do the same
 * thing).
 */
class ClosureKeyBindings
{
    /** @var callable[] Keyed by key pattern */
    private array $bindings = [];

    /**
     * Bind one or more key patterns to the same action.
     *
     * @param callable $action      Action to invoke; receives the Key
     * @param string   ...$patterns One or more key patterns (e.g. 'char:j', 'escape:[B')
     */
    public function bind(callable $action, string ...$patterns): void
    {
        foreach ($patterns as $pattern) {
            $this->bindings[$pattern] = $action;
        }
    }

    /**
     * Get the action bound to a key, or null if none.
     */
    public function get(Key $key): ?callable
    {
        return $this->bindings[(string) $key] ?? null;
    }
}
