<?php

use Symfony\Component\Console\Command\Command;

class BarBucCommand extends Command
{
    protected function configure()
    {
        $this->setName('bar:buc');
    }
}
