<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\Title;

class TitleRenderer extends Renderer
{
    /**
     * Render the title.
     */
    public function __invoke(Title $title): string
    {
        return "\033]0;{$title->title}\007";
    }
}
