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

use Psy\CodeCleanerAware;
use Psy\ContextAware;
use Psy\Output\ShellOutputAdapter;
use Psy\Readline\ReadlineAware;
use Psy\Shell;
use Psy\VarDumper\PresenterAware;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The Psy Shell base command.
 */
abstract class Command extends BaseCommand
{
    /**
     * Sets the application instance for this command.
     *
     * @param Application|null $application An Application instance
     *
     * @api
     */
    public function setApplication(?Application $application = null): void
    {
        if ($application !== null && !$application instanceof Shell) {
            throw new \InvalidArgumentException('PsySH Commands require an instance of Psy\Shell');
        }

        parent::setApplication($application);
    }

    /**
     * getApplication, but is guaranteed to return a Shell instance.
     */
    protected function getShell(): Shell
    {
        $shell = $this->getApplication();
        if (!$shell instanceof Shell) {
            throw new \RuntimeException('PsySH Commands require an instance of Psy\Shell');
        }

        return $shell;
    }

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        if (
            $this instanceof ContextAware ||
            $this instanceof CodeCleanerAware ||
            $this instanceof PresenterAware ||
            $this instanceof ReadlineAware
        ) {
            $this->getShell()->boot($input, $output);
        }

        return parent::run($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    public function asText(): string
    {
        $messages = [
            '<comment>Usage:</comment>',
            ' '.$this->getSynopsis(),
            '',
        ];

        if ($this->getAliases()) {
            $messages[] = $this->aliasesAsText();
        }

        if ($this->getArguments()) {
            $messages[] = $this->argumentsAsText();
        }

        if ($this->getOptions()) {
            $messages[] = $this->optionsAsText();
        }

        if ($help = $this->getProcessedHelp()) {
            $messages[] = '<comment>Help:</comment>';
            $messages[] = ' '.\str_replace("\n", "\n ", $help)."\n";
        }

        return \implode("\n", $messages);
    }

    /**
     * Render help text for the current input context.
     */
    public function asTextForInput(InputInterface $input): string
    {
        return $this->asText();
    }

    /**
     * {@inheritdoc}
     */
    private function getArguments(): array
    {
        $hidden = $this->getHiddenArguments();

        return \array_filter(
            $this->getNativeDefinition()->getArguments(),
            fn ($argument) => !\in_array($argument->getName(), $hidden)
        );
    }

    /**
     * These arguments will be excluded from help output.
     *
     * @return string[]
     */
    protected function getHiddenArguments(): array
    {
        return ['command'];
    }

    /**
     * {@inheritdoc}
     */
    private function getOptions(): array
    {
        $hidden = $this->getHiddenOptions();

        return \array_filter(
            $this->getNativeDefinition()->getOptions(),
            fn ($option) => !\in_array($option->getName(), $hidden)
        );
    }

    /**
     * These options will be excluded from help output.
     *
     * @return string[]
     */
    protected function getHiddenOptions(): array
    {
        return ['verbose'];
    }

    /**
     * Format command aliases as text..
     */
    private function aliasesAsText(): string
    {
        return '<comment>Aliases:</comment> <info>'.\implode(', ', $this->getAliases()).'</info>'.\PHP_EOL;
    }

    /**
     * Format command arguments as text.
     */
    private function argumentsAsText(): string
    {
        $max = $this->getMaxWidth();
        $messages = [];

        $arguments = $this->getArguments();
        if (!empty($arguments)) {
            $messages[] = '<comment>Arguments:</comment>';
            foreach ($arguments as $argument) {
                if (null !== $argument->getDefault() && (!\is_array($argument->getDefault()) || \count($argument->getDefault()))) {
                    $default = \sprintf('<comment> (default: %s)</comment>', $this->formatDefaultValue($argument->getDefault()));
                } else {
                    $default = '';
                }

                $name = $argument->getName();
                // @phan-suppress-next-line PhanParamSuspiciousOrder - intentionally padding empty string to create spaces
                $pad = \str_pad('', $max - \strlen($name));
                // @phan-suppress-next-line PhanParamSuspiciousOrder - intentionally padding empty string to create spaces
                $description = \str_replace("\n", "\n".\str_pad('', $max + 2, ' '), $argument->getDescription());

                $messages[] = \sprintf(' <info>%s</info>%s %s%s', $name, $pad, $description, $default);
            }

            $messages[] = '';
        }

        return \implode(\PHP_EOL, $messages);
    }

    /**
     * Format options as text.
     */
    private function optionsAsText(): string
    {
        $max = $this->getMaxWidth();
        $messages = [];

        $options = $this->getOptions();
        if ($options) {
            $messages[] = '<comment>Options:</comment>';

            foreach ($options as $option) {
                if ($option->acceptValue() && null !== $option->getDefault() && (!\is_array($option->getDefault()) || \count($option->getDefault()))) {
                    $default = \sprintf('<comment> (default: %s)</comment>', $this->formatDefaultValue($option->getDefault()));
                } else {
                    $default = '';
                }

                $multiple = $option->isArray() ? '<comment> (multiple values allowed)</comment>' : '';
                // @phan-suppress-next-line PhanParamSuspiciousOrder - intentionally padding empty string to create spaces
                $description = \str_replace("\n", "\n".\str_pad('', $max + 2, ' '), $option->getDescription());

                $optionMax = $max - \strlen($option->getName()) - 2;
                $messages[] = \sprintf(
                    " <info>%s</info> %-{$optionMax}s%s%s%s",
                    '--'.$option->getName(),
                    $option->getShortcut() ? \sprintf('(-%s) ', $option->getShortcut()) : '',
                    $description,
                    $default,
                    $multiple
                );
            }

            $messages[] = '';
        }

        return \implode(\PHP_EOL, $messages);
    }

    /**
     * Calculate the maximum padding width for a set of lines.
     */
    private function getMaxWidth(): int
    {
        $max = 0;

        foreach ($this->getOptions() as $option) {
            $nameLength = \strlen($option->getName()) + 2;
            if ($option->getShortcut()) {
                $nameLength += \strlen($option->getShortcut()) + 3;
            }

            $max = \max($max, $nameLength);
        }

        foreach ($this->getArguments() as $argument) {
            $max = \max($max, \strlen($argument->getName()));
        }

        return ++$max;
    }

    /**
     * Format an option default as text.
     *
     * @param mixed $default
     */
    private function formatDefaultValue($default): string
    {
        if (\is_array($default) && $default === \array_values($default)) {
            return \sprintf("['%s']", \implode("', '", $default));
        }

        return \str_replace("\n", '', \var_export($default, true));
    }

    /**
     * Get a Table instance.
     *
     * @return Table
     */
    protected function getTable(OutputInterface $output)
    {
        $style = new TableStyle();

        // Symfony 4.1 deprecated single-argument style setters.
        if (\method_exists($style, 'setVerticalBorderChars')) {
            $style->setVerticalBorderChars(' ');
            $style->setHorizontalBorderChars('');
            $style->setCrossingChars('', '', '', '', '', '', '', '', '');
        } else {
            $style->setVerticalBorderChar(' ');
            $style->setHorizontalBorderChar('');
            $style->setCrossingChar('');
        }

        $table = new Table($output);

        return $table
            ->setRows([])
            ->setStyle($style);
    }

    /**
     * Get a ShellOutputAdapter for the given output.
     */
    protected function shellOutput(OutputInterface $output): ShellOutputAdapter
    {
        return new ShellOutputAdapter($output);
    }
}
