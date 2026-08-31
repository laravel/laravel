<?php

namespace Laravel\Prompts;

use Closure;
use Illuminate\Support\Collection;

class AutoCompletePrompt extends Prompt
{
    use Concerns\TypedValue;

    /**
     * The options for the autocomplete prompt.
     *
     * @var array<string>|Closure(string): (array<string>|Collection<int, string>)
     */
    public array|Closure $options;

    protected string $match = '';

    protected int $highlighted = 0;

    /**
     * @var array<string>|null
     */
    protected ?array $matches = null;

    /**
     * Create a new AutoCompletePrompt instance.
     *
     * @param  array<string>|Collection<int, string>|Closure(string): (array<string>|Collection<int, string>)  $options
     */
    public function __construct(
        public string $label,
        array|Collection|Closure $options = [],
        public string $placeholder = '',
        public string $default = '',
        public bool|string $required = false,
        public mixed $validate = null,
        public string $hint = '',
        public ?Closure $transform = null,
    ) {
        $this->options = $options instanceof Collection ? $options->all() : $options;

        $this->on('key', function ($key) {
            if (in_array($key, [Key::UP, Key::UP_ARROW])) {
                $matches = $this->matches();

                if (count($matches) > 0) {
                    $this->highlighted = ($this->highlighted - 1 + count($matches)) % count($matches);
                }

                return;
            }

            if (in_array($key, [Key::DOWN, Key::DOWN_ARROW])) {
                $matches = $this->matches();

                if (count($matches) > 0) {
                    $this->highlighted = ($this->highlighted + 1) % count($matches);
                }

                return;
            }

            if ($key === Key::TAB && $this->cursorPosition >= mb_strlen($this->typedValue)) {
                $match = $this->getMatch();

                if ($match !== '' && mb_strlen($match) > mb_strlen($this->value())) {
                    // Ghost text is showing — accept it
                    $this->typedValue = $match;
                    $this->cursorPosition = mb_strlen($match);
                } else {
                    // No ghost text — request suggestions
                    $this->matches = null;
                    $this->highlighted = 0;
                }

                return;
            }

            if (in_array($key, [Key::RIGHT, Key::RIGHT_ARROW]) && $this->cursorPosition >= mb_strlen($this->typedValue)) {
                $match = $this->getMatch();

                if ($match !== '') {
                    $this->typedValue = $match;
                    $this->cursorPosition = mb_strlen($match);
                }

                return;
            }

            // Any other key resets the highlight and match cache
            $this->highlighted = 0;
            $this->matches = null;
        });

        $this->trackTypedValue(
            $default,
            ignore: fn ($key) => in_array($key, [Key::UP, Key::UP_ARROW, Key::DOWN, Key::DOWN_ARROW]),
        );
    }

    /**
     * Get the entered value with a virtual cursor.
     */
    public function valueWithCursor(int $maxWidth): string
    {
        if ($this->value() === '') {
            return $this->dim($this->addCursor($this->placeholder, 0, $maxWidth));
        }

        $this->match = $this->getMatch();

        $ghostText = '';

        if ($this->match !== '' && mb_strlen($this->match) > mb_strlen($this->value())) {
            $ghostText = mb_substr($this->match, mb_strlen($this->value()));
        }

        // When cursor is at the end and there's ghost text, make the first
        // ghost character the inverted cursor so it flows naturally.
        if ($ghostText !== '' && $this->cursorPosition >= mb_strlen($this->value())) {
            $cursorChar = mb_substr($ghostText, 0, 1);
            $remainingGhost = mb_substr($ghostText, 1);

            return $this->value()
                .$this->inverse($cursorChar)
                .$this->dim($remainingGhost);
        }

        return $this->addCursor(
            $this->value(),
            $this->cursorPosition,
            $maxWidth
        ).$this->dim($ghostText);
    }

    /**
     * Get the current matches for the typed value.
     *
     * @return array<string>
     */
    public function matches(): array
    {
        if (is_array($this->matches)) {
            return $this->matches;
        }

        if ($this->options instanceof Closure) {
            $options = ($this->options)($this->value());

            return $this->matches = array_values($options instanceof Collection ? $options->all() : $options);
        }

        return $this->matches = array_values(array_filter(
            $this->options,
            fn ($option) => str_starts_with(strtolower($option), strtolower($this->value())),
        ));
    }

    /**
     * Get the current match.
     */
    protected function getMatch(): string
    {
        return $this->matches()[$this->highlighted] ?? '';
    }
}
