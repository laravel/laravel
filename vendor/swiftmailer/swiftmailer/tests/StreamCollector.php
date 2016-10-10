<?php

class Swift_StreamCollector
{
    public $content = '';

    public function __invoke($arg)
    {
        $this->content .= $arg;
    }
}
