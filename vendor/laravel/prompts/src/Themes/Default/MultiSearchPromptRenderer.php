<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\MultiSearchPrompt;
use Laravel\Prompts\Themes\Contracts\Scrolling;

class MultiSearchPromptRenderer extends Renderer implements Scrolling
{
    use Concerns\DrawsBoxes;
    use Concerns\DrawsScrollbars;

    /**
     * Render the suggest prompt.
     */
    public function __invoke(MultiSearchPrompt $prompt): string
    {
        $maxWidth = $prompt->terminal()->cols() - 6;

        return match ($prompt->state) {
            'submit' => $this
                ->box(
                    $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->renderSelectedOptions($prompt),
                ),

            'cancel' => $this
                ->box(
                    $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->strikethrough($this->dim($this->truncate($prompt->searchValue() ?: $prompt->placeholder, $maxWidth))),
                    color: 'red',
                )
                ->error($prompt->cancelMessage),

            'error' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $prompt->valueWithCursor($maxWidth),
                    $this->renderOptions($prompt),
                    color: 'yellow',
                    info: $this->getInfoText($prompt),
                )
                ->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5)),

            'searching' => $this
                ->box(
                    $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->valueWithCursorAndSearchIcon($prompt, $maxWidth),
                    $this->renderOptions($prompt),
                    info: $this->getInfoText($prompt),
                )
                ->hint($prompt->hint),

            default => $this
                ->box(
                    $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $prompt->valueWithCursor($maxWidth),
                    $this->renderOptions($prompt),
                    info: $this->getInfoText($prompt),
                )
                ->when(
                    $prompt->hint,
                    fn () => $this->hint($prompt->hint),
                    fn () => $this->newLine() // Space for errors
                )
                ->spaceForDropdown($prompt)
        };
    }

    /**
     * Render the value with the cursor and a search icon.
     */
    protected function valueWithCursorAndSearchIcon(MultiSearchPrompt $prompt, int $maxWidth): string
    {
        return preg_replace(
            '/\s$/',
            $this->cyan('…'),
            $this->pad($prompt->valueWithCursor($maxWidth - 1).'  ', min($this->longest($prompt->matches(), padding: 2), $maxWidth))
        );
    }

    /**
     * Render a spacer to prevent jumping when the suggestions are displayed.
     */
    protected function spaceForDropdown(MultiSearchPrompt $prompt): self
    {
        if ($prompt->searchValue() !== '') {
            return $this;
        }

        $this->newLine(max(
            0,
            min($prompt->scroll, $prompt->terminal()->lines() - 7) - count($prompt->matches()),
        ));

        if ($prompt->matches() === []) {
            $this->newLine();
        }

        return $this;
    }

    /**
     * Render the options.
     */
    protected function renderOptions(MultiSearchPrompt $prompt): string
    {
        if ($prompt->searchValue() !== '' && empty($prompt->matches())) {
            return $this->gray('  '.($prompt->state === 'searching' ? 'Searching...' : 'No results.'));
        }

        return implode(PHP_EOL, $this->scrollbar(
            array_map(function ($label, $key) use ($prompt) {
                $label = $this->truncate($label, $prompt->terminal()->cols() - 12);

                $index = array_search($key, array_keys($prompt->matches()));
                $active = $index === $prompt->highlighted;
                $selected = $prompt->isList()
                    ? in_array($label, $prompt->value())
                    : in_array($key, $prompt->value());

                return match (true) {
                    $active && $selected => "{$this->cyan('› ◼')} {$label}  ",
                    $active => "{$this->cyan('›')} ◻ {$label}  ",
                    $selected => "  {$this->cyan('◼')} {$this->dim($label)}  ",
                    default => "  {$this->dim('◻')} {$this->dim($label)}  ",
                };
            }, $prompt->visible(), array_keys($prompt->visible())),
            $prompt->firstVisible,
            $prompt->scroll,
            count($prompt->matches()),
            min($this->longest($prompt->matches(), padding: 4), $prompt->terminal()->cols() - 6)
        ));
    }

    /**
     * Render the selected options.
     */
    protected function renderSelectedOptions(MultiSearchPrompt $prompt): string
    {
        if (count($prompt->labels()) === 0) {
            return $this->gray('None');
        }

        return implode("\n", array_map(
            fn ($label) => $this->truncate($label, $prompt->terminal()->cols() - 6),
            $prompt->labels()
        ));
    }

    /**
     * Render the info text.
     */
    protected function getInfoText(MultiSearchPrompt $prompt): string
    {
        $selected = count($prompt->value()).' selected';

        $hiddenCount = count($prompt->value()) - count(array_filter(
            $prompt->matches(),
            fn ($label, $key) => in_array($prompt->isList() ? $label : $key, $prompt->value()),
            ARRAY_FILTER_USE_BOTH
        ));

        if ($hiddenCount > 0) {
            $selected .= " ($hiddenCount hidden)";
        }

        $parts = array_filter([$prompt->infoText(), $selected]);

        return implode(' · ', $parts);
    }

    /**
     * The number of lines to reserve outside of the scrollable area.
     */
    public function reservedLines(): int
    {
        return 7;
    }
}
