<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Command\Descriptor\CliDescriptor;
use Symfony\Component\VarDumper\Command\Descriptor\DumpDescriptorInterface;
use Symfony\Component\VarDumper\Command\Descriptor\HtmlDescriptor;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Server\DumpServer;

/**
 * Starts a dump server to collect and output dumps on a single place with multiple formats support.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 *
 * @final
 */
#[AsCommand(name: 'server:dump', description: 'Start a dump server that collects and displays dumps in a single place')]
class ServerDumpCommand extends Command
{
    /** @var DumpDescriptorInterface[] */
    private array $descriptors;

    public function __construct(
        private DumpServer $server,
        array $descriptors = [],
    ) {
        $this->descriptors = $descriptors + [
            'cli' => new CliDescriptor(new CliDumper()),
            'html' => new HtmlDescriptor(new HtmlDumper()),
        ];

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('format', null, InputOption::VALUE_REQUIRED, \sprintf('The output format (%s)', implode(', ', $this->getAvailableFormats())), 'cli')
            ->setHelp(<<<'EOF'
                <info>%command.name%</info> starts a dump server that collects and displays
                dumps in a single place for debugging you application:

                  <info>php %command.full_name%</info>

                You can consult dumped data in HTML format in your browser by providing the <info>--format=html</info> option
                and redirecting the output to a file:

                  <info>php %command.full_name% --format="html" > dump.html</info>

                EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $format = $input->getOption('format');

        if (!$descriptor = $this->descriptors[$format] ?? null) {
            throw new InvalidArgumentException(\sprintf('Unsupported format "%s".', $format));
        }

        $errorIo = $io->getErrorStyle();
        $errorIo->title('Symfony Var Dumper Server');

        $this->server->start();

        $errorIo->success(\sprintf('Server listening on %s', $this->server->getHost()));
        $errorIo->comment('Quit the server with CONTROL-C.');

        $this->server->listen(static function (Data $data, array $context, int $clientId) use ($descriptor, $io) {
            $descriptor->describe($io, $data, $context, $clientId);
        });

        return 0;
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if ($input->mustSuggestOptionValuesFor('format')) {
            $suggestions->suggestValues($this->getAvailableFormats());
        }
    }

    private function getAvailableFormats(): array
    {
        return array_keys($this->descriptors);
    }
}
