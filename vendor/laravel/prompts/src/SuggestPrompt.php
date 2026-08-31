<?php

namespace Laravel\Prompts;

use Closure;
use Illuminate\Support\Collection;

class SuggestPrompt extends Prompt
{
    use Concerns\HasInfo;
    use Concerns\Scrolling;
    use Concerns\Truncation;
    use Concerns\TypedValue;

    /**
     * The options for the suggest prompt.
     *
     * @var array<string>|Closure(string): (array<string>|Collection<int, string>)
     */
    public array|Closure $options;

    /**
     * The cache of matches.
     *
     * @var array<string>|null
     */
    protected ?array $matches = null;

    /**
     * Create a new SuggestPrompt instance.
     *
     * @param  array<string>|Collection<int, string>|Closure(string): (array<string>|Collection<int, string>)  $options
     */
    public function __construct(
        public string $label,
        array|Collection|Closure $options,
        public string $placeholder = '',
        public string $default = '',
        public int $scroll = 5,
        public bool|string $required = false,
        public mixed $validate = null,
        public string $hint = '',
        public ?Closure $transform = null,
        public string|Closure $info = '',
    ) {
        $this->options = $options instanceof Collection ? $options->all() : $options;

        $this->initializeScrolling(null);

        $this->on('key', fn ($key) => match ($key) {
            Key::UP, Key::UP_ARROW, Key::SHIFT_TAB, Key::CTRL_P => $this->highlightPrevious(count($this->matches()), true),
            Key::DOWN, Key::DOWN_ARROW, Key::TAB, Key::CTRL_N => $this->highlightNext(count($this->matches()), true),
            Key::oneOf([Key::HOME, Key::CTRL_A], $key) => $this->highlighted !== null ? $this->highlight(0) : null,
            Key::oneOf([Key::END, Key::CTRL_E], $key) => $this->highlighted !== null ? $this->highlight(count($this->matches()) - 1) : null,
            Key::ENTER => $this->selectHighlighted(),
            Key::oneOf([Key::LEFT, Key::LEFT_ARROW, Key::RIGHT, Key::RIGHT_ARROW, Key::CTRL_B, Key::CTRL_F], $key) => $this->highlighted = null,
            default => (function () {
                $this->highlighted = null;
                $this->matches = null;
                $this->firstVisible = 0;
            })(),
        });

        $this->trackTypedValue($default, ignore: fn ($key) => Key::oneOf([Key::HOME, Key::END, Key::CTRL_A, Key::CTRL_E], $key) && $this->highlighted !== null);
    }

    /**
     * Get the value of the highlighted option.
     */
    public function highlightedValue(): ?string
    {
        if ($this->highlighted === null) {
            return null;
        }

        return $this->matches()[$this->highlighted] ?? null;
    }

    /**
     * Get the entered value with a virtual cursor.
     */
    public function valueWithCursor(int $maxWidth): string
    {
        if ($this->highlighted !== null) {
            return $this->value() === ''
                ? $this->dim($this->truncate($this->placeholder, $maxWidth))
                : $this->truncate($this->value(), $maxWidth);
        }

        if ($this->value() === '') {
            return $this->dim($this->addCursor($this->placeholder, 0, $maxWidth));
        }

        return $this->addCursor($this->value(), $this->cursorPosition, $maxWidth);
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

        if ($this->options instanceof Closure) {
            $matches = ($this->options)($this->value());

            return $this->matches = array_values($matches instanceof Collection ? $matches->all() : $matches);
        }

        return $this->matches = array_values(array_filter($this->options, function ($option) {
            return str_starts_with(strtolower($option), strtolower($this->value()));
        }));
    }

    /**
     * The current visible matches.
     *
     * @return array<string>
     */
    public function visible(): array
    {
        return array_slice($this->matches(), $this->firstVisible, $this->scroll, preserve_keys: true);
    }

    /**
     * Select the highlighted entry.
     */
    protected function selectHighlighted(): void
    {
        if ($this->highlighted === null) {
            return;
        }

        $this->typedValue = $this->matches()[$this->highlighted];
    }
}
