<?php

namespace Laravel\Prompts;

class Clear extends Prompt
{
    /**
     * Clear the terminal.
     */
    public function prompt(): bool
    {
        // Fill the previous newline count so subsequent prompts won't add padding.
        static::output()->write(PHP_EOL.PHP_EOL);

        $this->writeDirectly($this->renderTheme());

        return true;
    }

    /**
     * Clear the terminal.
     */
    public function display(): void
    {
        $this->prompt();
    }

    /**
     * Get the value of the prompt.
     */
    public function value(): bool
    {
        return true;
    }
}
