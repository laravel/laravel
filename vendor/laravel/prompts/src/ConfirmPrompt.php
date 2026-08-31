<?php

namespace Laravel\Prompts;

use Closure;

class ConfirmPrompt extends Prompt
{
    /**
     * Whether the prompt has been confirmed.
     */
    public bool $confirmed;

    /**
     * Create a new ConfirmPrompt instance.
     */
    public function __construct(
        public string $label,
        public bool $default = true,
        public string $yes = 'Yes',
        public string $no = 'No',
        public bool|string $required = false,
        public mixed $validate = null,
        public string $hint = '',
        public ?Closure $transform = null,
    ) {
        $this->confirmed = $default;

        $this->on('key', fn ($key) => match ($key) {
            'y' => $this->confirmed = true,
            'n' => $this->confirmed = false,
            Key::TAB, Key::UP, Key::UP_ARROW, Key::DOWN, Key::DOWN_ARROW, Key::LEFT, Key::LEFT_ARROW, Key::RIGHT, Key::RIGHT_ARROW, Key::CTRL_P, Key::CTRL_F, Key::CTRL_N, Key::CTRL_B, 'h', 'j', 'k', 'l' => $this->confirmed = ! $this->confirmed,
            Key::ENTER => $this->submit(),
            default => null,
        });
    }

    /**
     * Get the value of the prompt.
     */
    public function value(): bool
    {
        return $this->confirmed;
    }

    /**
     * Get the label of the selected option.
     */
    public function label(): string
    {
        return $this->confirmed ? $this->yes : $this->no;
    }
}
