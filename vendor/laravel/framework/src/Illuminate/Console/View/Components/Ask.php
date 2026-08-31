<?php

namespace Illuminate\Console\View\Components;

use Symfony\Component\Console\Question\Question;

class Ask extends Component
{
    /**
     * Renders the component using the given arguments.
     *
     * @param  string  $question
     * @param  string|null  $default
     * @param  bool  $multiline
     * @return mixed
     */
    public function render($question, $default = null, $multiline = false)
    {
        return $this->usingQuestionHelper(
            fn () => $this->output->askQuestion(
                (new Question($question, $default))
                    ->setMultiline($multiline)
            )
        );
    }
}
