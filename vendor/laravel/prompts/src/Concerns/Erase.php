<?php

namespace Laravel\Prompts\Concerns;

trait Erase
{
    /**
     * Erase the given number of lines downwards from the cursor position.
     */
    public function eraseLines(int $count): void
    {
        $clear = '';
        for ($i = 0; $i < $count; $i++) {
            $clear .= "\e[2K".($i < $count - 1 ? "\e[{$count}A" : '');
        }

        if ($count) {
            $clear .= "\e[G";
        }

        static::writeDirectly($clear);
    }

    /**
     * Erase from cursor until end of screen.
     */
    public function eraseDown(): void
    {
        static::writeDirectly("\e[J");
    }
}
