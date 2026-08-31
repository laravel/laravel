<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\MaxUuid;
use Symfony\Component\Uid\NilUuid;
use Symfony\Component\Uid\TimeBasedUidInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'uuid:inspect', description: 'Inspect a UUID')]
class InspectUuidCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('uuid', InputArgument::REQUIRED, 'The UUID to inspect'),
            ])
            ->setHelp(<<<'EOF'
                The <info>%command.name%</info> displays information about a UUID.

                    <info>php %command.full_name% a7613e0a-5986-11eb-a861-2bf05af69e52</info>
                    <info>php %command.full_name% MfnmaUvvQ1h8B14vTwt6dX</info>
                    <info>php %command.full_name% 57C4Z0MPC627NTGR9BY1DFD7JJ</info>
                EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);

        try {
            $uuid = Uuid::fromString($input->getArgument('uuid'));
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return 1;
        }

        if (new NilUuid() == $uuid) {
            $version = 'nil';
        } elseif (new MaxUuid() == $uuid) {
            $version = 'max';
        } else {
            $version = hexdec($uuid->toRfc4122()[14]);
        }

        $rows = [
            ['Version', $version],
            ['toRfc4122 (canonical)', (string) $uuid],
            ['toBase58', $uuid->toBase58()],
            ['toBase32', $uuid->toBase32()],
            ['toHex', $uuid->toHex()],
        ];

        if ($uuid instanceof TimeBasedUidInterface) {
            $rows[] = new TableSeparator();
            $rows[] = ['Time', $uuid->getDateTime()->format('Y-m-d H:i:s.u \U\T\C')];
        }

        $io->table(['Label', 'Value'], $rows);

        return 0;
    }
}
