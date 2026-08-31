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

use Psy\Formatter\ManualWrapper;
use Psy\Readline\Interactive\Layout\DisplayString;
use Psy\Util\Tty;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Help command.
 *
 * Lists available commands, and gives command-specific help when asked nicely.
 */
class HelpCommand extends Command
{
    private const TABLE_OVERHEAD_TWO_COLUMNS = 7;
    private const TABLE_OVERHEAD_THREE_COLUMNS = 10;
    private const MIN_DESCRIPTION_WIDTH_FOR_ALIAS_COLUMN = 40;

    private ?Command $command = null;
    private ?InputInterface $commandInput = null;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('help')
            ->setAliases(['?'])
            ->setDefinition([
                new InputArgument('command_name', InputArgument::OPTIONAL, 'The command name.', null),
            ])
            ->setDescription('Show a list of commands. Type `help [foo]` for information about [foo].')
            ->setHelp('My. How meta.');
    }

    /**
     * Helper for setting a subcommand to retrieve help for.
     *
     * @param Command $command
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;
    }

    /**
     * Helper for preserving the original input when rendering contextual help.
     */
    public function setCommandInput(InputInterface $input): void
    {
        $this->commandInput = $input;
    }

    /**
     * {@inheritdoc}
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shellOutput = $this->shellOutput($output);

        if ($this->command !== null) {
            // help for an individual command
            $shellOutput->page($this->command->asTextForInput($this->commandInput ?? $input));
            $this->command = null;
            $this->commandInput = null;
        } elseif ($name = $input->getArgument('command_name')) {
            // help for an individual command
            try {
                $cmd = $this->getApplication()->get($name);
            } catch (CommandNotFoundException $e) {
                $this->getShell()->writeException($e);
                $output->writeln('');
                $output->writeln(\sprintf(
                    '<aside>To read PHP documentation, use <return>doc %s</return></aside>',
                    $name
                ));
                $output->writeln('');

                return 1;
            }

            if (!$cmd instanceof Command) {
                throw new \RuntimeException(\sprintf('Expected Psy\Command\Command instance, got %s', \get_class($cmd)));
            }

            $shellOutput->page($cmd->asTextForInput($input));
        } else {
            $this->commandInput = null;
            $shellOutput->page(function (OutputInterface $pagedOutput): void {
                $this->renderCommandList($pagedOutput);
            });
        }

        return 0;
    }

    /**
     * Render the top-level command list with fixed command widths and a
     * conditional alias column when the terminal is wide enough.
     */
    private function renderCommandList(OutputInterface $output): void
    {
        $commands = [];

        foreach ($this->getApplication()->all() as $name => $command) {
            if ($name !== $command->getName()) {
                continue;
            }

            $commands[] = [
                'name'        => $name,
                'description' => $command->getDescription(),
                'aliasText'   => $command->getAliases()
                    ? \sprintf('<comment>Aliases:</comment> %s', \implode(', ', $command->getAliases()))
                    : '',
            ];
        }

        $nameWidth = 0;
        $aliasWidth = 0;
        $descriptionWidth = 0;
        $formatter = $output->getFormatter();
        foreach ($commands as $command) {
            $nameWidth = \max($nameWidth, DisplayString::width($command['name']));
            $aliasWidth = \max($aliasWidth, DisplayString::widthWithoutFormatting($command['aliasText'], $formatter));
            $descriptionWidth = \max($descriptionWidth, DisplayString::width($command['description']));
        }

        $terminalWidth = Tty::getWidth();
        $wrapper = new ManualWrapper();
        $table = $this->getTable($output)->setColumnWidth(0, $nameWidth);
        $descriptionWidthWithAliasColumn = $terminalWidth - $nameWidth - $aliasWidth - self::TABLE_OVERHEAD_THREE_COLUMNS;

        if ($aliasWidth > 0 && $descriptionWidthWithAliasColumn >= self::MIN_DESCRIPTION_WIDTH_FOR_ALIAS_COLUMN) {
            $descriptionColumnWidth = \min($descriptionWidth, $descriptionWidthWithAliasColumn);

            $table
                ->setColumnWidth(1, $descriptionColumnWidth)
                ->setColumnWidth(2, $aliasWidth);

            foreach ($commands as $command) {
                $table->addRow([
                    \sprintf('<info>%s</info>', $command['name']),
                    $wrapper->wrap($command['description'], $descriptionColumnWidth),
                    $command['aliasText'],
                ]);
            }

            $table->render();

            return;
        }

        $detailsWidth = \max(10, $terminalWidth - $nameWidth - self::TABLE_OVERHEAD_TWO_COLUMNS);
        $table->setColumnWidth(1, $detailsWidth);

        foreach ($commands as $command) {
            $details = $wrapper->wrap($command['description'], $detailsWidth);
            if ($command['aliasText'] !== '') {
                $details .= "\n".$wrapper->wrap($command['aliasText'], $detailsWidth);
            }

            $table->addRow([
                \sprintf('<info>%s</info>', $command['name']),
                $details,
            ]);
        }

        $table->render();
    }
}
