<?php

namespace Illuminate\Console\View\Components;

class Confirm extends Component
{
    /**
     * Renders the component using the given arguments.
     *
     * @param  string  $question
     * @param  bool  $default
     * @return bool
     */
    public function render($question, $default = false)
    {
        return $this->usingQuestionHelper(
            fn () => $this->output->confirm($question, $default),
        );
    }
}
