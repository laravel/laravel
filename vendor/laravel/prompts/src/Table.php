<?php

namespace Laravel\Prompts;

use Illuminate\Support\Collection;

class Table extends Prompt
{
    /**
     * The table headers.
     *
     * @var array<int, string|array<int, string>>
     */
    public array $headers;

    /**
     * The table rows.
     *
     * @var array<int, array<int, string>>
     */
    public array $rows;

    /**
     * Create a new Table instance.
     *
     * @param  array<int, string|array<int, string>>|Collection<int, string|array<int, string>>  $headers
     * @param  array<int, array<int, string>>|Collection<int, array<int, string>>  $rows
     *
     * @phpstan-param ($rows is null ? list<list<string>>|Collection<int, list<string>> : list<string|list<string>>|Collection<int, string|list<string>>) $headers
     */
    public function __construct(array|Collection $headers = [], array|Collection|null $rows = null)
    {
        if ($rows === null) {
            $rows = $headers;
            $headers = [];
        }

        $this->headers = $headers instanceof Collection ? $headers->all() : $headers;
        $this->rows = $rows instanceof Collection ? $rows->all() : $rows;
    }

    /**
     * Display the table.
     */
    public function display(): void
    {
        $this->prompt();
    }

    /**
     * Display the table.
     */
    public function prompt(): bool
    {
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
