<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Descriptor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * Text descriptor.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 *
 * @internal
 */
class TextDescriptor extends Descriptor
{
    protected function describeInputArgument(InputArgument $argument, array $options = []): void
    {
        if (null !== $argument->getDefault() && (!\is_array($argument->getDefault()) || \count($argument->getDefault()))) {
            $default = \sprintf('<comment> [default: %s]</comment>', $this->formatDefaultValue($argument->getDefault()));
        } else {
            $default = '';
        }

        $totalWidth = $options['total_width'] ?? Helper::width($argument->getName());
        $spacingWidth = $totalWidth - \strlen($argument->getName());

        $this->writeText(\sprintf('  <info>%s</info>  %s%s%s',
            $argument->getName(),
            str_repeat(' ', $spacingWidth),
            // + 4 = 2 spaces before <info>, 2 spaces after </info>
            preg_replace('/\s*[\r\n]\s*/', "\n".str_repeat(' ', $totalWidth + 4), $argument->getDescription()),
            $default
        ), $options);
    }

    protected function describeInputOption(InputOption $option, array $options = []): void
    {
        if ($option->acceptValue() && null !== $option->getDefault() && (!\is_array($option->getDefault()) || \count($option->getDefault()))) {
            $default = \sprintf('<comment> [default: %s]</comment>', $this->formatDefaultValue($option->getDefault()));
        } else {
            $default = '';
        }

        $value = '';
        if ($option->acceptValue()) {
            $value = '='.strtoupper($option->getName());

            if ($option->isValueOptional()) {
                $value = '['.$value.']';
            }
        }

        $totalWidth = $options['total_width'] ?? $this->calculateTotalWidthForOptions([$option]);
        $synopsis = \sprintf('%s%s',
            $option->getShortcut() ? \sprintf('-%s, ', $option->getShortcut()) : '    ',
            \sprintf($option->isNegatable() ? '--%1$s|--no-%1$s' : '--%1$s%2$s', $option->getName(), $value)
        );

        $spacingWidth = $totalWidth - Helper::width($synopsis);

        $this->writeText(\sprintf('  <info>%s</info>  %s%s%s%s',
            $synopsis,
            str_repeat(' ', $spacingWidth),
            // + 4 = 2 spaces before <info>, 2 spaces after </info>
            preg_replace('/\s*[\r\n]\s*/', "\n".str_repeat(' ', $totalWidth + 4), $option->getDescription()),
            $default,
            $option->isArray() ? '<comment> (multiple values allowed)</comment>' : ''
        ), $options);
    }

    protected function describeInputDefinition(InputDefinition $definition, array $options = []): void
    {
        $totalWidth = $this->calculateTotalWidthForOptions($definition->getOptions());
        foreach ($definition->getArguments() as $argument) {
            $totalWidth = max($totalWidth, Helper::width($argument->getName()));
        }

        if ($definition->getArguments()) {
            $this->writeText('<comment>Arguments:</comment>', $options);
            $this->writeText("\n");
            foreach ($definition->getArguments() as $argument) {
                $this->describeInputArgument($argument, array_merge($options, ['total_width' => $totalWidth]));
                $this->writeText("\n");
            }
        }

        if ($definition->getArguments() && $definition->getOptions()) {
            $this->writeText("\n");
        }

        if ($definition->getOptions()) {
            $laterOptions = [];

            $this->writeText('<comment>Options:</comment>', $options);
            foreach ($definition->getOptions() as $option) {
                if (\strlen($option->getShortcut() ?? '') > 1) {
                    $laterOptions[] = $option;
                    continue;
                }
                $this->writeText("\n");
                $this->describeInputOption($option, array_merge($options, ['total_width' => $totalWidth]));
            }
            foreach ($laterOptions as $option) {
                $this->writeText("\n");
                $this->describeInputOption($option, array_merge($options, ['total_width' => $totalWidth]));
            }
        }
    }

    protected function describeCommand(Command $command, array $options = []): void
    {
        $command->mergeApplicationDefinition(false);

        if ($description = $command->getDescription()) {
            $this->writeText('<comment>Description:</comment>', $options);
            $this->writeText("\n");
            $this->writeText('  '.$description);
            $this->writeText("\n\n");
        }

        $this->writeText('<comment>Usage:</comment>', $options);
        foreach (array_merge([$command->getSynopsis(true)], $command->getAliases(), $command->getUsages()) as $usage) {
            $this->writeText("\n");
            $this->writeText('  '.OutputFormatter::escape($usage), $options);
        }
        $this->writeText("\n");

        $definition = $command->getDefinition();
        if ($definition->getOptions() || $definition->getArguments()) {
            $this->writeText("\n");
            $this->describeInputDefinition($definition, $options);
            $this->writeText("\n");
        }

        $help = $command->getProcessedHelp();
        if ($help && $help !== $description) {
            $this->writeText("\n");
            $this->writeText('<comment>Help:</comment>', $options);
            $this->writeText("\n");
            $this->writeText('  '.str_replace("\n", "\n  ", $help), $options);
            $this->writeText("\n");
        }
    }

