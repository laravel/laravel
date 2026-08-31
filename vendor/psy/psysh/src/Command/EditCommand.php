<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command;

use Psy\ConfigPaths;
use Psy\Context;
use Psy\ContextAware;
use Psy\Util\Tty;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EditCommand extends Command implements ContextAware
{
    private string $runtimeDir = '';
    private Context $context;

    /**
     * Constructor.
     *
     * @param string      $runtimeDir The directory to use for temporary files
     * @param string|null $name       The name of the command; passing null means it must be set in configure()
     *
     * @throws \Symfony\Component\Console\Exception\LogicException When the command name is empty
     */
    public function __construct($runtimeDir, $name = null)
    {
        parent::__construct($name);

        $this->runtimeDir = $runtimeDir;
    }

    protected function configure(): void
    {
        $this
            ->setName('edit')
            ->setDefinition([
                new InputArgument('file', InputArgument::OPTIONAL, 'The file to open for editing. If this is not given, edits a temporary file.', null),
                new InputOption(
                    'exec',
                    'e',
                    InputOption::VALUE_NONE,
                    'Execute the file content after editing. This is the default when a file name argument is not given.',
                    null
                ),
                new InputOption(
                    'no-exec',
                    'E',
                    InputOption::VALUE_NONE,
                    'Do not execute the file content after editing. This is the default when a file name argument is given.',
                    null
                ),
            ])
            ->setDescription('Open an external editor. Afterwards, get produced code in input buffer.')
            ->setHelp('Set the EDITOR environment variable to something you\'d like to use.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @throws \InvalidArgumentException when both exec and no-exec flags are given or if a given variable is not found in the current context
     * @throws \UnexpectedValueException if file_get_contents on the edited file returns false instead of a string
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('exec') &&
            $input->getOption('no-exec')) {
            throw new \InvalidArgumentException('The --exec and --no-exec flags are mutually exclusive');
        }

        $filePath = $this->extractFilePath($input->getArgument('file'));

        $execute = $this->shouldExecuteFile(
            $input->getOption('exec'),
            $input->getOption('no-exec'),
            $filePath
        );

        $shouldRemoveFile = false;

        if ($filePath === null) {
            ConfigPaths::ensureDir($this->runtimeDir);
            $filePath = \tempnam($this->runtimeDir, 'psysh-edit-command');
            $shouldRemoveFile = true;
        }

        $editedContent = $this->editFile($filePath, $shouldRemoveFile);

        if ($execute) {
            $this->getShell()->addInput($editedContent);
        }

        return 0;
    }

    /**
     * @param bool        $execOption
     * @param bool        $noExecOption
     * @param string|null $filePath
     */
    private function shouldExecuteFile(bool $execOption, bool $noExecOption, ?string $filePath = null): bool
    {
        if ($execOption) {
            return true;
        }

        if ($noExecOption) {
            return false;
        }

        // By default, code that is edited is executed if there was no given input file path
        return $filePath === null;
    }

    /**
     * @param string|null $fileArgument
     *
     * @return string|null The file path to edit, null if the input was null, or the value of the referenced variable
     *
     * @throws \InvalidArgumentException If the variable is not found in the current context
     */
    private function extractFilePath(?string $fileArgument = null)
    {
        // If the file argument was a variable, get it from the context
        if ($fileArgument !== null &&
            $fileArgument !== '' &&
            $fileArgument[0] === '$') {
            $fileArgument = $this->context->get(\preg_replace('/^\$/', '', $fileArgument));
        }

        return $fileArgument;
    }

    /**
     * @param string $filePath
     * @param bool   $shouldRemoveFile
     *
     * @throws \UnexpectedValueException if file_get_contents on $filePath returns false instead of a string
     */
    private function editFile(string $filePath, bool $shouldRemoveFile): string
    {
        $escapedFilePath = \escapeshellarg($filePath);
        $editor = (isset($_SERVER['EDITOR']) && $_SERVER['EDITOR']) ? $_SERVER['EDITOR'] : 'nano';

        // Enable signal characters so Ctrl-C can interrupt the editor.
        // PsySH's interactive readline disables isig at the prompt, but
        // the editor needs it to handle signals properly.
        $originalStty = null;
        if (Tty::supportsStty()) {
            $originalStty = \trim((string) @\shell_exec('stty -g 2>/dev/null'));
            @\shell_exec('stty isig 2>/dev/null');
        }

        $pipes = [];
        $proc = \proc_open("{$editor} {$escapedFilePath}", [\STDIN, \STDOUT, \STDERR], $pipes);

        // Ignore SIGINT in PsySH while the editor is running. The editor
        // handles ctrl-c itself; we just need to not die when the signal
        // is delivered to our process group. Set this after proc_open so
        // the editor inherits default signal handling.
        if (\function_exists('pcntl_signal')) {
            \pcntl_signal(\SIGINT, \SIG_IGN);
        }

        try {
            \proc_close($proc);
        } finally {
            if (\function_exists('pcntl_signal')) {
                \pcntl_signal(\SIGINT, \SIG_DFL);
            }

            if ($originalStty === null) {
                // nothing to restore
            } elseif ($originalStty === '') {
                @\shell_exec('stty -isig 2>/dev/null');
            } else {
                @\shell_exec('stty '.\escapeshellarg($originalStty).' 2>/dev/null');
            }
        }

        $editedContent = @\file_get_contents($filePath);

        if ($shouldRemoveFile) {
            @\unlink($filePath);
        }

        if ($editedContent === false) {
            throw new \UnexpectedValueException("Reading {$filePath} returned false");
        }

        return $editedContent;
    }

    /**
     * Set the Context reference.
     *
     * @param Context $context
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }
}
