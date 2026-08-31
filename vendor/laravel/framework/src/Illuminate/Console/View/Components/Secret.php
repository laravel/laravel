<?php

namespace Illuminate\Console\View\Components;

use Symfony\Component\Console\Question\Question;

class Secret extends Component
{
    /**
     * Renders the component using the given arguments.
     *
     * @param  string  $question
     * @param  bool  $fallback
     * @return mixed
     */
    public function render($question, $fallback = true)
    {
        $question = new Question($question);

        $question->setHidden(true)->setHiddenFallback($fallback);

        return $this->usingQuestionHelper(fn () => $this->output->askQuestion($question));
    }
}
