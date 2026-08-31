<?php

namespace Laravel\Prompts;

use Closure;
use Illuminate\Support\Collection;
use Laravel\Prompts\Exceptions\FormRevertedException;

class FormBuilder
{
    /**
     * Each step that should be executed.
     *
     * @var array<int, FormStep>
     */
    protected array $steps = [];

    /**
     * The responses provided by each step.
     *
     * @var array<mixed>
     */
    protected array $responses = [];

    /**
     * Add a new step.
     */
    public function add(Closure $step, ?string $name = null, bool $ignoreWhenReverting = false): self
    {
        $this->steps[] = new FormStep($step, true, $name, $ignoreWhenReverting);

        return $this;
    }

    /**
     * Add a new conditional step.
     */
    public function addIf(Closure|bool $condition, Closure $step, ?string $name = null, bool $ignoreWhenReverting = false): self
    {
        $this->steps[] = new FormStep($step, $condition, $name, $ignoreWhenReverting);

        return $this;
    }

    /**
     * Run all of the given steps.
     *
     * @return array<mixed>
     */
    public function submit(): array
    {
        $index = 0;
        $wasReverted = false;

        while ($index < count($this->steps)) {
            $step = $this->steps[$index];

            if ($wasReverted && $index > 0 && $step->shouldIgnoreWhenReverting($this->responses)) {
                $index--;

                continue;
            }

            $wasReverted = false;

            $index > 0
                ? Prompt::revertUsing(function () use (&$wasReverted) {
                    $wasReverted = true;
                }) : Prompt::preventReverting();

            try {
                $this->responses[$step->name ?? $index] = $step->run(
                    $this->responses,
                    $this->responses[$step->name ?? $index] ?? null,
                );
            } catch (FormRevertedException) {
                $wasReverted = true;
            }

            $wasReverted ? $index-- : $index++;
        }

        Prompt::preventReverting();

        return $this->responses;
    }

    /**
     * Prompt the user for text input.
     */
    public function text(string $label, string $placeholder = '', string $default = '', bool|string $required = false, mixed $validate = null, string $hint = '', ?string $name = null, ?Closure $transform = null): self
    {
        return $this->runPrompt(text(...), get_defined_vars());
    }

    /**
     * Prompt the user for multiline text input.
     */
    public function textarea(string $label, string $placeholder = '', string $default = '', bool|string $required = false, ?Closure $validate = null, string $hint = '', int $rows = 5, ?string $name = null, ?Closure $transform = null): self
    {
        return $this->runPrompt(textarea(...), get_defined_vars());
    }

    /**
     * Prompt the user for input, hiding the value.
     */
    public function password(string $label, string $placeholder = '', bool|string $required = false, mixed $validate = null, string $hint = '', ?string $name = null, ?Closure $transform = null): self
    {
        return $this->runPrompt(password(...), get_defined_vars());
    }

    /**
     * Prompt the user to select an option.
     *
     * @param  array<int|string, string>|Collection<int|string, string>  $options
     * @param  true|string  $required
     */
    public function select(string $label, array|Collection $options, int|string|null $default = null, int $scroll = 5, mixed $validate = null, string $hint = '', bool|string $required = true, ?string $name = null, ?Closure $transform = null): self
    {
        return $this->runPrompt(select(...), get_defined_vars());
    }

    /**
     * Prompt the user to select multiple options.
     *
     * @param  array<int|string, string>|Collection<int|string, string>  $options
     * @param  array<int|string>|Collection<int, int|string>  $default
     */
    public function multiselect(string $label, array|Collection $options, array|Collection $default = [], int $scroll = 5, bool|string $required = false, mixed $validate = null, string $hint = 'Use the space bar to select options.', ?string $name = null, ?Closure $transform = null): self
    {
        return $this->runPrompt(multiselect(...), get_defined_vars());
    }

    /**
     * Prompt the user to confirm an action.
     */
    public function confirm(string $label, bool $default = true, string $yes = 'Yes', string $no = 'No', bool|string $required = false, mixed $validate = null, string $hint = '', ?string $name = null, ?Closure $transform = null): self
    {
        return $this->runPrompt(confirm(...), get_defined_vars());
    }

    /**
     * Prompt the user to continue or cancel after pausing.
     */
    public function pause(string $message = 'Press enter to continue...', ?string $name = null): self
    {
        return $this->runPrompt(pause(...), get_defined_vars());
    }

