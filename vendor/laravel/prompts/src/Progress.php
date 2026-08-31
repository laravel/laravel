<?php

namespace Laravel\Prompts;

use Closure;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * @template TSteps of iterable<mixed>|int
 */
class Progress extends Prompt
{
    /**
     * The current progress bar item count.
     */
    public int $progress = 0;

    /**
     * The total number of steps.
     */
    public int $total = 0;

    /**
     * The original value of pcntl_async_signals
     */
    protected bool $originalAsync;

    /**
     * Create a new ProgressBar instance.
     *
     * @param  TSteps  $steps
     */
    public function __construct(public string $label, public iterable|int $steps, public string $hint = '')
    {
        /** @phpstan-ignore assign.propertyType (PHPStan doesn't parse that we convert from iterable to int in the below match) */
        $this->total = match (true) {
            is_int($this->steps) => $this->steps,
            is_countable($this->steps) => count($this->steps),
            is_iterable($this->steps) => iterator_count($this->steps),
            /** @phpstan-ignore match.unreachable (Technically we shouldn't be able to reach the default as should be int|countable|iterable ) */
            default => throw new InvalidArgumentException('Unable to count steps.'),
        };

        if ($this->total === 0) {
            throw new InvalidArgumentException('Progress bar must have at least one item.');
        }
    }

    /**
     * Map over the steps while rendering the progress bar.
     *
     * @template TReturn
     *
     * @param  Closure((TSteps is int ? int : value-of<TSteps>), $this): TReturn  $callback
     * @return array<TReturn>
     *
     * @throws Throwable
     */
    public function map(Closure $callback): array
    {
        $this->start();

        $result = [];

        try {
            if (is_int($this->steps)) {
                for ($i = 0; $i < $this->steps; $i++) {
                    $result[] = $callback($i, $this);
                    $this->advance();
                }
            } else {
                foreach ($this->steps as $step) {
                    $result[] = $callback($step, $this);
                    $this->advance();
                }
            }
        } catch (Throwable $e) {
            $this->state = 'error';
            $this->render();
            $this->restoreCursor();
            $this->resetSignals();

            throw $e;
        }

        if ($this->hint !== '') {
            // Just pause for one moment to show the final hint
            // so it doesn't look like it was skipped
            usleep(250_000);
        }

        $this->finish();

        return $result;
    }

    /**
     * Start the progress bar.
     */
    public function start(): void
    {
        $this->capturePreviousNewLines();

        if (function_exists('pcntl_signal')) {
            $this->originalAsync = pcntl_async_signals(true);
            pcntl_signal(SIGINT, function () {
                $this->state = 'cancel';
                $this->render();
                exit();
            });
        }

        $this->hideCursor();
        $this->render();
        $this->state = 'active';
    }

    /**
     * Advance the progress bar.
     */
    public function advance(int $step = 1): void
    {
        $this->progress += $step;

        if ($this->progress > $this->total) {
            $this->progress = $this->total;
        }

        $this->render();
    }

    /**
     * Finish the progress bar.
     */
    public function finish(): void
    {
        $this->state = 'submit';
        $this->render();
        $this->restoreCursor();
        $this->resetSignals();
    }

    /**
     * Force the progress bar to re-render.
     */
    public function render(): void
    {
        parent::render();
    }

    /**
     * Update the label.
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Update the hint.
     */
    public function hint(string $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    /**
     * Get the completion percentage.
     */
    public function percentage(): int|float
    {
        return $this->progress / $this->total;
    }

    /**
     * Disable prompting for input.
     *
     * @throws RuntimeException
     */
    public function prompt(): never
    {
        throw new RuntimeException('Progress Bar cannot be prompted.');
    }

    /**
     * Get the value of the prompt.
     */
    public function value(): bool
    {
        return true;
    }

    /**
     * Reset the signal handling.
     */
    protected function resetSignals(): void
    {
        if (isset($this->originalAsync)) {
            pcntl_async_signals($this->originalAsync);
            pcntl_signal(SIGINT, SIG_DFL);
        }
    }

    /**
     * Restore the cursor.
     */
    public function __destruct()
    {
        $this->restoreCursor();
    }
}
