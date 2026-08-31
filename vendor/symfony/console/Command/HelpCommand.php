<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Descriptor\ApplicationDescription;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * HelpCommand displays the help for a given command.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HelpCommand extends Command
{
    private Command $command;

    protected function configure(): void
    {
        $this->ignoreValidationErrors();

        $this
            ->setName('help')
            ->setDefinition([
                new InputArgument('command_name', InputArgument::OPTIONAL, 'The command name', 'help', fn () => array_keys((new ApplicationDescription($this->getApplication()))->getCommands())),
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt', static fn () => (new DescriptorHelper())->getFormats()),
                new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command help'),
            ])
            ->setDescription('Display help for a command')
            ->setHelp(<<<'EOF'
                The <info>%command.name%</info> command displays help for a given command:

                  <info>%command.full_name% list</info>

                You can also output the help in other formats by using the <info>--format</info> option:

                  <info>%command.full_name% --format=xml list</info>

                To display the list of available commands, please use the <info>list</info> command.
                EOF
            )
        ;
    }

    public function setCommand(Command $command): void
    {
        $this->command = $command;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->command ??= $this->getApplication()->find($input->getArgument('command_name'));

        $helper = new DescriptorHelper();
        $helper->describe($output, $this->command, [
            'format' => $input->getOption('format'),
            'raw_text' => $input->getOption('raw'),
        ]);

        unset($this->command);

        return 0;
    }
}
