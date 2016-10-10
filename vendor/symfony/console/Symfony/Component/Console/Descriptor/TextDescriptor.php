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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * Text descriptor.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class TextDescriptor extends Descriptor
{
    /**
     * {@inheritdoc}
     */
    protected function describeInputArgument(InputArgument $argument, array $options = array())
    {
        if (null !== $argument->getDefault() && (!is_array($argument->getDefault()) || count($argument->getDefault()))) {
            $default = sprintf('<comment> (default: %s)</comment>', $this->formatDefaultValue($argument->getDefault()));
        } else {
            $default = '';
        }

        $nameWidth = isset($options['name_width']) ? $options['name_width'] : strlen($argument->getName());

        $this->writeText(sprintf(" <info>%-${nameWidth}s</info> %s%s",
            $argument->getName(),
            str_replace("\n", "\n".str_repeat(' ', $nameWidth + 2), $argument->getDescription()),
            $default
        ), $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function describeInputOption(InputOption $option, array $options = array())
    {
        if ($option->acceptValue() && null !== $option->getDefault() && (!is_array($option->getDefault()) || count($option->getDefault()))) {
            $default = sprintf('<comment> (default: %s)</comment>', $this->formatDefaultValue($option->getDefault()));
        } else {
            $default = '';
        }

        $nameWidth = isset($options['name_width']) ? $options['name_width'] : strlen($option->getName());
        $nameWithShortcutWidth = $nameWidth - strlen($option->getName()) - 2;

        $this->writeText(sprintf(" <info>%s</info> %-${nameWithShortcutWidth}s%s%s%s",
            '--'.$option->getName(),
            $option->getShortcut() ? sprintf('(-%s) ', $option->getShortcut()) : '',
            str_replace("\n", "\n".str_repeat(' ', $nameWidth + 2), $option->getDescription()),
            $default,
            $option->isArray() ? '<comment> (multiple values allowed)</comment>' : ''
        ), $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function describeInputDefinition(InputDefinition $definition, array $options = array())
    {
        $nameWidth = 0;
        foreach ($definition->getOptions() as $option) {
            $nameLength = strlen($option->getName()) + 2;
            if ($option->getShortcut()) {
                $nameLength += strlen($option->getShortcut()) + 3;
            }
            $nameWidth = max($nameWidth, $nameLength);
        }
        foreach ($definition->getArguments() as $argument) {
            $nameWidth = max($nameWidth, strlen($argument->getName()));
        }
        ++$nameWidth;

        if ($definition->getArguments()) {
            $this->writeText('<comment>Arguments:</comment>', $options);
            $this->writeText("\n");
            foreach ($definition->getArguments() as $argument) {
                $this->describeInputArgument($argument, array_merge($options, array('name_width' => $nameWidth)));
                $this->writeText("\n");
            }
        }

        if ($definition->getArguments() && $definition->getOptions()) {
            $this->writeText("\n");
        }

        if ($definition->getOptions()) {
            $this->writeText('<comment>Options:</comment>', $options);
            $this->writeText("\n");
            foreach ($definition->getOptions() as $option) {
                $this->describeInputOption($option, array_merge($options, array('name_width' => $nameWidth)));
                $this->writeText("\n");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function describeCommand(Command $command, array $options = array())
    {
        $command->getSynopsis();
        $command->mergeApplicationDefinition(false);

        $this->writeText('<comment>Usage:</comment>', $options);
        $this->writeText("\n");
        $this->writeText(' '.$command->getSynopsis(), $options);
        $this->writeText("\n");

        if (count($command->getAliases()) > 0) {
            $this->writeText("\n");
            $this->writeText('<comment>Aliases:</comment> <info>'.implode(', ', $command->getAliases()).'</info>', $options);
        }

        if ($definition = $command->getNativeDefinition()) {
            $this->writeText("\n");
            $this->describeInputDefinition($definition, $options);
        }

        $this->writeText("\n");

        if ($help = $command->getProcessedHelp()) {
            $this->writeText('<comment>Help:</comment>', $options);
            $this->writeText("\n");
            $this->writeText(' '.str_replace("\n", "\n ", $help), $options);
            $this->writeText("\n");
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function describeApplication(Application $application, array $options = array())
    {
        $describedNamespace = isset($options['namespace']) ? $options['namespace'] : null;
        $description = new ApplicationDescription($application, $describedNamespace);

        if (isset($options['raw_text']) && $options['raw_text']) {
            $width = $this->getColumnWidth($description->getCommands());

            foreach ($description->getCommands() as $command) {
                $this->writeText(sprintf("%-${width}s %s", $command->getName(), $command->getDescription()), $options);
                $this->writeText("\n");
            }
        } else {
            $width = $this->getColumnWidth($description->getCommands());

            $this->writeText($application->getHelp(), $options);
            $this->writeText("\n\n");

            if ($describedNamespace) {
                $this->writeText(sprintf("<comment>Available commands for the \"%s\" namespace:</comment>", $describedNamespace), $options);
            } else {
                $this->writeText('<comment>Available commands:</comment>', $options);
            }

            // add commands by namespace
            foreach ($description->getNamespaces() as $namespace) {
                if (!$describedNamespace && ApplicationDescription::GLOBAL_NAMESPACE !== $namespace['id']) {
                    $this->writeText("\n");
                    $this->writeText('<comment>'.$namespace['id'].'</comment>', $options);
                }

                foreach ($namespace['commands'] as $name) {
                    $this->writeText("\n");
                    $this->writeText(sprintf("  <info>%-${width}s</info> %s", $name, $description->getCommand($name)->getDescription()), $options);
                }
            }

            $this->writeText("\n");
        }
    }

    /**
     * {@inheritdoc}
     */
    private function writeText($content, array $options = array())
    {
        $this->write(
            isset($options['raw_text']) && $options['raw_text'] ? strip_tags($content) : $content,
            isset($options['raw_output']) ? !$options['raw_output'] : true
        );
    }

    /**
     * Formats input option/argument default value.
     *
     * @param mixed $default
     *
     * @return string
     */
    private function formatDefaultValue($default)
    {
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            return str_replace('\/', '/', json_encode($default));
        }

        return json_encode($default, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param Command[] $commands
     *
     * @return int
     */
    private function getColumnWidth(array $commands)
    {
        $width = 0;
        foreach ($commands as $command) {
            $width = strlen($command->getName()) > $width ? strlen($command->getName()) : $width;
        }

        return $width + 2;
    }
}
