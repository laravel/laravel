<?php

namespace Laravel\Prompts\Concerns;

use Laravel\Prompts\Exceptions\NonInteractiveValidationException;

trait Interactivity
{
    /**
     * Whether to render the prompt interactively.
     */
    protected static bool $interactive;

    /**
     * Set interactive mode.
     */
    public static function interactive(bool $interactive = true): void
    {
        static::$interactive = $interactive;
    }

    /**
     * Return the default value if it passes validation.
     */
    protected function default(): mixed
    {
        $default = $this->value();

        $this->validate($default);

        if ($this->state === 'error') {
            throw new NonInteractiveValidationException($this->error);
        }

        return $default;
    }
}
