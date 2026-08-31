<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\TextPrompt;

class TextPromptRenderer extends Renderer
{
    use Concerns\DrawsBoxes;

    /**
     * Render the text prompt.
     */
    public function __invoke(TextPrompt $prompt): string
    {
        $maxWidth = $prompt->terminal()->cols() - 6;

        return match ($prompt->state) {
            'submit' => $this
                ->box(
                    $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->truncate($prompt->value(), $maxWidth),
                ),

            'cancel' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->strikethrough($this->dim($this->truncate($prompt->value() ?: $prompt->placeholder, $maxWidth))),
                    color: 'red',
                )
                ->error($prompt->cancelMessage),

            'error' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $prompt->valueWithCursor($maxWidth),
                    color: 'yellow',
                )
                ->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5)),

            default => $this
                ->box(
                    $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $prompt->valueWithCursor($maxWidth),
                )
                ->when(
                    $prompt->hint,
                    fn () => $this->hint($prompt->hint),
                    fn () => $this->newLine() // Space for errors
                )
        };
    }
}
