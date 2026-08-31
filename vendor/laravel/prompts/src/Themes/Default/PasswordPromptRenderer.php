<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\PasswordPrompt;

class PasswordPromptRenderer extends Renderer
{
    use Concerns\DrawsBoxes;

    /**
     * Render the password prompt.
     */
    public function __invoke(PasswordPrompt $prompt): string
    {
        $maxWidth = $prompt->terminal()->cols() - 6;

        return match ($prompt->state) {
            'submit' => $this
                ->box(
                    $this->dim($prompt->label),
                    $this->truncate($prompt->masked(), $maxWidth),
                ),

            'cancel' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->strikethrough($this->dim($this->truncate($prompt->masked() ?: $prompt->placeholder, $maxWidth))),
                    color: 'red',
                )
                ->error($prompt->cancelMessage),

            'error' => $this
                ->box(
                    $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $prompt->maskedWithCursor($maxWidth),
                    color: 'yellow',
                )
                ->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5)),

            default => $this
                ->box(
                    $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $prompt->maskedWithCursor($maxWidth),
                )
                ->when(
                    $prompt->hint,
                    fn () => $this->hint($prompt->hint),
                    fn () => $this->newLine() // Space for errors
                ),
        };
    }
}
