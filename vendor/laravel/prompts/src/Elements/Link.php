<?php

namespace Laravel\Prompts\Elements;

class Link implements ElementContract
{
    public readonly string $label;

    public function __construct(
        public readonly string $url,
        ?string $label = null,
        public readonly bool $underline = true,
    ) {
        $this->label = $label ?? $this->url;
    }

    public function __toString(): string
    {
        $text = ($this->underline) ? "\e[4m{$this->label}\e[24m" : $this->label;

        return "\e]8;;{$this->url}\e\\{$text}\e]8;;\e\\";
    }
}
