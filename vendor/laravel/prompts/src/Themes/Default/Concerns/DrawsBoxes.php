<?php

namespace Laravel\Prompts\Themes\Default\Concerns;

use Laravel\Prompts\Prompt;

trait DrawsBoxes
{
    use InteractsWithStrings;

    protected int $minWidth = 60;

    /**
     * Draw a box.
     *
     * @return $this
     */
    protected function box(
        string $title,
        string $body,
        string $footer = '',
        string $color = 'gray',
        string $info = '',
    ): self {
        $this->minWidth = min($this->minWidth, Prompt::terminal()->cols() - 6);

        $bodyLines = explode(PHP_EOL, $body);
        $footerLines = array_filter(explode(PHP_EOL, $footer));

        $width = $this->longest(array_merge($bodyLines, $footerLines, [$title]));

        $titleLength = mb_strwidth($this->stripEscapeSequences($title));
        $titleLabel = $titleLength > 0 ? " {$title} " : '';
        $topBorder = str_repeat('─', $width - $titleLength + ($titleLength > 0 ? 0 : 2));

        $this->line("{$this->{$color}(' ┌')}{$titleLabel}{$this->{$color}($topBorder.'┐')}");

        foreach ($bodyLines as $line) {
            $this->line("{$this->{$color}(' │')} {$this->pad($line, $width)} {$this->{$color}('│')}");
        }

        if (count($footerLines) > 0) {
            $this->line($this->{$color}(' ├'.str_repeat('─', $width + 2).'┤'));

            foreach ($footerLines as $line) {
                $this->line("{$this->{$color}(' │')} {$this->pad($line, $width)} {$this->{$color}('│')}");
            }
        }

        if ($info) {
            $info = $this->truncate($info, $width - 1);
        }

        $this->line($this->{$color}(' └'.str_repeat(
            '─', $info ? ($width - mb_strwidth($this->stripEscapeSequences($info))) : ($width + 2)
        ).($info ? " {$info} " : '').'┘'));

        return $this;
    }
}
