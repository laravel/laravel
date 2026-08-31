<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Yaml\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\CI\GithubActionReporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

/**
 * Validates YAML files syntax and outputs encountered errors.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
#[AsCommand(name: 'lint:yaml', description: 'Lint a YAML file and outputs encountered errors')]
class LintCommand extends Command
{
    private Parser $parser;
    private ?string $format = null;
    private bool $displayCorrectFiles;
    private ?\Closure $directoryIteratorProvider;
    private ?\Closure $isReadableProvider;

    public function __construct(?string $name = null, ?callable $directoryIteratorProvider = null, ?callable $isReadableProvider = null)
    {
        parent::__construct($name);

        $this->directoryIteratorProvider = null === $directoryIteratorProvider ? null : $directoryIteratorProvider(...);
        $this->isReadableProvider = null === $isReadableProvider ? null : $isReadableProvider(...);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::IS_ARRAY, 'A file, a directory or "-" for reading from STDIN')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, \sprintf('The output format ("%s")', implode('", "', $this->getAvailableFormatOptions())))
            ->addOption('exclude', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Path(s) to exclude')
            ->addOption('parse-tags', null, InputOption::VALUE_NEGATABLE, 'Parse custom tags', null)
            ->setHelp(<<<EOF
                The <info>%command.name%</info> command lints a YAML file and outputs to STDOUT
                the first encountered syntax error.

                You can validates YAML contents passed from STDIN:

                  <info>cat filename | php %command.full_name% -</info>

                You can also validate the syntax of a file:

                  <info>php %command.full_name% filename</info>

                Or of a whole directory:

                  <info>php %command.full_name% dirname</info>

                The <info>--format</info> option specifies the format of the command output:

                  <info>php %command.full_name% dirname --format=json</info>

                You can also exclude one or more specific files:

                  <info>php %command.full_name% dirname --exclude="dirname/foo.yaml" --exclude="dirname/bar.yaml"</info>

                EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filenames = (array) $input->getArgument('filename');
        $excludes = $input->getOption('exclude');
        $this->format = $input->getOption('format');
        $flags = $input->getOption('parse-tags');

        if (null === $this->format) {
            // Autodetect format according to CI environment
            $this->format = class_exists(GithubActionReporter::class) && GithubActionReporter::isGithubActionEnvironment() ? 'github' : 'txt';
        }

        $flags = $flags ? Yaml::PARSE_CUSTOM_TAGS : 0;

        $this->displayCorrectFiles = $output->isVerbose();

        if (['-'] === $filenames) {
            return $this->display($io, [$this->validate(file_get_contents('php://stdin'), $flags)]);
        }

        if (!$filenames) {
            throw new RuntimeException('Please provide a filename or pipe file content to STDIN.');
        }

        $filesInfo = [];
        foreach ($filenames as $filename) {
            if (!$this->isReadable($filename)) {
                throw new RuntimeException(\sprintf('File or directory "%s" is not readable.', $filename));
            }

            foreach ($this->getFiles($filename) as $file) {
                if (!\in_array($file->getPathname(), $excludes, true)) {
                    $filesInfo[] = $this->validate(file_get_contents($file), $flags, $file);
                }
            }
        }

        return $this->display($io, $filesInfo);
    }

    private function validate(string $content, int $flags, ?string $file = null): array
    {
        $prevErrorHandler = set_error_handler(function ($level, $message, $file, $line) use (&$prevErrorHandler) {
            if (\E_USER_DEPRECATED === $level) {
                throw new ParseException($message, $this->getParser()->getRealCurrentLineNb() + 1);
            }

            return $prevErrorHandler ? $prevErrorHandler($level, $message, $file, $line) : false;
        });

        try {
            $this->getParser()->parse($content, Yaml::PARSE_CONSTANT | $flags);
        } catch (ParseException $e) {
            return ['file' => $file, 'line' => $e->getParsedLine(), 'valid' => false, 'message' => $e->getMessage()];
        } finally {
            restore_error_handler();
        }

        return ['file' => $file, 'valid' => true];
    }

    private function display(SymfonyStyle $io, array $files): int
    {
        return match ($this->format) {
            'txt' => $this->displayTxt($io, $files),
            'json' => $this->displayJson($io, $files),
            'github' => $this->displayTxt($io, $files, true),
            default => throw new InvalidArgumentException(\sprintf('Supported formats are "%s".', implode('", "', $this->getAvailableFormatOptions()))),
        };
    }

    private function displayTxt(SymfonyStyle $io, array $filesInfo, bool $errorAsGithubAnnotations = false): int
    {
        $countFiles = \count($filesInfo);
        $erroredFiles = 0;
        $suggestTagOption = false;

        if ($errorAsGithubAnnotations) {
            $githubReporter = new GithubActionReporter($io);
        }

        foreach ($filesInfo as $info) {
            if ($info['valid'] && $this->displayCorrectFiles) {
                $io->comment('<info>OK</info>'.($info['file'] ? \sprintf(' in %s', $info['file']) : ''));
            } elseif (!$info['valid']) {
                ++$erroredFiles;
                $io->text('<error> ERROR </error>'.($info['file'] ? \sprintf(' in %s', $info['file']) : ''));
                $io->text(\sprintf('<error> >> %s</error>', $info['message']));

                if (str_contains($info['message'], 'PARSE_CUSTOM_TAGS')) {
                    $suggestTagOption = true;
                }

                if ($errorAsGithubAnnotations) {
                    $githubReporter->error($info['message'], $info['file'] ?? 'php://stdin', $info['line']);
                }
            }
        }

        if (0 === $erroredFiles) {
            $io->success(\sprintf('All %d YAML files contain valid syntax.', $countFiles));
        } else {
            $io->warning(\sprintf('%d YAML files have valid syntax and %d contain errors.%s', $countFiles - $erroredFiles, $erroredFiles, $suggestTagOption ? ' Use the --parse-tags option if you want parse custom tags.' : ''));
        }

        return min($erroredFiles, 1);
    }

    private function displayJson(SymfonyStyle $io, array $filesInfo): int
    {
        $errors = 0;

        array_walk($filesInfo, static function (&$v) use (&$errors) {
            $v['file'] = (string) $v['file'];
            if (!$v['valid']) {
                ++$errors;
            }

            if (isset($v['message']) && str_contains($v['message'], 'PARSE_CUSTOM_TAGS')) {
                $v['message'] .= ' Use the --parse-tags option if you want parse custom tags.';
            }
        });

        $io->writeln(json_encode($filesInfo, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES));

        return min($errors, 1);
    }

    private function getFiles(string $fileOrDirectory): iterable
    {
        if (is_file($fileOrDirectory)) {
            yield new \SplFileInfo($fileOrDirectory);

            return;
        }

        foreach ($this->getDirectoryIterator($fileOrDirectory) as $file) {
            if (!\in_array($file->getExtension(), ['yml', 'yaml'], true)) {
                continue;
            }

            yield $file;
        }
    }

    private function getParser(): Parser
    {
        return $this->parser ??= new Parser();
    }

    private function getDirectoryIterator(string $directory): iterable
    {
        $default = static fn ($directory) => new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        if (null !== $this->directoryIteratorProvider) {
            return ($this->directoryIteratorProvider)($directory, $default);
        }

        return $default($directory);
    }

    private function isReadable(string $fileOrDirectory): bool
    {
        $default = is_readable(...);

        if (null !== $this->isReadableProvider) {
            return ($this->isReadableProvider)($fileOrDirectory, $default);
        }

        return $default($fileOrDirectory);
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
        return ['txt', 'json', 'github'];
    }
}
