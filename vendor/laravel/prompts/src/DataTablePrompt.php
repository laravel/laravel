<?php

namespace Laravel\Prompts;

use Closure;
use Illuminate\Support\Collection;

class DataTablePrompt extends Prompt
{
    use Concerns\Scrolling;
    use Concerns\TypedValue;

    /**
     * The table headers.
     *
     * @var array<int, string|array<int, string>>
     */
    public array $headers;

    /**
     * The table rows.
     *
     * @var array<int|string, array<int, string>>
     */
    public array $rows;

    /**
     * The cached filtered rows.
     *
     * @var array<int|string, array<int, string>>|null
     */
    protected ?array $filteredCache = null;

    /**
     * The previous search query (for cache invalidation).
     */
    protected string $previousQuery = '';

    /**
     * Create a new DataTable instance.
     *
     * @param  array<int, string|array<int, string>>|Collection<int, string|array<int, string>>  $headers
     * @param  array<int|string, array<int, string>>|Collection<int|string, array<int, string>>|null  $rows
     *
     * @phpstan-param ($rows is null ? list<list<string>>|Collection<int, list<string>> : list<string|list<string>>|Collection<int, string|list<string>>) $headers
     */
    public function __construct(
        array|Collection $headers = [],
        array|Collection|null $rows = null,
        public int $scroll = 10,
        public string $label = '',
        public string $hint = '',
        public bool|string $required = false,
        public mixed $validate = null,
        public ?Closure $transform = null,
        public ?Closure $filter = null,
    ) {
        if ($rows === null) {
            $rows = $headers;
            $headers = [];
        }

        $this->headers = $headers instanceof Collection ? $headers->all() : $headers;
        $this->rows = $rows instanceof Collection ? $rows->all() : $rows;

        $this->initializeScrolling(0);

        $this->trackTypedValue(
            submit: false,
            ignore: fn ($key) => $this->state !== 'search',
        );

        $this->on('key', fn ($key) => match ($this->state) {
            'search' => $this->handleSearchKey($key),
            default => $this->handleBrowseKey($key),
        });
    }

    /**
     * Handle key presses in browse mode.
     */
    protected function handleBrowseKey(string $key): void
    {
        $total = count($this->filteredRows());

        match ($key) {
            Key::UP, Key::UP_ARROW, Key::CTRL_P => $this->highlightPrevious($total),
            Key::DOWN, Key::DOWN_ARROW, Key::CTRL_N => $this->highlightNext($total),
            Key::PAGE_UP => $this->highlight(max(0, $this->highlighted - $this->scroll)),
            Key::PAGE_DOWN => $this->highlight(min($total - 1, $this->highlighted + $this->scroll)),
            Key::oneOf([Key::HOME, Key::CTRL_A], $key) => $this->highlight(0),
            Key::oneOf([Key::END, Key::CTRL_E], $key) => $this->highlight(max(0, $total - 1)),
            Key::ENTER => $total > 0 ? $this->submit() : null,
            '/' => $this->enterSearch(),
            default => null,
        };
    }

    /**
     * Handle key presses in search mode.
     */
    protected function handleSearchKey(string $key): void
    {
        match ($key) {
            Key::ENTER => $this->exitSearch(),
            Key::ESCAPE => $this->cancelSearch(),
            default => $this->search(),
        };
    }

    /**
     * Enter search mode.
     */
    protected function enterSearch(): void
    {
        $this->state = 'search';
        $this->typedValue = '';
        $this->cursorPosition = 0;
    }

    /**
     * Exit search mode, keeping the filtered results.
     */
    protected function exitSearch(): void
    {
        $this->state = 'active';
        $this->highlighted = 0;
        $this->firstVisible = 0;
    }

    /**
     * Cancel search, clearing the query and showing all rows.
     */
    protected function cancelSearch(): void
    {
        $this->state = 'active';
        $this->typedValue = '';
        $this->cursorPosition = 0;
        $this->filteredCache = null;
        $this->previousQuery = '';
        $this->highlighted = 0;
        $this->firstVisible = 0;
    }

    /**
     * Handle typing in search mode.
     */
    protected function search(): void
    {
        $this->filteredCache = null;
        $this->highlighted = 0;
        $this->firstVisible = 0;
    }

    /**
     * Get the filtered rows based on the current search query.
     *
     * @return array<int|string, array<int, string>>
     */
    public function filteredRows(): array
    {
        if ($this->filteredCache !== null && $this->previousQuery === $this->typedValue) {
            return $this->filteredCache;
        }

        $this->previousQuery = $this->typedValue;

        if ($this->typedValue === '') {
            return $this->filteredCache = $this->rows;
        }

        if ($this->filter !== null) {
            return $this->filteredCache = array_filter(
                $this->rows,
                fn ($row) => ($this->filter)($row, $this->typedValue),
            );
        }

        return $this->filteredCache = array_filter(
            $this->rows,
            fn ($row) => str_contains(
                mb_strtolower(implode(' ', $row)),
                mb_strtolower($this->typedValue),
            ),
        );
    }

    /**
     * The currently visible rows.
     *
     * @return array<int|string, array<int, string>>
     */
    public function visible(): array
    {
        return array_slice($this->filteredRows(), $this->firstVisible, $this->scroll, preserve_keys: true);
    }

    /**
     * Get the current search query.
     */
    public function searchValue(): string
    {
        return $this->typedValue;
    }

    /**
     * Get the search query with a virtual cursor.
     */
    public function searchWithCursor(int $maxWidth): string
    {
        if ($this->typedValue === '') {
            return $this->dim($this->addCursor('', 0, $maxWidth));
        }

        return $this->addCursor($this->typedValue, $this->cursorPosition, $maxWidth);
    }

    /**
     * Get the value of the prompt.
     */
    public function value(): mixed
    {
        if ($this->highlighted === null) {
            return null;
        }

        $filtered = $this->filteredRows();
        $keys = array_keys($filtered);

        if (! isset($keys[$this->highlighted])) {
            return null;
        }

        return $keys[$this->highlighted];
    }

    /**
     * Get the selected row for display purposes.
     *
     * @return array<int, string>|null
     */
    public function selectedRow(): ?array
    {
        if ($this->highlighted === null) {
            return null;
        }

        $filtered = $this->filteredRows();
        $keys = array_keys($filtered);

        if (! isset($keys[$this->highlighted])) {
            return null;
        }

        return $filtered[$keys[$this->highlighted]];
    }
}
