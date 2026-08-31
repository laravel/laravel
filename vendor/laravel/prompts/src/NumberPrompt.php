<?php

namespace Laravel\Prompts;

use Closure;
use RuntimeException;

class NumberPrompt extends Prompt
{
    use Concerns\TypedValue;

    /**
     * Create a new NumberPrompt instance.
     */
    public function __construct(
        public string $label,
        public string $placeholder = '',
        public string $default = '',
        public bool|string $required = false,
        public mixed $validate = null,
        public string $hint = '',
        public ?Closure $transform = null,
        public ?int $min = null,
        public ?int $max = null,
        public ?int $step = null,
    ) {
        $this->trackTypedValue($default);

        $this->step = max(1, $this->step ?? 1);
        $this->min ??= PHP_INT_MIN;
        $this->max ??= PHP_INT_MAX;

        $originalValidate = $this->validate;

        $this->validate = $this->wrapValidation($this->validate);

        $this->on('key', function (string $key) {
            match ($key) {
                Key::UP, Key::UP_ARROW => $this->increaseValue(),
                Key::DOWN, Key::DOWN_ARROW => $this->decreaseValue(),
                default => null,
            };
        });
    }

    protected function wrapValidation(mixed $validate): callable
    {
        return function ($value) use ($validate) {
            if ($value !== '' && ! is_numeric($value)) {
                return 'Must be a number';
            }

            if (is_numeric($value)) {
                if ($value < $this->min) {
                    return 'Must be at least '.$this->min;
                }

                if ($value > $this->max) {
                    return 'Must be less than '.$this->max;
                }
            }

            if (! $validate && ! isset(static::$validateUsing)) {
                return null;
            }

            return match (true) {
                is_callable($validate) => ($validate)($value),
                isset(static::$validateUsing) => (static::$validateUsing)($this),
                default => throw new RuntimeException('The validation logic is missing.'),
            };
        };
    }

    /**
     * Increase the value of the prompt by the step.
     */
    protected function increaseValue(): void
    {
        if ($this->typedValue === '') {
            $this->typedValue = (string) ($this->min === PHP_INT_MIN ? 1 : $this->min);
            $this->cursorPosition++;

            return;
        }

        if (is_numeric($this->typedValue)) {
            $previousValueLength = mb_strlen($this->typedValue);

            $this->typedValue = (string) min($this->max, (int) $this->typedValue + $this->step);

            if (mb_strlen($this->typedValue) > $previousValueLength) {
                $this->cursorPosition++;
            }
        }
    }

    /**
     * Decrease the value of the prompt by the step.
     */
    protected function decreaseValue(): void
    {
        if ($this->typedValue === '') {
            $this->typedValue = (string) ($this->max === PHP_INT_MAX ? 0 : $this->max);
            $this->cursorPosition++;

            return;
        }

        if (is_numeric($this->typedValue)) {
            $previousValueLength = mb_strlen($this->typedValue);

            $this->typedValue = (string) max($this->min, (int) $this->typedValue - $this->step);

            if (mb_strlen($this->typedValue) < $previousValueLength) {
                $this->cursorPosition--;
            }
        }
    }

    public function value(): int|string
    {
        if (is_numeric($this->typedValue)) {
            return (int) $this->typedValue;
        }

        return $this->typedValue;
    }

    /**
     * Get the entered value with a virtual cursor.
     */
    public function valueWithCursor(int $maxWidth): string
    {
        if ($this->value() === '') {
            return $this->dim($this->addCursor($this->placeholder, 0, $maxWidth));
        }

        return $this->addCursor((string) $this->value(), $this->cursorPosition, $maxWidth);
    }
}
