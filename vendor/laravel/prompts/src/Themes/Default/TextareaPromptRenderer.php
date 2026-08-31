<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\TextareaPrompt;
use Laravel\Prompts\Themes\Contracts\Scrolling;

class TextareaPromptRenderer extends Renderer implements Scrolling
{
    use Concerns\DrawsBoxes;
    use Concerns\DrawsScrollbars;

    /**
     * Render the textarea prompt.
     */
    public function __invoke(TextareaPrompt $prompt): string
    {
        $prompt->width = $prompt->terminal()->cols() - 8;

        return match ($prompt->state) {
            'submit' => $this
                ->box(
                    $this->dim($this->truncate($prompt->label, $prompt->width)),
                    implode(PHP_EOL, $prompt->lines()),
                ),

            'cancel' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->width),
                    implode(PHP_EOL, array_map(fn ($line) => $this->strikethrough($this->dim($line)), $prompt->lines())),
                    color: 'red',
                )
                ->error($prompt->cancelMessage),

            'error' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->width),
                    $this->renderText($prompt),
                    color: 'yellow',
                    info: 'Ctrl+D to submit'
                )
                ->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5)),

            default => $this
                ->box(
                    $this->cyan($this->truncate($prompt->label, $prompt->width)),
                    $this->renderText($prompt),
                    info: 'Ctrl+D to submit'
                )
                ->when(
                    $prompt->hint,
                    fn () => $this->hint($prompt->hint),
                    fn () => $this->newLine() // Space for errors
                )
        };
    }

    /**
     * Render the text in the prompt.
     */
    protected function renderText(TextareaPrompt $prompt): string
    {
        $visible = $prompt->visible();

        while (count($visible) < $prompt->scroll) {
            $visible[] = '';
        }

        $longest = $this->longest($prompt->lines()) + 2;

        return implode(PHP_EOL, $this->scrollbar(
            $visible,
            $prompt->firstVisible,
            $prompt->scroll,
            count($prompt->lines()),
            min($longest, $prompt->width + 2),
        ));
    }

    /**
     * The number of lines to reserve outside of the scrollable area.
     */
    public function reservedLines(): int
    {
        return 5;
    }
}
