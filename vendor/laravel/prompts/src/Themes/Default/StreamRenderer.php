<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\Stream;

class StreamRenderer extends Renderer
{
    /**
     * Render the stream.
     */
    public function __invoke(Stream $stream): string
    {
        foreach ($stream->lines() as $line) {
            $this->line(" {$line}");
        }

        return $this;
    }
}
