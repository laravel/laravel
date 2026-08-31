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

use Psy\Output\ShellOutputAdapter;
use Psy\Readline\LegacyReadline;
use Psy\Readline\Readline;
use Psy\Readline\ReadlineAware;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interact with the current code buffer.
 *
 * Shows and clears the buffer for the current multi-line expression.
 */
class BufferCommand extends Command implements ReadlineAware
{
    private ?Readline $readline = null;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('buffer')
            ->setAliases(['buf'])
            ->setDefinition([
                new InputOption('clear', '', InputOption::VALUE_NONE, 'Clear the current buffer.'),
            ])
            ->setDescription('Show (or clear) the contents of the code input buffer.')
            ->setHelp(
                <<<'HELP'
Show the contents of the code buffer for the current multi-line expression.

Optionally, clear the buffer by passing the <info>--clear</info> option.
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
        $shell = $this->getShell();
        $shellOutput = $this->shellOutput($output);
        $readline = $this->getLegacyReadline();
        $legacyBuffer = $readline->getBuffer();
        $shellBuffer = $shell->getPendingCodeBuffer();
        $buf = $legacyBuffer !== [] ? $legacyBuffer : $shellBuffer;
        if ($input->getOption('clear')) {
            $readline->clearBuffer();
            if ($shellBuffer !== []) {
                $shell->clearPendingCodeBuffer();
            }
            $shellOutput->writeln($this->formatLines($buf, 'urgent'), ShellOutputAdapter::NUMBER_LINES);
        } else {
            $shellOutput->writeln($this->formatLines($buf), ShellOutputAdapter::NUMBER_LINES);
        }

        return 0;
    }

    /**
     * Set the shell's readline implementation.
     */
    public function setReadline(Readline $readline)
    {
        $this->readline = $readline;
    }

    /**
     * A helper method for wrapping buffer lines in `<urgent>` and `<return>` formatter strings.
     *
     * @param array  $lines
     * @param string $type  (default: 'return')
     *
     * @return array Formatted strings
     */
    protected function formatLines(array $lines, string $type = 'return'): array
    {
        $template = \sprintf('<%s>%%s</%s>', $type, $type);

        return \array_map(fn ($line) => \sprintf($template, $line), $lines);
    }

    /**
     * Get the active multiline buffer from the legacy shim.
     */
    private function getLegacyReadline(): LegacyReadline
    {
        if ($this->readline instanceof LegacyReadline) {
            return $this->readline;
        }

        throw new \LogicException('BufferCommand requires LegacyReadline.');
    }
}
