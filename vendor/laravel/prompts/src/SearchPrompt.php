<?php

namespace Laravel\Prompts;

use Closure;
use InvalidArgumentException;

class SearchPrompt extends Prompt
{
    use Concerns\HasInfo;
    use Concerns\Scrolling;
    use Concerns\Truncation;
    use Concerns\TypedValue;

    /**
     * The cached matches.
     *
     * @var array<int|string, string>|null
     */
    protected ?array $matches = null;

    /**
     * Create a new SearchPrompt instance.
     *
     * @param  Closure(string): array<int|string, string>  $options
     */
    public function __construct(
        public string $label,
        public Closure $options,
        public string $placeholder = '',
        public int $scroll = 5,
        public mixed $validate = null,
        public string $hint = '',
        public bool|string $required = true,
        public ?Closure $transform = null,
        public string|Closure $info = '',
    ) {
        if ($this->required === false) {
            throw new InvalidArgumentException('Argument [required] must be true or a string.');
        }

        $this->trackTypedValue(submit: false, ignore: fn ($key) => Key::oneOf([Key::HOME, Key::END, Key::CTRL_A, Key::CTRL_E], $key) && $this->highlighted !== null);

        $this->initializeScrolling(null);

        $this->on('key', fn ($key) => match ($key) {
            Key::UP, Key::UP_ARROW, Key::SHIFT_TAB, Key::CTRL_P => $this->highlightPrevious(count($this->matches), true),
            Key::DOWN, Key::DOWN_ARROW, Key::TAB, Key::CTRL_N => $this->highlightNext(count($this->matches), true),
            Key::oneOf([Key::HOME, Key::CTRL_A], $key) => $this->highlighted !== null ? $this->highlight(0) : null,
            Key::oneOf([Key::END, Key::CTRL_E], $key) => $this->highlighted !== null ? $this->highlight(count($this->matches()) - 1) : null,
            Key::ENTER => $this->highlighted !== null ? $this->submit() : $this->search(),
            Key::oneOf([Key::LEFT, Key::LEFT_ARROW, Key::RIGHT, Key::RIGHT_ARROW, Key::CTRL_B, Key::CTRL_F], $key) => $this->highlighted = null,
            default => $this->search(),
        });
    }

    /**
     * Get the value of the highlighted option.
     */
    public function highlightedValue(): int|string|null
    {
        return $this->value();
    }

    /**
     * Perform the search.
     */
    protected function search(): void
    {
        $this->state = 'searching';
        $this->highlighted = null;
        $this->render();
        $this->matches = null;
        $this->firstVisible = 0;
        $this->state = 'active';
    }

    /**
     * Get the entered value with a virtual cursor.
     */
    public function valueWithCursor(int $maxWidth): string
    {
        if ($this->highlighted !== null) {
            return $this->typedValue === ''
                ? $this->dim($this->truncate($this->placeholder, $maxWidth))
                : $this->truncate($this->typedValue, $maxWidth);
        }

        if ($this->typedValue === '') {
            return $this->dim($this->addCursor($this->placeholder, 0, $maxWidth));
        }

        return $this->addCursor($this->typedValue, $this->cursorPosition, $maxWidth);
    }

    /**
     * Get options that match the input.
     *
     * @return array<string>
     */
    public function matches(): array
    {
        if (is_array($this->matches)) {
            return $this->matches;
        }

        return $this->matches = ($this->options)($this->typedValue);
    }

    /**
     * The currently visible matches.
     *
     * @return array<string>
     */
    public function visible(): array
    {
        return array_slice($this->matches(), $this->firstVisible, $this->scroll, preserve_keys: true);
    }

    /**
     * Get the current search query.
     */
    public function searchValue(): string
    {
        return $this->typedValue;
    }

    /**
     * Get the selected value.
     */
    public function value(): int|string|null
    {
        if ($this->matches === null || $this->highlighted === null) {
            return null;
        }

        return array_is_list($this->matches)
            ? $this->matches[$this->highlighted]
            : array_keys($this->matches)[$this->highlighted];
    }

    /**
     * Get the selected label.
     */
    public function label(): ?string
    {
        return $this->matches[array_keys($this->matches)[$this->highlighted]] ?? null;
    }
}
