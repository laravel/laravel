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
use Symfony\Component\Uid\Exception\LogicException;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'uuid:generate', description: 'Generate a UUID')]
class GenerateUuidCommand extends Command
{
    public function __construct(
        private UuidFactory $factory = new UuidFactory(),
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputOption('time-based', null, InputOption::VALUE_REQUIRED, 'The timestamp, to generate a time-based UUID: a parsable date/time string'),
                new InputOption('node', null, InputOption::VALUE_REQUIRED, 'The UUID whose node part should be used as the node of the generated UUID'),
                new InputOption('name-based', null, InputOption::VALUE_REQUIRED, 'The name, to generate a name-based UUID'),
                new InputOption('namespace', null, InputOption::VALUE_REQUIRED, 'The UUID to use at the namespace for named-based UUIDs, predefined namespaces keywords "dns", "url", "oid" and "x500" are accepted'),
                new InputOption('random-based', null, InputOption::VALUE_NONE, 'To generate a random-based UUID'),
                new InputOption('count', 'c', InputOption::VALUE_REQUIRED, 'The number of UUID to generate', 1),
                new InputOption('format', 'f', InputOption::VALUE_REQUIRED, \sprintf('The UUID output format ("%s")', implode('", "', $this->getAvailableFormatOptions())), 'rfc4122'),
            ])
            ->setHelp(<<<'EOF'
                The <info>%command.name%</info> generates a UUID.

                    <info>php %command.full_name%</info>

                To generate a time-based UUID:

                    <info>php %command.full_name% --time-based=now</info>

                To specify a time-based UUID's node:

                    <info>php %command.full_name% --time-based=@1613480254 --node=fb3502dc-137e-4849-8886-ac90d07f64a7</info>

                To generate a name-based UUID:

                    <info>php %command.full_name% --name-based=foo</info>

                To specify a name-based UUID's namespace:

                    <info>php %command.full_name% --name-based=bar --namespace=fb3502dc-137e-4849-8886-ac90d07f64a7</info>

                To generate a random-based UUID:

                    <info>php %command.full_name% --random-based</info>

                To generate several UUIDs:

                    <info>php %command.full_name% --count=10</info>

                To output a specific format:

                    <info>php %command.full_name% --format=base58</info>
                EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);

        $time = $input->getOption('time-based');
        $node = $input->getOption('node');
        $name = $input->getOption('name-based');
        $namespace = $input->getOption('namespace');
        $random = $input->getOption('random-based');

        if (false !== ($time ?? $name ?? $random) && 1 < ((null !== $time) + (null !== $name) + $random)) {
            $io->error('Only one of "--time-based", "--name-based" or "--random-based" can be provided at a time.');

            return 1;
        }

        if (null === $time && null !== $node) {
            $io->error('Option "--node" can only be used with "--time-based".');

            return 1;
        }

        if (null === $name && null !== $namespace) {
            $io->error('Option "--namespace" can only be used with "--name-based".');

            return 1;
        }

        switch (true) {
            case null !== $time:
                if (null !== $node) {
                    try {
                        $node = Uuid::fromString($node);
                    } catch (\InvalidArgumentException $e) {
                        $io->error(\sprintf('Invalid node "%s": %s', $node, $e->getMessage()));

                        return 1;
                    }
                }

                try {
                    new \DateTimeImmutable($time);
                } catch (\Exception $e) {
                    $io->error(\sprintf('Invalid timestamp "%s": %s', $time, str_replace('DateTimeImmutable::__construct(): ', '', $e->getMessage())));

                    return 1;
                }

                $create = fn (): Uuid => $this->factory->timeBased($node)->create(new \DateTimeImmutable($time));
                break;

            case null !== $name:
                if ($namespace && !\in_array($namespace, ['dns', 'url', 'oid', 'x500'], true)) {
                    try {
                        $namespace = Uuid::fromString($namespace);
                    } catch (\InvalidArgumentException $e) {
                        $io->error(\sprintf('Invalid namespace "%s": %s', $namespace, $e->getMessage()));

                        return 1;
                    }
                }

                $create = function () use ($namespace, $name): Uuid {
                    try {
                        $factory = $this->factory->nameBased($namespace);
                    } catch (LogicException) {
                        throw new \InvalidArgumentException('Missing namespace: use the "--namespace" option or configure a default namespace in the underlying factory.');
                    }

                    return $factory->create($name);
                };
                break;

            case $random:
                $create = $this->factory->randomBased()->create(...);
                break;

            default:
                $create = $this->factory->create(...);
                break;
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
                $output->writeln($create()->$format());
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