    /**
     * Prompt the user for text input with auto-completion.
     *
     * @param  array<string>|Collection<int, string>|Closure(string): array<string>  $options
     */
    public function suggest(string $label, array|Collection|Closure $options, string $placeholder = '', string $default = '', int $scroll = 5, bool|string $required = false, mixed $validate = null, string $hint = '', ?string $name = null, ?Closure $transform = null): self
    {
        return $this->runPrompt(suggest(...), get_defined_vars());
    }

    /**
     * Allow the user to search for an option.
     *
     * @param  Closure(string): array<int|string, string>  $options
     * @param  true|string  $required
     */
    public function search(string $label, Closure $options, string $placeholder = '', int $scroll = 5, mixed $validate = null, string $hint = '', bool|string $required = true, ?string $name = null, ?Closure $transform = null): self
    {
        return $this->runPrompt(search(...), get_defined_vars());
    }

    /**
     * Allow the user to search for multiple option.
     *
     * @param  Closure(string): array<int|string, string>  $options
     */
    public function multisearch(string $label, Closure $options, string $placeholder = '', int $scroll = 5, bool|string $required = false, mixed $validate = null, string $hint = 'Use the space bar to select options.', ?string $name = null, ?Closure $transform = null): self
    {
        return $this->runPrompt(multisearch(...), get_defined_vars());
    }

    /**
     * Render a spinner while the given callback is executing.
     *
     * @param  Closure(): mixed  $callback
     */
    public function spin(Closure $callback, string $message = '', ?string $name = null): self
    {
        return $this->runPrompt(spin(...), get_defined_vars(), true);
    }

    /**
     * Display a note.
     */
    public function note(string $message, ?string $type = null, ?string $name = null): self
    {
        return $this->runPrompt(note(...), get_defined_vars(), true);
    }

    /**
     * Display an error.
     */
    public function error(string $message, ?string $name = null): self
    {
        return $this->runPrompt(error(...), get_defined_vars(), true);
    }

    /**
     * Display a warning.
     */
    public function warning(string $message, ?string $name = null): self
    {
        return $this->runPrompt(warning(...), get_defined_vars(), true);
    }

    /**
     * Display an alert.
     */
    public function alert(string $message, ?string $name = null): self
    {
        return $this->runPrompt(alert(...), get_defined_vars(), true);
    }

    /**
     * Display an informational message.
     */
    public function info(string $message, ?string $name = null): self
    {
        return $this->runPrompt(info(...), get_defined_vars(), true);
    }

    /**
     * Display an introduction.
     */
    public function intro(string $message, ?string $name = null): self
    {
        return $this->runPrompt(intro(...), get_defined_vars(), true);
    }

    /**
     * Display a closing message.
     */
    public function outro(string $message, ?string $name = null): self
    {
        return $this->runPrompt(outro(...), get_defined_vars(), true);
    }

    /**
     * Display a table.
     *
     * @param  array<int, string|array<int, string>>|Collection<int, string|array<int, string>>  $headers
     * @param  array<int, array<int, string>>|Collection<int, array<int, string>>  $rows
     */
    public function table(array|Collection $headers = [], array|Collection|null $rows = null, ?string $name = null): self
    {
        return $this->runPrompt(table(...), get_defined_vars(), true);
    }

    /**
     * Display a progress bar.
     *
     * @template TSteps of iterable<mixed>|int
     * @template TReturn
     *
     * @param  TSteps  $steps
     * @param  ?Closure((TSteps is int ? int : value-of<TSteps>), Progress<TSteps>): TReturn  $callback
     */
    public function progress(string $label, iterable|int $steps, ?Closure $callback = null, string $hint = '', ?string $name = null): self
    {
        return $this->runPrompt(progress(...), get_defined_vars(), true);
    }

    /**
     * Execute the given prompt passing the given arguments.
     *
     * @param  array<mixed>  $arguments
     */
    protected function runPrompt(callable $prompt, array $arguments, bool $ignoreWhenReverting = false): self
    {
        return $this->add(function (array $responses, mixed $previousResponse) use ($prompt, $arguments) {
            unset($arguments['name']);

            if (array_key_exists('default', $arguments) && $previousResponse !== null) {
                $arguments['default'] = $previousResponse;
            }

            return $prompt(...$arguments);
        }, name: $arguments['name'], ignoreWhenReverting: $ignoreWhenReverting);
    }
}