    protected function describeApplication(Application $application, array $options = []): void
    {
        $describedNamespace = $options['namespace'] ?? null;
        $description = new ApplicationDescription($application, $describedNamespace);

        if (isset($options['raw_text']) && $options['raw_text']) {
            $width = $this->getColumnWidth($description->getCommands());

            foreach ($description->getCommands() as $command) {
                $this->writeText(\sprintf("%-{$width}s %s", $command->getName(), $command->getDescription()), $options);
                $this->writeText("\n");
            }
        } else {
            if ('' != $help = $application->getHelp()) {
                $this->writeText("$help\n\n", $options);
            }

            $this->writeText("<comment>Usage:</comment>\n", $options);
            $this->writeText("  command [options] [arguments]\n\n", $options);

            $this->describeInputDefinition(new InputDefinition($application->getDefinition()->getOptions()), $options);

            $this->writeText("\n");
            $this->writeText("\n");

            $commands = $description->getCommands();
            $namespaces = $description->getNamespaces();
            if ($describedNamespace && $namespaces) {
                // make sure all alias commands are included when describing a specific namespace
                $describedNamespaceInfo = reset($namespaces);
                foreach ($describedNamespaceInfo['commands'] as $name) {
                    $commands[$name] = $description->getCommand($name);
                }
            }

            // calculate max. width based on available commands per namespace
            $width = $this->getColumnWidth(array_merge(...array_values(array_map(static fn ($namespace) => array_intersect($namespace['commands'], array_keys($commands)), array_values($namespaces)))));

            if ($describedNamespace) {
                $this->writeText(\sprintf('<comment>Available commands for the "%s" namespace:</comment>', $describedNamespace), $options);
            } else {
                $this->writeText('<comment>Available commands:</comment>', $options);
            }

            foreach ($namespaces as $namespace) {
                $namespace['commands'] = array_filter($namespace['commands'], static fn ($name) => isset($commands[$name]));

                if (!$namespace['commands']) {
                    continue;
                }

                if (!$describedNamespace && ApplicationDescription::GLOBAL_NAMESPACE !== $namespace['id']) {
                    $this->writeText("\n");
                    $this->writeText(' <comment>'.$namespace['id'].'</comment>', $options);
                }

                foreach ($namespace['commands'] as $name) {
                    $this->writeText("\n");
                    $spacingWidth = $width - Helper::width($name);
                    $command = $commands[$name];
                    $commandAliases = $name === $command->getName() ? $this->getCommandAliasesText($command) : '';
                    $this->writeText(\sprintf('  <info>%s</info>%s%s', $name, str_repeat(' ', $spacingWidth), $commandAliases.$command->getDescription()), $options);
                }
            }

            $this->writeText("\n");
        }
    }

    private function writeText(string $content, array $options = []): void
    {
        $this->write(
            isset($options['raw_text']) && $options['raw_text'] ? strip_tags($content) : $content,
            isset($options['raw_output']) ? !$options['raw_output'] : true
        );
    }

    /**
     * Formats command aliases to show them in the command description.
     */
    private function getCommandAliasesText(Command $command): string
    {
        $text = '';
        $aliases = $command->getAliases();

        if ($aliases) {
            $text = '['.implode('|', $aliases).'] ';
        }

        return $text;
    }

    /**
     * Formats input option/argument default value.
     */
    private function formatDefaultValue(mixed $default): string
    {
        if (\INF === $default) {
            return 'INF';
        }

        if (\is_string($default)) {
            $default = OutputFormatter::escape($default);
        } elseif (\is_array($default)) {
            foreach ($default as $key => $value) {
                if (\is_string($value)) {
                    $default[$key] = OutputFormatter::escape($value);
                }
            }
        }

        return str_replace('\\\\', '\\', json_encode($default, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param array<Command|string> $commands
     */
    private function getColumnWidth(array $commands): int
    {
        $widths = [];

        foreach ($commands as $command) {
            if ($command instanceof Command) {
                $widths[] = Helper::width($command->getName());
                foreach ($command->getAliases() as $alias) {
                    $widths[] = Helper::width($alias);
                }
            } else {
                $widths[] = Helper::width($command);
            }
        }

        return $widths ? max($widths) + 2 : 0;
    }

    /**
     * @param InputOption[] $options
     */
    private function calculateTotalWidthForOptions(array $options): int
    {
        $totalWidth = 0;
        foreach ($options as $option) {
            // "-" + shortcut + ", --" + name
            $nameLength = 1 + max(Helper::width($option->getShortcut()), 1) + 4 + Helper::width($option->getName());
            if ($option->isNegatable()) {
                $nameLength += 6 + Helper::width($option->getName()); // |--no- + name
            } elseif ($option->acceptValue()) {
                $valueLength = 1 + Helper::width($option->getName()); // = + value
                $valueLength += $option->isValueOptional() ? 2 : 0; // [ + ]

                $nameLength += $valueLength;
            }
            $totalWidth = max($totalWidth, $nameLength);
        }

        return $totalWidth;
    }
}
