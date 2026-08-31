<?php

namespace Laravel\Prompts;

use Closure;
use Laravel\Prompts\Exceptions\FormRevertedException;
use Laravel\Prompts\Output\ConsoleOutput;
use Laravel\Prompts\Support\Result;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

abstract class Prompt
{
    use Concerns\Colors;
    use Concerns\Cursor;
    use Concerns\Erase;
    use Concerns\Events;
    use Concerns\FakesInputOutput;
    use Concerns\Fallback;
    use Concerns\Interactivity;
    use Concerns\Themes;

    /**
     * The current state of the prompt.
     */
    public string $state = 'initial';

    /**
     * The error message from the validator.
     */
    public string $error = '';

    /**
     * The cancel message displayed when this prompt is cancelled.
     */
    public string $cancelMessage = 'Cancelled.';

    /**
     * The previously rendered frame.
     */
    protected string $prevFrame = '';

    /**
     * How many new lines were written by the last output.
     */
    protected int $newLinesWritten = 1;

    /**
     * Whether user input is required.
     */
    public bool|string $required;

    /**
     * The transformation callback.
     */
    public ?Closure $transform = null;

    /**
     * The validator callback or rules.
     */
    public mixed $validate;

    /**
     * The cancellation callback.
     */
    protected static ?Closure $cancelUsing;

    /**
     * Indicates if the prompt has been validated.
     */
    protected bool $validated = false;

    /**
     * The custom validation callback.
     */
    protected static ?Closure $validateUsing;

    /**
     * The revert handler from the StepBuilder.
     */
    protected static ?Closure $revertUsing = null;

    /**
     * The output instance.
     */
    protected static OutputInterface $output;

    /**
     * The terminal instance.
     */
    protected static Terminal $terminal;

    /**
     * Get the value of the prompt.
     */
    abstract public function value(): mixed;

    /**
     * Render the prompt and listen for input.
     */
    public function prompt(): mixed
    {
        try {
            $this->capturePreviousNewLines();

            if (static::shouldFallback()) {
                return $this->fallback();
            }

            static::$interactive ??= stream_isatty(STDIN);

            if (! static::$interactive) {
                return $this->default();
            }

            $this->checkEnvironment();

            try {
                static::terminal()->setTty('-icanon -isig -echo');
            } catch (Throwable $e) {
                static::output()->writeln("<comment>{$e->getMessage()}</comment>");
                static::fallbackWhen(true);

                return $this->fallback();
            }

            $this->hideCursor();
            $this->render();

            $result = $this->runLoop(function (string $key): ?Result {
                $continue = $this->handleKeyPress($key);

                $this->render();

                if ($continue === false || $key === Key::CTRL_C) {
                    if ($key === Key::CTRL_C) {
                        if (isset(static::$cancelUsing)) {
                            return Result::from((static::$cancelUsing)());
                        } else {
                            static::terminal()->exit();
                        }
                    }

                    if ($key === Key::CTRL_U && self::$revertUsing) {
                        throw new FormRevertedException;
                    }

                    return Result::from($this->transformedValue());
                }

                // Continue looping.
                return null;
            });

            return $result;
        } finally {
            $this->clearListeners();
        }
    }

    /**
     * Implementation of the prompt looping mechanism.
     *
     * @param  callable(string $key): ?Result  $callable
     */
    public function runLoop(callable $callable): mixed
    {
        while (($key = static::terminal()->read()) !== null) {
            /**
             * If $key is an empty string, Terminal::read
             * has failed. We can continue to the next
             * iteration of the loop, and try again.
             */
            if ($key === '') {
                continue;
            }

            $result = $callable($key);

            if ($result instanceof Result) {
                return $result->value;
            }
        }
    }

    /**
     * Register a callback to be invoked when a user cancels a prompt.
     */
    public static function cancelUsing(?Closure $callback): void
    {
        static::$cancelUsing = $callback;
    }

    /**
     * How many new lines were written by the last output.
     */
    public function newLinesWritten(): int
    {
        return $this->newLinesWritten;
    }

    /**
     * Capture the number of new lines written by the last output.
     */
    protected function capturePreviousNewLines(): void
    {
        $this->newLinesWritten = method_exists(static::output(), 'newLinesWritten')
            ? static::output()->newLinesWritten()
            : 1;
    }

    /**
     * Set the output instance.
     */
    public static function setOutput(OutputInterface $output): void
    {
        self::$output = $output;
    }

    /**
     * Get the current output instance.
     */
    protected static function output(): OutputInterface
    {
        return self::$output ??= new ConsoleOutput;
    }

