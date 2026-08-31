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

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Dumps the completion script for the current shell.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
#[AsCommand(name: 'completion', description: 'Dump the shell completion script')]
final class DumpCompletionCommand extends Command
{
    private array $supportedShells;

    protected function configure(): void
    {
        $fullCommand = $_SERVER['PHP_SELF'];
        $commandName = basename($fullCommand);
        $fullCommand = @realpath($fullCommand) ?: $fullCommand;

        $shell = self::guessShell();
        [$rcFile, $completionFile] = match ($shell) {
            'fish' => ['~/.config/fish/config.fish', "/etc/fish/completions/$commandName.fish"],
            'zsh' => ['~/.zshrc', '$fpath[1]/_'.$commandName],
            default => ['~/.bashrc', "/etc/bash_completion.d/$commandName"],
        };

        $supportedShells = implode(', ', $this->getSupportedShells());

        $this
            ->setHelp(<<<EOH
                The <info>%command.name%</> command dumps the shell completion script required
                to use shell autocompletion (currently, {$supportedShells} completion are supported).

                <comment>Static installation
                -------------------</>

                Dump the script to a global completion file and restart your shell:

                    <info>%command.full_name% {$shell} | sudo tee {$completionFile}</>

                Or dump the script to a local file and source it:

                    <info>%command.full_name% {$shell} > completion.sh</>

                    <comment># source the file whenever you use the project</>
                    <info>source completion.sh</>

                    <comment># or add this line at the end of your "{$rcFile}" file:</>
                    <info>source /path/to/completion.sh</>

                <comment>Dynamic installation
                --------------------</>

                Add this to the end of your shell configuration file (e.g. <info>"{$rcFile}"</>):

                    <info>eval "$({$fullCommand} completion {$shell})"</>
                EOH
            )
            ->addArgument('shell', InputArgument::OPTIONAL, 'The shell type (e.g. "bash"), the value of the "$SHELL" env var will be used if this is not given', null, $this->getSupportedShells(...))
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Tail the completion debug log')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandName = basename($_SERVER['argv'][0]);

        if ($input->getOption('debug')) {
            $this->tailDebugLog($commandName, $output);

            return 0;
        }

        $shell = $input->getArgument('shell') ?? self::guessShell();
        $completionFile = __DIR__.'/../Resources/completion.'.$shell;
        if (!file_exists($completionFile)) {
            $supportedShells = $this->getSupportedShells();

            if ($output instanceof ConsoleOutputInterface) {
                $output = $output->getErrorOutput();
            }
            if ($shell) {
                $output->writeln(\sprintf('<error>Detected shell "%s", which is not supported by Symfony shell completion (supported shells: "%s").</>', $shell, implode('", "', $supportedShells)));
            } else {
                $output->writeln(\sprintf('<error>Shell not detected, Symfony shell completion only supports "%s").</>', implode('", "', $supportedShells)));
            }

            return 2;
        }

        $output->write(str_replace(['{{ COMMAND_NAME }}', '{{ VERSION }}'], [$commandName, CompleteCommand::COMPLETION_API_VERSION], file_get_contents($completionFile)));

        return 0;
    }

    private static function guessShell(): string
    {
        return basename($_SERVER['SHELL'] ?? '');
    }

    private function tailDebugLog(string $commandName, OutputInterface $output): void
    {
        $debugFile = sys_get_temp_dir().'/sf_'.$commandName.'.log';
        if (!file_exists($debugFile)) {
            touch($debugFile);
        }
        $process = new Process(['tail', '-f', $debugFile], null, null, null, 0);
        $process->run(static function (string $type, string $line) use ($output): void {
            $output->write($line);
        });
    }

    /**
     * @return string[]
     */
    private function getSupportedShells(): array
    {
        if (isset($this->supportedShells)) {
            return $this->supportedShells;
        }

        $shells = [];

        foreach (new \DirectoryIterator(__DIR__.'/../Resources/') as $file) {
            if (str_starts_with($file->getBasename(), 'completion.') && $file->isFile()) {
                $shells[] = $file->getExtension();
            }
        }
        sort($shells);

        return $this->supportedShells = $shells;
    }
}
