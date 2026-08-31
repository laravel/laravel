<?php

namespace Illuminate\Console\View\Components;

use Symfony\Component\Console\Output\OutputInterface;

class Info extends Component
{
    /**
     * Renders the component using the given arguments.
     *
     * @param  string  $string
     * @param  int  $verbosity
     * @return void
     */
    public function render($string, $verbosity = OutputInterface::VERBOSITY_NORMAL)
    {
        (new Line($this->output))->render('info', $string, $verbosity);
    }
}
