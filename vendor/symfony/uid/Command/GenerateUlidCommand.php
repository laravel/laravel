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
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Factory\UlidFactory;

#[AsCommand(name: 'ulid:generate', description: 'Generate a ULID')]
class GenerateUlidCommand extends Command
{
    public function __construct(
        private UlidFactory $factory = new UlidFactory(),
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputOption('time', null, InputOption::VALUE_REQUIRED, 'The ULID timestamp: a parsable date/time string'),
                new InputOption('count', 'c', InputOption::VALUE_REQUIRED, 'The number of ULID to generate', 1),
                new InputOption('format', 'f', InputOption::VALUE_REQUIRED, \sprintf('The ULID output format ("%s")', implode('", "', $this->getAvailableFormatOptions())), 'base32'),
            ])
            ->setHelp(<<<'EOF'
                The <info>%command.name%</info> command generates a ULID.

                    <info>php %command.full_name%</info>

                To specify the timestamp:

                    <info>php %command.full_name% --time="2021-02-16 14:09:08"</info>

                To generate several ULIDs:

                    <info>php %command.full_name% --count=10</info>

                To output a specific format:

                    <info>php %command.full_name% --format=rfc4122</info>
                EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);

        if (null !== $time = $input->getOption('time')) {
            try {
                $time = new \DateTimeImmutable($time);
            } catch (\Exception $e) {
                $io->error(\sprintf('Invalid timestamp "%s": %s', $time, str_replace('DateTimeImmutable::__construct(): ', '', $e->getMessage())));

                return 1;
            }
        }

        $formatOption = $input->getOption('format');

        if (\in_array($formatOption, $this->getAvailableFormatOptions(), true)) {
            $format = 'to'.ucfirst($formatOption);
        } else {
            $io->error(\sprintf('Invalid format "%s", supported formats are "%s".', $formatOption, implode('", "', $this->getAvailableFormatOptions())));

            return 1;
        }

        $count = (int) $input->getOption('count');
        try {
            for ($i = 0; $i < $count; ++$i) {
                $output->writeln($this->factory->create($time)->$format());
            }
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return 1;
        }

        return 0;
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestOptionValuesFor('format')) {
            $suggestions->suggestValues($this->getAvailableFormatOptions());
        }
    }

    /** @return string[] */
    private function getAvailableFormatOptions(): array
    {
        return [
            'base32',
            'base58',
            'rfc4122',
        ];
    }
}
