<?php

use Symfony\Component\Console\Command\Command;

class Foo4Command extends Command
{
    protected function configure()
    {
        $this->setName('foo3:bar:toh');
    }
}
