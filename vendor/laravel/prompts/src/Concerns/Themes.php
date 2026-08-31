<?php

namespace Laravel\Prompts\Concerns;

use InvalidArgumentException;
use Laravel\Prompts\AutoCompletePrompt;
use Laravel\Prompts\Callout;
use Laravel\Prompts\Clear;
use Laravel\Prompts\ConfirmPrompt;
use Laravel\Prompts\DataTablePrompt;
use Laravel\Prompts\Grid;
use Laravel\Prompts\MultiSearchPrompt;
use Laravel\Prompts\MultiSelectPrompt;
use Laravel\Prompts\Note;
use Laravel\Prompts\NumberPrompt;
use Laravel\Prompts\PasswordPrompt;
use Laravel\Prompts\PausePrompt;
use Laravel\Prompts\Progress;
use Laravel\Prompts\Prompt;
use Laravel\Prompts\SearchPrompt;
use Laravel\Prompts\SelectPrompt;
use Laravel\Prompts\Spinner;
use Laravel\Prompts\Stream;
use Laravel\Prompts\SuggestPrompt;
use Laravel\Prompts\Table;
use Laravel\Prompts\Task;
use Laravel\Prompts\TextareaPrompt;
use Laravel\Prompts\TextPrompt;
use Laravel\Prompts\Themes\Default\AutoCompletePromptRenderer;
use Laravel\Prompts\Themes\Default\CalloutRenderer;
use Laravel\Prompts\Themes\Default\ClearRenderer;
use Laravel\Prompts\Themes\Default\ConfirmPromptRenderer;
use Laravel\Prompts\Themes\Default\DataTableRenderer;
use Laravel\Prompts\Themes\Default\GridRenderer;
use Laravel\Prompts\Themes\Default\MultiSearchPromptRenderer;
use Laravel\Prompts\Themes\Default\MultiSelectPromptRenderer;
use Laravel\Prompts\Themes\Default\NoteRenderer;
use Laravel\Prompts\Themes\Default\NumberPromptRenderer;
use Laravel\Prompts\Themes\Default\PasswordPromptRenderer;
use Laravel\Prompts\Themes\Default\PausePromptRenderer;
use Laravel\Prompts\Themes\Default\ProgressRenderer;
use Laravel\Prompts\Themes\Default\SearchPromptRenderer;
use Laravel\Prompts\Themes\Default\SelectPromptRenderer;
use Laravel\Prompts\Themes\Default\SpinnerRenderer;
use Laravel\Prompts\Themes\Default\StreamRenderer;
use Laravel\Prompts\Themes\Default\SuggestPromptRenderer;
use Laravel\Prompts\Themes\Default\TableRenderer;
use Laravel\Prompts\Themes\Default\TaskRenderer;
use Laravel\Prompts\Themes\Default\TextareaPromptRenderer;
use Laravel\Prompts\Themes\Default\TextPromptRenderer;
use Laravel\Prompts\Themes\Default\TitleRenderer;
use Laravel\Prompts\Title;

trait Themes
{
    /**
     * The name of the active theme.
     */
    protected static string $theme = 'default';

    /**
     * The available themes.
     *
     * @var array<string, array<class-string<Prompt>, class-string<object&callable>>>
     */
    protected static array $themes = [
        'default' => [
            TextPrompt::class => TextPromptRenderer::class,
            NumberPrompt::class => NumberPromptRenderer::class,
            TextareaPrompt::class => TextareaPromptRenderer::class,
            PasswordPrompt::class => PasswordPromptRenderer::class,
            SelectPrompt::class => SelectPromptRenderer::class,
            MultiSelectPrompt::class => MultiSelectPromptRenderer::class,
            ConfirmPrompt::class => ConfirmPromptRenderer::class,
            PausePrompt::class => PausePromptRenderer::class,
            SearchPrompt::class => SearchPromptRenderer::class,
            MultiSearchPrompt::class => MultiSearchPromptRenderer::class,
            SuggestPrompt::class => SuggestPromptRenderer::class,
            Spinner::class => SpinnerRenderer::class,
            Note::class => NoteRenderer::class,
            Table::class => TableRenderer::class,
            Progress::class => ProgressRenderer::class,
            Clear::class => ClearRenderer::class,
            Grid::class => GridRenderer::class,
            AutoCompletePrompt::class => AutoCompletePromptRenderer::class,
            Title::class => TitleRenderer::class,
            Stream::class => StreamRenderer::class,
            Task::class => TaskRenderer::class,
            DataTablePrompt::class => DataTableRenderer::class,
            Callout::class => CalloutRenderer::class,
        ],
    ];

    /**
     * Get or set the active theme.
     *
     * @throws InvalidArgumentException
     */
    public static function theme(?string $name = null): string
    {
        if ($name === null) {
            return static::$theme;
        }

        if (! isset(static::$themes[$name])) {
            throw new InvalidArgumentException("Prompt theme [{$name}] not found.");
        }

        return static::$theme = $name;
    }

    /**
     * Add a new theme.
     *
     * @param  array<class-string<Prompt>, class-string<object&callable>>  $renderers
     */
    public static function addTheme(string $name, array $renderers): void
    {
        if ($name === 'default') {
            throw new InvalidArgumentException('The default theme cannot be overridden.');
        }

        static::$themes[$name] = $renderers;
    }

    /**
     * Get the renderer for the current prompt.
     */
    protected function getRenderer(): callable
    {
        $class = get_class($this);

        return new (static::$themes[static::$theme][$class] ?? static::$themes['default'][$class])($this);
    }

    /**
     * Render the prompt using the active theme.
     */
    protected function renderTheme(): string
    {
        $renderer = $this->getRenderer();

        return $renderer($this);
    }
}
