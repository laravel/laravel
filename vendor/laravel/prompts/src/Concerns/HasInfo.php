<?php

namespace Laravel\Prompts\Concerns;

use Closure;

trait HasInfo
{
    /**
     * Get the resolved info text.
     */
    public function infoText(): string
    {
        if ($this->info instanceof Closure) {
            return ($this->info)($this->highlightedValue()) ?? '';
        }

        return $this->info;
    }
}
