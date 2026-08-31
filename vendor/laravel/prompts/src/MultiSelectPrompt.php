<?php

namespace Laravel\Prompts;

use Closure;
use Illuminate\Support\Collection;

class MultiSelectPrompt extends Prompt
{
    use Concerns\HasInfo;
    use Concerns\Scrolling;

    /**
     * The options for the multi-select prompt.
     *
     * @var array<int|string, string>
     */
    public array $options;

    /**
     * The default values the multi-select prompt.
     *
     * @var array<int|string>
     */
    public array $default;

    /**
     * The selected values.
     *
     * @var array<int|string>
     */
    protected array $values = [];

    /**
     * Create a new MultiSelectPrompt instance.
     *
     * @param  array<int|string, string>|Collection<int|string, string>  $options
     * @param  array<int|string>|Collection<int, int|string>  $default
     */
    public function __construct(
        public string $label,
        array|Collection $options,
        array|Collection $default = [],
        public int $scroll = 5,
        public bool|string $required = false,
        public mixed $validate = null,
        public string $hint = '',
        public ?Closure $transform = null,
        public string|Closure $info = '',
    ) {
        $this->options = $options instanceof Collection ? $options->all() : $options;
        $this->default = $default instanceof Collection ? $default->all() : $default;
        $this->values = $this->default;

        $this->initializeScrolling(0);

        $this->on('key', fn ($key) => match ($key) {
            Key::UP, Key::UP_ARROW, Key::LEFT, Key::LEFT_ARROW, Key::SHIFT_TAB, Key::CTRL_P, Key::CTRL_B, 'k', 'h' => $this->highlightPrevious(count($this->options)),
            Key::DOWN, Key::DOWN_ARROW, Key::RIGHT, Key::RIGHT_ARROW, Key::TAB, Key::CTRL_N, Key::CTRL_F, 'j', 'l' => $this->highlightNext(count($this->options)),
            Key::oneOf(Key::HOME, $key) => $this->highlight(0),
            Key::oneOf(Key::END, $key) => $this->highlight(count($this->options) - 1),
            Key::SPACE => $this->toggleHighlighted(),
            Key::CTRL_A => $this->toggleAll(),
            Key::ENTER => $this->submit(),
            default => null,
        });
    }

    /**
     * Get the value of the highlighted option.
     */
    public function highlightedValue(): int|string|null
    {
        if ($this->highlighted === null) {
            return null;
        }

        if (array_is_list($this->options)) {
            return $this->options[$this->highlighted] ?? null;
        }

        return array_keys($this->options)[$this->highlighted] ?? null;
    }

    /**
     * Get the selected values.
     *
     * @return array<int|string>
     */
    public function value(): array
    {
        return array_values($this->values);
    }

    /**
     * Get the selected labels.
     *
     * @return array<string>
     */
    public function labels(): array
    {
        if (array_is_list($this->options)) {
            return array_map(fn ($value) => (string) $value, $this->values);
        }

        return array_values(array_intersect_key($this->options, array_flip($this->values)));
    }

    /**
     * The currently visible options.
     *
     * @return array<int|string, string>
     */
    public function visible(): array
    {
        return array_slice($this->options, $this->firstVisible, $this->scroll, preserve_keys: true);
    }

    /**
     * Check whether the value is currently highlighted.
     */
    public function isHighlighted(string $value): bool
    {
        if (array_is_list($this->options)) {
            return $this->options[$this->highlighted] === $value;
        }

        return array_keys($this->options)[$this->highlighted] === $value;
    }

    /**
     * Check whether the value is currently selected.
     */
    public function isSelected(string $value): bool
    {
        return in_array($value, $this->values);
    }

    /**
     * Toggle all options.
     */
    protected function toggleAll(): void
    {
        if (count($this->values) === count($this->options)) {
            $this->values = [];
        } else {
            $this->values = array_is_list($this->options)
                ? array_values($this->options)
                : array_keys($this->options);
        }
    }

    /**
     * Toggle the highlighted entry.
     */
    protected function toggleHighlighted(): void
    {
        $value = array_is_list($this->options)
            ? $this->options[$this->highlighted]
            : array_keys($this->options)[$this->highlighted];

        if (in_array($value, $this->values)) {
            $this->values = array_filter($this->values, fn ($v) => $v !== $value);
        } else {
            $this->values[] = $value;
        }
    }
}
