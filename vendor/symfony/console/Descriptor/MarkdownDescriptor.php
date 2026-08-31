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
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Markdown descriptor.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 *
 * @internal
 */
class MarkdownDescriptor extends Descriptor
{
    public function describe(OutputInterface $output, object $object, array $options = []): void
    {
        $decorated = $output->isDecorated();
        $output->setDecorated(false);

        parent::describe($output, $object, $options);

        $output->setDecorated($decorated);
    }

    protected function write(string $content, bool $decorated = true): void
    {
        parent::write($content, $decorated);
    }

    protected function describeInputArgument(InputArgument $argument, array $options = []): void
    {
        $this->write(
            '#### `'.($argument->getName() ?: '<none>')."`\n\n"
            .($argument->getDescription() ? preg_replace('/\s*[\r\n]\s*/', "\n", $argument->getDescription())."\n\n" : '')
            .'* Is required: '.($argument->isRequired() ? 'yes' : 'no')."\n"
            .'* Is array: '.($argument->isArray() ? 'yes' : 'no')."\n"
            .'* Default: `'.str_replace("\n", '', var_export($argument->getDefault(), true)).'`'
        );
    }

    protected function describeInputOption(InputOption $option, array $options = []): void
    {
        $name = '--'.$option->getName();
        if ($option->isNegatable()) {
            $name .= '|--no-'.$option->getName();
        }
        if ($option->getShortcut()) {
            $name .= '|-'.str_replace('|', '|-', $option->getShortcut()).'';
        }

        $this->write(
            '#### `'.$name.'`'."\n\n"
            .($option->getDescription() ? preg_replace('/\s*[\r\n]\s*/', "\n", $option->getDescription())."\n\n" : '')
            .'* Accept value: '.($option->acceptValue() ? 'yes' : 'no')."\n"
            .'* Is value required: '.($option->isValueRequired() ? 'yes' : 'no')."\n"
            .'* Is multiple: '.($option->isArray() ? 'yes' : 'no')."\n"
            .'* Is negatable: '.($option->isNegatable() ? 'yes' : 'no')."\n"
            .'* Default: `'.str_replace("\n", '', var_export($option->getDefault(), true)).'`'
        );
    }

    protected function describeInputDefinition(InputDefinition $definition, array $options = []): void
    {
        if ($showArguments = \count($definition->getArguments()) > 0) {
            $this->write('### Arguments');
            foreach ($definition->getArguments() as $argument) {
                $this->write("\n\n");
                $this->describeInputArgument($argument);
            }
        }

        if (\count($definition->getOptions()) > 0) {
            if ($showArguments) {
                $this->write("\n\n");
            }

            $this->write('### Options');
            foreach ($definition->getOptions() as $option) {
                $this->write("\n\n");
                $this->describeInputOption($option);
            }
        }
    }

    protected function describeCommand(Command $command, array $options = []): void
    {
        if ($options['short'] ?? false) {
            $this->write(
                '`'.$command->getName()."`\n"
                .str_repeat('-', Helper::width($command->getName()) + 2)."\n\n"
                .($command->getDescription() ? $command->getDescription()."\n\n" : '')
                .'### Usage'."\n\n"
                .array_reduce($command->getAliases(), static fn ($carry, $usage) => $carry.'* `'.$usage.'`'."\n")
            );

            return;
        }

        $command->mergeApplicationDefinition(false);

        $this->write(
            '`'.$command->getName()."`\n"
            .str_repeat('-', Helper::width($command->getName()) + 2)."\n\n"
            .($command->getDescription() ? $command->getDescription()."\n\n" : '')
            .'### Usage'."\n\n"
            .array_reduce(array_merge([$command->getSynopsis()], $command->getAliases(), $command->getUsages()), static fn ($carry, $usage) => $carry.'* `'.$usage.'`'."\n")
        );

        if ($help = $command->getProcessedHelp()) {
            $this->write("\n");
            $this->write($help);
        }

        $definition = $command->getDefinition();
        if ($definition->getOptions() || $definition->getArguments()) {
            $this->write("\n\n");
            $this->describeInputDefinition($definition);
        }
    }

    protected function describeApplication(Application $application, array $options = []): void
    {
        $describedNamespace = $options['namespace'] ?? null;
        $description = new ApplicationDescription($application, $describedNamespace);
        $title = $this->getApplicationTitle($application);

        $this->write($title."\n".str_repeat('=', Helper::width($title)));

        foreach ($description->getNamespaces() as $namespace) {
            if (ApplicationDescription::GLOBAL_NAMESPACE !== $namespace['id']) {
                $this->write("\n\n");
                $this->write('**'.$namespace['id'].':**');
            }

            $this->write("\n\n");
            $this->write(implode("\n", array_map(static fn ($commandName) => \sprintf('* [`%s`](#%s)', $commandName, str_replace(':', '', $description->getCommand($commandName)->getName())), $namespace['commands'])));
        }

        foreach ($description->getCommands() as $command) {
            $this->write("\n\n");
            $this->describeCommand($command, $options);
        }
    }

    private function getApplicationTitle(Application $application): string
    {
        if ('UNKNOWN' !== $application->getName()) {
            if ('UNKNOWN' !== $application->getVersion()) {
                return \sprintf('%s %s', $application->getName(), $application->getVersion());
            }

            return $application->getName();
        }

        return 'Console Tool';
    }
}
