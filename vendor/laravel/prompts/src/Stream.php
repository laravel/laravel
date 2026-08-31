<?php

namespace Laravel\Prompts;

use Laravel\Prompts\Themes\Default\Concerns\InteractsWithStrings;

class Stream extends Prompt
{
    use InteractsWithStrings;

    protected int $minWidth = 0;

    protected string $message = '';

    /** @var array<int, string> */
    protected array $currentlyFading = [];

    protected int $maxWidth = 0;

    /** @var array<int, \Closure(string): string> */
    protected array $fadingOutColors = [];

    /**
     * Create a new Stream instance.
     */
    public function __construct()
    {
        $this->maxWidth = static::terminal()->cols() - 20;
        $this->hideCursor();
        $this->fadingOutColors = $this->fadeOut();
    }

    public function append(string $message): self
    {
        $this->currentlyFading[] = $message;

        while (count($this->currentlyFading) > count($this->fadingOutColors)) {
            $this->message .= array_shift($this->currentlyFading);
        }

        $this->render();

        return $this;
    }

    public function close(): void
    {
        try {
            while (count($this->currentlyFading) > 0) {
                $this->message .= array_shift($this->currentlyFading);
                $this->render();
                usleep(25_000);
            }
        } finally {
            $this->showCursor();
        }
    }

    /** @return array<int, string> */
    public function lines(): array
    {
        $toFadeIn = [];

        foreach ($this->currentlyFading as $index => $message) {
            $toFadeIn[] = $this->fadingOutColors[$index]($message);
        }

        $lines = explode(PHP_EOL, $this->message.implode('', $toFadeIn));
        $finalLines = [];

        foreach ($lines as $line) {
            $finalLines = array_merge(
                $finalLines,
                $this->ansiWordwrap($line, $this->maxWidth),
            );
        }

        return $finalLines;
    }

    public function prompt(): mixed
    {
        throw new \RuntimeException('Stream cannot be prompted');
    }

    /**
     * Get the value of the prompt.
     */
    public function value(): string
    {
        return $this->message.implode('', $this->currentlyFading);
    }

    /**
     * Get an array of closures that progressively fade text from full color to nearly invisible.
     *
     * @return array<int, \Closure(string): string>
     */
    protected function fadeOut(int $steps = 10): array
    {
        if (! static::terminal()->supportsTrueColor()) {
            return [
                fn (string $text) => $text,
                fn (string $text) => $this->dim($text),
            ];
        }

        $fg = static::terminal()->foregroundColor();
        $bg = static::terminal()->backgroundColor();

        return array_map(
            function (int $step) use ($fg, $bg, $steps) {
                $factor = 1 - ($step / $steps);
                $r = (int) ($bg[0] + ($fg[0] - $bg[0]) * $factor);
                $g = (int) ($bg[1] + ($fg[1] - $bg[1]) * $factor);
                $b = (int) ($bg[2] + ($fg[2] - $bg[2]) * $factor);

                return fn (string $text) => "\e[38;2;{$r};{$g};{$b}m{$text}\e[0m";
            },
            range(0, $steps - 1),
        );
    }
}
