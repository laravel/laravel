<?php

namespace Laravel\Prompts;

use Closure;
use Illuminate\Support\Collection;
use Laravel\Prompts\Elements\ElementContract;

if (! function_exists('\Laravel\Prompts\text')) {
    /**
     * Prompt the user for text input.
     */
    function text(
        string $label,
        string $placeholder = '',
        string $default = '',
        bool|string $required = false,
        mixed $validate = null,
        string $hint = '',
        ?Closure $transform = null,
    ): string {
        return (new TextPrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\autocomplete')) {
    /**
     * Prompt the user for text input with auto-completion.
     *
     * @param  array<string>|Collection<int, string>|Closure(string): (array<string>|Collection<int, string>)  $options
     */
    function autocomplete(
        string $label,
        array|Collection|Closure $options = [],
        string $placeholder = '',
        string $default = '',
        bool|string $required = false,
        mixed $validate = null,
        string $hint = '',
        ?Closure $transform = null,
    ): string {
        return (new AutoCompletePrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\number')) {
    /**
     * Prompt the user for number input.
     */
    function number(string $label, string $placeholder = '', string $default = '', bool|string $required = false, mixed $validate = null, string $hint = '', ?int $min = null, ?int $max = null, ?int $step = null): int|string
    {
        return (new NumberPrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\textarea')) {
    /**
     * Prompt the user for multiline text input.
     */
    function textarea(
        string $label,
        string $placeholder = '',
        string $default = '',
        bool|string $required = false,
        mixed $validate = null,
        string $hint = '',
        int $rows = 5,
        ?Closure $transform = null,
    ): string {
        return (new TextareaPrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\password')) {
    /**
     * Prompt the user for input, hiding the value.
     */
    function password(
        string $label,
        string $placeholder = '',
        bool|string $required = false,
        mixed $validate = null,
        string $hint = '',
        ?Closure $transform = null,
    ): string {
        return (new PasswordPrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\select')) {
    /**
     * Prompt the user to select an option.
     *
     * @param  array<int|string, string>|Collection<int|string, string>  $options
     * @param  true|string  $required
     */
    function select(
        string $label,
        array|Collection $options,
        int|string|null $default = null,
        int $scroll = 5,
        mixed $validate = null,
        string $hint = '',
        bool|string $required = true,
        ?Closure $transform = null,
        string|Closure $info = '',
    ): int|string {
        return (new SelectPrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\multiselect')) {
    /**
     * Prompt the user to select multiple options.
     *
     * @param  array<int|string, string>|Collection<int|string, string>  $options
     * @param  array<int|string>|Collection<int, int|string>  $default
     * @return array<int|string>
     */
    function multiselect(
        string $label,
        array|Collection $options,
        array|Collection $default = [],
        int $scroll = 5,
        bool|string $required = false,
        mixed $validate = null,
        string $hint = 'Use the space bar to select options.',
        ?Closure $transform = null,
        string|Closure $info = '',
    ): array {
        return (new MultiSelectPrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\confirm')) {
    /**
     * Prompt the user to confirm an action.
     */
    function confirm(
        string $label,
        bool $default = true,
        string $yes = 'Yes',
        string $no = 'No',
        bool|string $required = false,
        mixed $validate = null,
        string $hint = '',
        ?Closure $transform = null,
    ): bool {
        return (new ConfirmPrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\pause')) {
    /**
     * Prompt the user to continue or cancel after pausing.
     */
    function pause(string $message = 'Press enter to continue...'): bool
    {
        return (new PausePrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\clear')) {
    /**
     * Clear the terminal.
     */
    function clear(): void
    {
        (new Clear)->display();
    }
}

if (! function_exists('\Laravel\Prompts\suggest')) {
    /**
     * Prompt the user for text input with auto-completion.
     *
     * @param  array<string>|Collection<int, string>|Closure(string): array<string>  $options
     */
    function suggest(
        string $label,
        array|Collection|Closure $options,
        string $placeholder = '',
        string $default = '',
        int $scroll = 5,
        bool|string $required = false,
        mixed $validate = null,
        string $hint = '',
        ?Closure $transform = null,
        string|Closure $info = '',
    ): string {
        return (new SuggestPrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\search')) {
    /**
     * Allow the user to search for an option.
     *
     * @param  Closure(string): array<int|string, string>  $options
     * @param  true|string  $required
     */
    function search(
        string $label,
        Closure $options,
        string $placeholder = '',
        int $scroll = 5,
        mixed $validate = null,
        string $hint = '',
        bool|string $required = true,
        ?Closure $transform = null,
        string|Closure $info = '',
    ): int|string {
        return (new SearchPrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\multisearch')) {
    /**
     * Allow the user to search for multiple option.
     *
     * @param  Closure(string): array<int|string, string>  $options
     * @return array<int|string>
     */
    function multisearch(
        string $label,
        Closure $options,
        string $placeholder = '',
        int $scroll = 5,
        bool|string $required = false,
        mixed $validate = null,
        string $hint = 'Use the space bar to select options.',
        ?Closure $transform = null,
        string|Closure $info = '',
    ): array {
        return (new MultiSearchPrompt(...get_defined_vars()))->prompt();
    }
}

if (! function_exists('\Laravel\Prompts\spin')) {
    /**
     * Render a spinner while the given callback is executing.
     *
     * @template TReturn of mixed
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    function spin(Closure $callback, string $message = ''): mixed
    {
        return (new Spinner($message))->spin($callback);
    }
}

if (! function_exists('\Laravel\Prompts\note')) {
    /**
     * Display a note.
     */
    function note(string $message, ?string $type = null): void
    {
        (new Note($message, $type))->display();
    }
}

if (! function_exists('\Laravel\Prompts\callout')) {
    /**
     * Display a callout.
     *
     * @param  string|list<string|ElementContract>  $content
     */
    function callout(string $label, string|array $content, ?string $type = null, string $info = ''): void
    {
        (new Callout($label, $content, $type, $info))->display();
    }
}

if (! function_exists('\Laravel\Prompts\error')) {
    /**
     * Display an error.
     */
    function error(string $message): void
    {
        (new Note($message, 'error'))->display();
    }
}

if (! function_exists('\Laravel\Prompts\warning')) {
    /**
     * Display a warning.
     */
    function warning(string $message): void
    {
        (new Note($message, 'warning'))->display();
    }
}

if (! function_exists('\Laravel\Prompts\alert')) {
    /**
     * Display an alert.
     */
    function alert(string $message): void
    {
        (new Note($message, 'alert'))->display();
    }
}

if (! function_exists('\Laravel\Prompts\info')) {
    /**
     * Display an informational message.
     */
    function info(string $message): void
    {
        (new Note($message, 'info'))->display();
    }
}

if (! function_exists('\Laravel\Prompts\intro')) {
    /**
     * Display an introduction.
     */
    function intro(string $message): void
    {
        (new Note($message, 'intro'))->display();
    }
}

if (! function_exists('\Laravel\Prompts\outro')) {
    /**
     * Display a closing message.
     */
    function outro(string $message): void
    {
        (new Note($message, 'outro'))->display();
    }
}

if (! function_exists('\Laravel\Prompts\notify')) {
    /**
     * Send a notification to the user. (macOS and Linux only)
     *
     * The icon option is Linux only. The subtitle and sound options are macOS only.
     *
     * @param  string  $subtitle  macOS only
     * @param  string  $sound  macOS only
     * @param  string  $icon  Linux only
     */
    function notify(string $title, string $body = '', string $subtitle = '', string $sound = '', string $icon = ''): void
    {
        (new NotifyPrompt(...get_defined_vars()))->display();
    }
}

if (! function_exists('\Laravel\Prompts\table')) {
    /**
     * Display a table.
     *
     * @param  array<int, string|array<int, string>>|Collection<int, string|array<int, string>>  $headers
     * @param  array<int, array<int, string>>|Collection<int, array<int, string>>  $rows
     */
    function table(array|Collection $headers = [], array|Collection|null $rows = null): void
    {
        (new Table($headers, $rows))->display();
    }
}

if (! function_exists('\Laravel\Prompts\grid')) {
    /**
     * Display a grid.
     *
     * @param  array<int, string>|Collection<int, string>  $items
     */
    function grid(array|Collection $items = [], ?int $maxWidth = null): void
    {
        (new Grid($items, $maxWidth))->display();
    }
}

if (! function_exists('\Laravel\Prompts\progress')) {
    /**
     * Display a progress bar.
     *
     * @template TSteps of iterable<mixed>|int
     * @template TReturn
     *
     * @param  TSteps  $steps
     * @param  ?Closure((TSteps is int ? int : value-of<TSteps>), Progress<TSteps>): TReturn  $callback
     * @return ($callback is null ? Progress<TSteps> : array<TReturn>)
     */
    function progress(
        string $label,
        iterable|int $steps,
        ?Closure $callback = null,
        string $hint = '',
    ): array|Progress {
        $progress = new Progress($label, $steps, $hint);

        if ($callback !== null) {
            return $progress->map($callback);
        }

        return $progress;
    }
}

if (! function_exists('\Laravel\Prompts\form')) {
    function form(): FormBuilder
    {
        return new FormBuilder;
    }
}

if (! function_exists('\Laravel\Prompts\title')) {
    /**
     * Update the title of the terminal.
     */
    function title(string $title): void
    {
        (new Title($title))->display();
    }
}

if (! function_exists('\Laravel\Prompts\stream')) {
    /**
     * Display a stream of text.
     */
    function stream(): Stream
    {
        return new Stream;
    }
}

if (! function_exists('\Laravel\Prompts\task')) {
    /**
     * Display a task with a spinner and live output.
     *
     * @template TReturn of mixed
     *
     * @param  Closure(Support\Logger): TReturn  $callback
     * @return TReturn
     */
    function task(string $label, Closure $callback, ?int $limit = null, bool $keepSummary = false, ?string $subLabel = null): mixed
    {
        return (new Task($label, $limit ?? 10, $keepSummary, $subLabel))->run($callback);
    }
}

if (! function_exists('\Laravel\Prompts\datatable')) {
    /**
     * Display an interactive data table.
     *
     * @param  array<int, string|array<int, string>>|Collection<int, string|array<int, string>>  $headers
     * @param  array<int|string, array<int, string>>|Collection<int|string, array<int, string>>|null  $rows
     */
    function datatable(
        array|Collection $headers = [],
        array|Collection|null $rows = null,
        int $scroll = 10,
        string $label = '',
        string $hint = '',
        bool|string $required = false,
        mixed $validate = null,
        ?Closure $transform = null,
        ?Closure $filter = null,
    ): mixed {
        return (new DataTablePrompt(
            headers: $headers,
            rows: $rows,
            scroll: $scroll,
            label: $label,
            hint: $hint,
            required: $required,
            validate: $validate,
            transform: $transform,
            filter: $filter,
        ))->prompt();
    }
}
