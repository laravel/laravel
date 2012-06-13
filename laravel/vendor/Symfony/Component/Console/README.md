Console Component
=================

Console eases the creation of beautiful and testable command line interfaces.

The Application object manages the CLI application:

    use Symfony\Component\Console\Application;

    $console = new Application();
    $console->run();

The ``run()`` method parses the arguments and options passed on the command
line and executes the right command.

Registering a new command can easily be done via the ``register()`` method,
which returns a ``Command`` instance:

    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Output\OutputInterface;

    $console
        ->register('ls')
        ->setDefinition(array(
            new InputArgument('dir', InputArgument::REQUIRED, 'Directory name'),
        ))
        ->setDescription('Displays the files in the given directory')
        ->setCode(function (InputInterface $input, OutputInterface $output) {
            $dir = $input->getArgument('dir');

            $output->writeln(sprintf('Dir listing for <info>%s</info>', $dir));
        })
    ;

You can also register new commands via classes.

The component provides a lot of features like output coloring, input and
output abstractions (so that you can easily unit-test your commands),
validation, automatic help messages, ...

Resources
---------

Unit tests:

https://github.com/symfony/symfony/tree/master/tests/Symfony/Tests/Component/Console
