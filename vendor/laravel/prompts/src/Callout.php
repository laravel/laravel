<?php

namespace Laravel\Prompts;

use Laravel\Prompts\Elements\ElementContract;

class Callout extends Prompt
{
    /**
     * Create a new Callout instance.
     *
     * @param  string|array<int, string|ElementContract>  $content
     */
    public function __construct(
        public string $label,
        public string|array $content,
        public ?string $type = null,
        public string $info = '',
    ) {
        //
    }

    /**
     * Display the note.
     */
    public function display(): void
    {
        $this->prompt();
    }

    /**
     * Display the callout.
     */
    public function prompt(): bool
    {
        $this->capturePreviousNewLines();

        if (static::shouldFallback()) {
            return $this->fallback();
        }

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
