<?php

namespace Laravel\Prompts;

use Illuminate\Support\Collection;

class Grid extends Prompt
{
    /**
     * The grid items.
     *
     * @var array<int, string>
     */
    public array $items;

    /**
     * The maximum width of the grid.
     */
    public int $maxWidth;

    /**
     * Create a new Grid instance.
     *
     * @param  array<int, string>|Collection<int, string>  $items
     */
    public function __construct(array|Collection $items = [], ?int $maxWidth = null)
    {
        $this->items = $items instanceof Collection ? $items->all() : $items;
        $this->maxWidth = $maxWidth ?? static::terminal()->cols() ?: 80;
    }

    /**
     * Display the grid.
     */
    public function display(): void
    {
        $this->prompt();
    }

    /**
     * Display the grid.
     */
    public function prompt(): bool
    {
        if ($this->items === []) {
            return true;
        }

        $this->capturePreviousNewLines();

        $this->state = 'submit';

        static::output()->write($this->renderTheme());

        return true;
    }

    /**
     * Get the value of the prompt.
     */
    public function value(): bool
    {
        return true;
    }
}
