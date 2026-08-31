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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show runtime-configurable PsySH settings and their current values.
 */
class ConfigListCommand extends AbstractConfigCommand
{
    protected function configure(): void
    {
        $this
            ->setName('config-list')
            ->setDescription('Show runtime-configurable PsySH settings and their current values.');
    }

    public function asText(): string
    {
        return \implode("\n", [
            '<comment>Usage:</comment>',
            ' config list',
            '',
            '<comment>Help:</comment>',
            ' Show runtime-configurable PsySH settings and their current values.',
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = $this->getTable($output);

        foreach ($this->getOptions() as $option) {
            $table->addRow([
                $this->formatOptionName($option['name']),
                $this->formatValue($option['getter']()),
            ]);
        }

        $table->render();

        return 0;
    }
}
