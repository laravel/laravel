<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\ConfirmPrompt;

class ConfirmPromptRenderer extends Renderer
{
    use Concerns\DrawsBoxes;

    /**
     * Render the confirm prompt.
     */
    public function __invoke(ConfirmPrompt $prompt): string
    {
        return match ($prompt->state) {
            'submit' => $this
                ->box(
                    $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->truncate($prompt->label(), $prompt->terminal()->cols() - 6)
                ),

            'cancel' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->renderOptions($prompt),
                    color: 'red'
                )
                ->error($prompt->cancelMessage),

            'error' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->renderOptions($prompt),
                    color: 'yellow',
                )
                ->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5)),

            default => $this
                ->box(
                    $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->renderOptions($prompt),
                )
                ->when(
                    $prompt->hint,
                    fn () => $this->hint($prompt->hint),
                    fn () => $this->newLine() // Space for errors
                ),
        };
    }

    /**
     * Render the confirm prompt options.
     */
    protected function renderOptions(ConfirmPrompt $prompt): string
    {
        $length = (int) floor(($prompt->terminal()->cols() - 14) / 2);
        $yes = $this->truncate($prompt->yes, $length);
        $no = $this->truncate($prompt->no, $length);

        if ($prompt->state === 'cancel') {
            return $this->dim($prompt->confirmed
                ? "● {$this->strikethrough($yes)} / ○ {$this->strikethrough($no)}"
                : "○ {$this->strikethrough($yes)} / ● {$this->strikethrough($no)}");
        }

        return $prompt->confirmed
            ? "{$this->green('●')} {$yes} {$this->dim('/ ○ '.$no)}"
            : "{$this->dim('○ '.$yes.' /')} {$this->green('●')} {$no}";
    }
}
