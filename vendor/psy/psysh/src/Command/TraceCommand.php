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

use Psy\Formatter\TraceFormatter;
use Psy\Input\FilterOptions;
use Psy\Output\ShellOutputAdapter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show the current stack trace.
 */
class TraceCommand extends Command
{
    protected $filter;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null)
    {
        $this->filter = new FilterOptions();

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        list($grep, $insensitive, $invert) = FilterOptions::getOptions();

        $this
            ->setName('trace')
            ->setDefinition([
                new InputOption('include-psy', 'p', InputOption::VALUE_NONE, 'Include Psy in the call stack.'),
                new InputOption('num', 'n', InputOption::VALUE_REQUIRED, 'Only include NUM lines.'),

                $grep,
                $insensitive,
                $invert,
            ])
            ->setDescription('Show the current call stack.')
            ->setHelp(
                <<<'HELP'
Show the current call stack.

Optionally, include PsySH in the call stack by passing the <info>--include-psy</info> option.

e.g.
<return>> trace -n10</return>
<return>> trace --include-psy</return>
HELP
            );
    }

    /**
     * {@inheritdoc}
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->filter->bind($input);
        $trace = $this->getBacktrace(new \Exception(), $input->getOption('num'), $input->getOption('include-psy'));
        $this->shellOutput($output)->page($trace, ShellOutputAdapter::NUMBER_LINES);

        return 0;
    }

    /**
     * Get a backtrace for an exception or error.
     *
     * Optionally limit the number of rows to include with $count, and exclude
     * Psy from the trace.
     *
     * @param \Throwable $e          The exception or error with a backtrace
     * @param int|null   $count      (default: PHP_INT_MAX)
     * @param bool       $includePsy (default: true)
     *
     * @return array Formatted stacktrace lines
     */
    protected function getBacktrace(\Throwable $e, ?int $count = null, bool $includePsy = true): array
    {
        return TraceFormatter::formatTrace($e, $this->filter, $count, $includePsy);
    }
}
