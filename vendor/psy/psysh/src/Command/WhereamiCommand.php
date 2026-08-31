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
use Psy\Formatter\CodeFormatter;
use Psy\Shell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show the context of where you opened the debugger.
 */
class WhereamiCommand extends Command
{
    private array $backtrace;

    public function __construct()
    {
        $this->backtrace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('whereami')
            ->setDefinition([
                new InputOption('num', 'n', InputOption::VALUE_OPTIONAL, 'Number of lines before and after.', '5'),
                new InputOption('file', 'f|a', InputOption::VALUE_NONE, 'Show the full source for the current file.'),
            ])
            ->setDescription('Show where you are in the code.')
            ->setHelp(
                <<<'HELP'
Show where you are in the code.

Optionally, include the number of lines before and after you want to display,
or --file for the whole file.

e.g.
<return>> whereami </return>
<return>> whereami -n10</return>
<return>> whereami --file</return>
HELP
            );
    }

    /**
     * Obtains the correct stack frame in the full backtrace.
     *
     * @return array
     */
    protected function trace(): array
    {
        foreach (\array_reverse($this->backtrace) as $stackFrame) {
            if ($this->isDebugCall($stackFrame)) {
                return $stackFrame;
            }
        }

        return \end($this->backtrace);
    }

    private static function isDebugCall(array $stackFrame): bool
    {
        $class = isset($stackFrame['class']) ? $stackFrame['class'] : null;
        $function = isset($stackFrame['function']) ? $stackFrame['function'] : null;

        return ($class === null && $function === 'Psy\\debug') ||
            ($class === Shell::class && \in_array($function, ['__construct', 'debug']));
    }

    /**
     * Determine the file and line based on the specific backtrace.
     *
     * @return array
     */
    protected function fileInfo(): array
    {
        $stackFrame = $this->trace();
        if (\preg_match('/eval\(/', $stackFrame['file'])) {
            \preg_match_all('/([^\(]+)\((\d+)/', $stackFrame['file'], $matches);
            $file = $matches[1][0];
            $line = (int) $matches[2][0];
        } else {
            $file = $stackFrame['file'];
            $line = $stackFrame['line'];
        }

        return \compact('file', 'line');
    }

    /**
     * {@inheritdoc}
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shellOutput = $this->shellOutput($output);

        $info = $this->fileInfo();
        $num = $input->getOption('num');
        $lineNum = $info['line'];
        $startLine = \max($lineNum - $num, 1);
        $endLine = $lineNum + $num;
        $code = \file_get_contents($info['file']);

        if ($input->getOption('file')) {
            $startLine = 1;
            $endLine = null;
        }

        $shellOutput->startPaging();

        $output->writeln(\sprintf('From <info>%s:%s</info>:', ConfigPaths::prettyPath($info['file']), $lineNum));
        $output->write(CodeFormatter::formatCode($code, $startLine, $endLine, $lineNum), false);

        $shellOutput->stopPaging();

        return 0;
    }
}