    /**
     * Write output directly, bypassing newline capture.
     */
    protected static function writeDirectly(string $message): void
    {
        match (true) {
            method_exists(static::output(), 'writeDirectly') => static::output()->writeDirectly($message),
            method_exists(static::output(), 'getOutput') => static::output()->getOutput()->write($message),
            default => static::output()->write($message),
        };
    }

    /**
     * Get the terminal instance.
     */
    public static function terminal(): Terminal
    {
        return static::$terminal ??= new Terminal;
    }

    /**
     * Set the custom validation callback.
     */
    public static function validateUsing(Closure $callback): void
    {
        static::$validateUsing = $callback;
    }

    /**
     * Revert the prompt using the given callback.
     *
     * @internal
     */
    public static function revertUsing(Closure $callback): void
    {
        static::$revertUsing = $callback;
    }

    /**
     * Clear any previous revert callback.
     *
     * @internal
     */
    public static function preventReverting(): void
    {
        static::$revertUsing = null;
    }

    /**
     * Render the prompt.
     */
    protected function render(): void
    {
        $this->terminal()->initDimensions();

        $frame = $this->renderTheme();

        if ($frame === $this->prevFrame) {
            return;
        }

        if ($this->state === 'initial') {
            static::output()->write($frame);

            $this->state = 'active';
            $this->prevFrame = $frame;

            return;
        }

        $terminalHeight = $this->terminal()->lines();
        $previousFrameHeight = count(explode(PHP_EOL, $this->prevFrame));
        $renderableLines = array_slice(explode(PHP_EOL, $frame), abs(min(0, $terminalHeight - $previousFrameHeight)));

        $this->moveCursorToColumn(1);
        $this->moveCursorUp(min($terminalHeight, $previousFrameHeight) - 1);
        $this->eraseDown();
        $this->output()->write(implode(PHP_EOL, $renderableLines));

        $this->prevFrame = $frame;
    }

    /**
     * Submit the prompt.
     */
    protected function submit(): void
    {
        $this->validate($this->transformedValue());

        if ($this->state !== 'error') {
            $this->state = 'submit';
        }
    }

    /**
     * Handle a key press and determine whether to continue.
     */
    private function handleKeyPress(string $key): bool
    {
        if ($this->state === 'error') {
            $this->state = 'active';
        }

        $this->emit('key', $key);

        if ($this->state === 'submit') {
            return false;
        }

        if ($key === Key::CTRL_U) {
            if (! self::$revertUsing) {
                $this->state = 'error';
                $this->error = 'This cannot be reverted.';

                return true;
            }

            $this->state = 'cancel';
            $this->cancelMessage = 'Reverted.';

            call_user_func(self::$revertUsing);

            return false;
        }

        if ($key === Key::CTRL_C) {
            $this->state = 'cancel';

            return false;
        }

        if ($this->validated) {
            $this->validate($this->transformedValue());
        }

        return true;
    }

    /**
     * Transform the input.
     */
    private function transform(mixed $value): mixed
    {
        if (is_null($this->transform)) {
            return $value;
        }

        return call_user_func($this->transform, $value);
    }

    /**
     * Get the transformed value of the prompt.
     */
    protected function transformedValue(): mixed
    {
        return $this->transform($this->value());
    }

    /**
     * Validate the input.
     */
    private function validate(mixed $value): void
    {
        $this->validated = true;

        if ($this->required !== false && $this->isInvalidWhenRequired($value)) {
            $this->state = 'error';
            $this->error = is_string($this->required) && strlen($this->required) > 0 ? $this->required : 'Required.';

            return;
        }

        if (! isset($this->validate) && ! isset(static::$validateUsing)) {
            return;
        }

        $error = match (true) {
            is_callable($this->validate) => ($this->validate)($value),
            isset(static::$validateUsing) => (static::$validateUsing)($this),
            default => throw new RuntimeException('The validation logic is missing.'),
        };

        if (! is_string($error) && ! is_null($error)) {
            throw new RuntimeException('The validator must return a string or null.');
        }

        if (is_string($error) && strlen($error) > 0) {
            $this->state = 'error';
            $this->error = $error;
        }
    }

    /**
     * Determine whether the given value is invalid when the prompt is required.
     */
    protected function isInvalidWhenRequired(mixed $value): bool
    {
        return $value === '' || $value === [] || $value === false || $value === null;
    }

    /**
     * Check whether the environment can support the prompt.
     */
    private function checkEnvironment(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            throw new RuntimeException('Prompts is not currently supported on Windows. Please use WSL or configure a fallback.');
        }
    }

    /**
     * Restore the cursor and terminal state.
     */
    public function __destruct()
    {
        $this->restoreCursor();

        static::terminal()->restoreTty();
    }
}
