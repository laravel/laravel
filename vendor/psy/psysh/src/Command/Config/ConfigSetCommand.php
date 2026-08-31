<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command\Config;

use Psy\Input\CodeArgument;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Update a runtime-configurable PsySH setting for the current session.
 */
class ConfigSetCommand extends AbstractConfigCommand
{
    protected function configure(): void
    {
        $this
            ->setName('config-set')
            ->setDefinition([
                new InputArgument('key', InputArgument::OPTIONAL, 'Runtime-configurable option to update.'),
                new CodeArgument('value', CodeArgument::OPTIONAL, 'New runtime value for the selected option.'),
            ])
            ->setDescription('Update one runtime-configurable PsySH setting for the current session.');
    }

    public function asText(): string
    {
        return \implode("\n", [
            '<comment>Usage:</comment>',
            ' config set \\<key> \\<value>',
            '',
            '<comment>Help:</comment>',
            ' Set a runtime-configurable PsySH setting for the current session.',
            '',
            '<comment>Examples:</comment>',
            ' <return>>>> config set verbosity debug</return>',
            ' <return>>>> config set pager off</return>',
            ' <return>>>> config set \\<key> --help</return>',
            '',
            '<comment>Supported Options:</comment>',
            $this->renderSettableKeys(),
        ]);
    }

    public function asTextForInput(InputInterface $input): string
    {
        $key = $input->getArgument('key');

        if ($key === null) {
            return $this->asText();
        }

        $option = $this->getOption((string) $key);

        if ($option === null) {
            return $this->asText();
        }

        return \implode("\n", [
            '<comment>Usage:</comment>',
            \sprintf(' config set %s \\<value>', $option['name']),
            '',
            '<comment>Help:</comment>',
            \sprintf(' Set %s for the current session.', $this->formatOptionName($option['name'])),
            '',
            '<comment>Accepted Values:</comment>',
            ' '.$this->formatAcceptedValues($option),
            '',
            '<comment>Current Value:</comment>',
            ' '.$this->formatValue($option['getter']()),
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = $input->getArgument('key');
        if ($key === null) {
            throw new \InvalidArgumentException('Please specify a runtime-configurable option to update.');
        }

        $option = $this->getOption($key);
        if ($option === null) {
            $output->writeln(\sprintf('<error>%s</error>', $this->unsupportedMessage((string) $key)));

            return 1;
        }

        $rawValue = $input->getArgument('value');
        if ($rawValue === null) {
            throw new \InvalidArgumentException(\sprintf('Please specify a value for `%s`. Accepted values: %s', $option['name'], $this->formatAcceptedValues($option)));
        }

        try {
            $value = $option['parser']((string) $rawValue);
            $changed = $option['setter']($value);
        } catch (\InvalidArgumentException $e) {
            $output->writeln(\sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }

        if ($option['refresh'] && $changed !== false) {
            $this->getShell()->applyRuntimeConfigChange($option['name']);
        }

        $output->writeln(\sprintf(
            '<info>%s</info> = <return>%s</return>',
            $option['name'],
            $this->formatValue($option['getter']())
        ));

        return 0;
    }

    private function renderSettableKeys(): string
    {
        $lines = [];

        foreach ($this->getOptions() as $option) {
            $lines[] = \sprintf(' %s (%s)', $this->formatOptionName($option['name']), $this->formatAcceptedValues($option));
        }

        return \implode("\n", $lines);
    }
}
