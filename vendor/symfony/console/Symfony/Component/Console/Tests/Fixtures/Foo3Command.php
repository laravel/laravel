<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Foo3Command extends Command
{
    protected function configure()
    {
        $this
            ->setName('foo3:bar')
            ->setDescription('The foo3:bar command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            try {
                throw new \Exception("First exception <p>this is html</p>");
            } catch (\Exception $e) {
                throw new \Exception("Second exception <comment>comment</comment>", 0, $e);
            }
        } catch (\Exception $e) {
            throw new \Exception("Third exception <fg=blue;bg=red>comment</>", 0, $e);
        }
    }
}
