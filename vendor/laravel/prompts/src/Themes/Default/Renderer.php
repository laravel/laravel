<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\Concerns\Colors;
use Laravel\Prompts\Concerns\Truncation;
use Laravel\Prompts\Prompt;

abstract class Renderer
{
    use Colors;
    use Truncation;

    /**
     * The output to be rendered.
     */
    protected string $output = '';

    /**
     * Create a new renderer instance.
     */
    public function __construct(protected Prompt $prompt)
    {
        //
    }

    /**
     * Render a line of output.
     */
    protected function line(string $message): self
    {
        $this->output .= $message.PHP_EOL;

        return $this;
    }

    /**
     * Render a new line.
     */
    protected function newLine(int $count = 1): self
    {
        $this->output .= str_repeat(PHP_EOL, $count);

        return $this;
    }

    /**
     * Render a warning message.
     */
    protected function warning(string $message): self
    {
        return $this->line($this->yellow("  ⚠ {$message}"));
    }

    /**
     * Render an error message.
     */
    protected function error(string $message): self
    {
        return $this->line($this->red("  ⚠ {$message}"));
    }

    /**
     * Render an hint message.
     */
    protected function hint(string $message): self
    {
        if ($message === '') {
            return $this;
        }

        $message = $this->truncate($message, $this->prompt->terminal()->cols() - 6);

        return $this->line($this->gray("  {$message}"));
    }

    /**
     * Apply the callback if the given "value" is truthy.
     *
     * @return $this
     */
    protected function when(mixed $value, callable $callback, ?callable $default = null): self
    {
        if ($value) {
            $callback($this);
        } elseif ($default) {
            $default($this);
        }

        return $this;
    }

    /**
     * Render the output with a blank line above and below.
     */
    public function __toString()
    {
        return str_repeat(PHP_EOL, max(2 - $this->prompt->newLinesWritten(), 0))
            .$this->output
            .(in_array($this->prompt->state, ['submit', 'cancel']) ? PHP_EOL : '');
    }
}
