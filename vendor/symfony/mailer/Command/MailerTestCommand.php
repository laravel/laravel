<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;

/**
 * A console command to test Mailer transports.
 */
#[AsCommand(name: 'mailer:test', description: 'Test Mailer transports by sending an email')]
final class MailerTestCommand extends Command
{
    public function __construct(private TransportInterface $transport)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('to', InputArgument::REQUIRED, 'The recipient of the message')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'The sender of the message', 'from@example.org')
            ->addOption('subject', null, InputOption::VALUE_REQUIRED, 'The subject of the message', 'Testing transport')
            ->addOption('body', null, InputOption::VALUE_REQUIRED, 'The body of the message', 'Testing body')
            ->addOption('transport', null, InputOption::VALUE_REQUIRED, 'The transport to be used')
            ->setHelp(<<<'EOF'
                The <info>%command.name%</info> command tests a Mailer transport by sending a simple email message:

                <info>php %command.full_name% to@example.com</info>

                You can also specify a specific transport:

                    <info>php %command.full_name% to@example.com --transport=transport_name</info>

                Note that this command bypasses the Messenger bus if configured.

                EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = (new Email())
            ->to($input->getArgument('to'))
            ->from($input->getOption('from'))
            ->subject($input->getOption('subject'))
            ->text($input->getOption('body'))
        ;
        if ($transport = $input->getOption('transport')) {
            $message->getHeaders()->addTextHeader('X-Transport', $transport);
        }

        $this->transport->send($message);

        return 0;
    }
}
