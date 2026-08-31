<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\SuggestPrompt;
use Laravel\Prompts\Themes\Contracts\Scrolling;

class SuggestPromptRenderer extends Renderer implements Scrolling
{
    use Concerns\DrawsBoxes;
    use Concerns\DrawsScrollbars;

    /**
     * Render the suggest prompt.
     */
    public function __invoke(SuggestPrompt $prompt): string
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
                    $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->strikethrough($this->dim($this->truncate($prompt->value() ?: $prompt->placeholder, $maxWidth))),
                    color: 'red',
                )
                ->error($prompt->cancelMessage),

            'error' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->valueWithCursorAndArrow($prompt, $maxWidth),
                    $this->renderOptions($prompt),
                    color: 'yellow',
                )
                ->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5)),

            default => $this
                ->box(
                    $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->valueWithCursorAndArrow($prompt, $maxWidth),
                    $this->renderOptions($prompt),
                    info: $prompt->infoText(),
                )
                ->when(
                    $prompt->hint,
                    fn () => $this->hint($prompt->hint),
                    fn () => $this->newLine() // Space for errors
                )
                ->spaceForDropdown($prompt),
        };
    }

    /**
     * Render the value with the cursor and an arrow.
     */
    protected function valueWithCursorAndArrow(SuggestPrompt $prompt, int $maxWidth): string
    {
        if ($prompt->highlighted !== null || $prompt->value() !== '' || count($prompt->matches()) === 0) {
            return $prompt->valueWithCursor($maxWidth);
        }

        return preg_replace(
            '/\s$/',
            $this->cyan('⌄'),
            $this->pad($prompt->valueWithCursor($maxWidth - 1).'  ', min($this->longest($prompt->matches(), padding: 2), $maxWidth))
        );
    }

    /**
     * Render a spacer to prevent jumping when the suggestions are displayed.
     */
    protected function spaceForDropdown(SuggestPrompt $prompt): self
    {
        if ($prompt->value() === '' && $prompt->highlighted === null) {
            $this->newLine(min(
                count($prompt->matches()),
                $prompt->scroll,
                $prompt->terminal()->lines() - 7
            ) + 1);
        }

        return $this;
    }

    /**
     * Render the options.
     */
    protected function renderOptions(SuggestPrompt $prompt): string
    {
        if (empty($prompt->matches()) || ($prompt->value() === '' && $prompt->highlighted === null)) {
            return '';
        }

        return implode(PHP_EOL, $this->scrollbar(
            array_map(function ($label, $key) use ($prompt) {
                $label = $this->truncate($label, $prompt->terminal()->cols() - 12);

                return $prompt->highlighted === $key
                    ? "{$this->cyan('›')} {$label}  "
                    : "  {$this->dim($label)}  ";
            }, $visible = $prompt->visible(), array_keys($visible)),
            $prompt->firstVisible,
            $prompt->scroll,
            count($prompt->matches()),
            min($this->longest($prompt->matches(), padding: 4), $prompt->terminal()->cols() - 6),
            $prompt->state === 'cancel' ? 'dim' : 'cyan'
        ));
    }

    /**
     * The number of lines to reserve outside of the scrollable area.
     */
    public function reservedLines(): int
    {
        return 7;
    }
}
