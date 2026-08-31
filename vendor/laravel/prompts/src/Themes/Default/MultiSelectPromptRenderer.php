<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\MultiSelectPrompt;
use Laravel\Prompts\Themes\Contracts\Scrolling;

class MultiSelectPromptRenderer extends Renderer implements Scrolling
{
    use Concerns\DrawsBoxes;
    use Concerns\DrawsScrollbars;

    /**
     * Render the multiselect prompt.
     */
    public function __invoke(MultiSelectPrompt $prompt): string
    {
        return match ($prompt->state) {
            'submit' => $this
                ->box(
                    $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->renderSelectedOptions($prompt)
                ),

            'cancel' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->renderOptions($prompt),
                    color: 'red',
                )
                ->error($prompt->cancelMessage),

            'error' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->renderOptions($prompt),
                    color: 'yellow',
                    info: count($prompt->options) > $prompt->scroll ? (count($prompt->value()).' selected') : '',
                )
                ->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5)),

            default => $this
                ->box(
                    $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->renderOptions($prompt),
                    info: $this->getInfoText($prompt),
                )
                ->when(
                    $prompt->hint,
                    fn () => $this->hint($prompt->hint),
                    fn () => $this->newLine() // Space for errors
                ),
        };
    }

    /**
     * Render the options.
     */
    protected function renderOptions(MultiSelectPrompt $prompt): string
    {
        return implode(PHP_EOL, $this->scrollbar(
            array_values(array_map(function ($label, $key) use ($prompt) {
                $label = $this->truncate($label, $prompt->terminal()->cols() - 12);

                $index = array_search($key, array_keys($prompt->options));
                $active = $index === $prompt->highlighted;
                if (array_is_list($prompt->options)) {
                    $value = $prompt->options[$index];
                } else {
                    $value = array_keys($prompt->options)[$index];
                }
                $selected = in_array($value, $prompt->value());

                if ($prompt->state === 'cancel') {
                    return $this->dim(match (true) {
                        $active && $selected => "› ◼ {$this->strikethrough($label)}  ",
                        $active => "› ◻ {$this->strikethrough($label)}  ",
                        $selected => "  ◼ {$this->strikethrough($label)}  ",
                        default => "  ◻ {$this->strikethrough($label)}  ",
                    });
                }

                return match (true) {
                    $active && $selected => "{$this->cyan('› ◼')} {$label}  ",
                    $active => "{$this->cyan('›')} ◻ {$label}  ",
                    $selected => "  {$this->cyan('◼')} {$this->dim($label)}  ",
                    default => "  {$this->dim('◻')} {$this->dim($label)}  ",
                };
            }, $visible = $prompt->visible(), array_keys($visible))),
            $prompt->firstVisible,
            $prompt->scroll,
            count($prompt->options),
            min($this->longest($prompt->options, padding: 6), $prompt->terminal()->cols() - 6),
            $prompt->state === 'cancel' ? 'dim' : 'cyan'
        ));
    }

    /**
     * Render the selected options.
     */
    protected function renderSelectedOptions(MultiSelectPrompt $prompt): string
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
    protected function getInfoText(MultiSelectPrompt $prompt): string
    {
        $parts = array_filter([
            $prompt->infoText(),
            count($prompt->options) > $prompt->scroll ? (count($prompt->value()).' selected') : '',
        ]);

        return implode(' · ', $parts);
    }

    /**
     * The number of lines to reserve outside of the scrollable area.
     */
    public function reservedLines(): int
    {
        return 5;
    }
}
