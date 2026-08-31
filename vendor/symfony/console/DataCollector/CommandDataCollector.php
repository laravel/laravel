<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\DataCollector;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Debug\CliRequest;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SignalRegistry\SignalMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * @internal
 *
 * @author Jules Pietri <jules@heahprod.com>
 */
final class CommandDataCollector extends DataCollector
{
    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        if (!$request instanceof CliRequest) {
            return;
        }

        $command = $request->command;
        $application = $command->getApplication();

        $this->data = [
            'command' => $command->invokableCommandInfo ?? $this->cloneVar($command->command),
            'exit_code' => $command->exitCode,
            'interrupted_by_signal' => $command->interruptedBySignal,
            'duration' => $command->duration,
            'max_memory_usage' => $command->maxMemoryUsage,
            'verbosity_level' => match ($command->output->getVerbosity()) {
                OutputInterface::VERBOSITY_SILENT => 'silent',
                OutputInterface::VERBOSITY_QUIET => 'quiet',
                OutputInterface::VERBOSITY_NORMAL => 'normal',
                OutputInterface::VERBOSITY_VERBOSE => 'verbose',
                OutputInterface::VERBOSITY_VERY_VERBOSE => 'very verbose',
                OutputInterface::VERBOSITY_DEBUG => 'debug',
            },
            'interactive' => $command->isInteractive,
            'validate_input' => !$command->ignoreValidation,
            'enabled' => $command->isEnabled(),
            'visible' => !$command->isHidden(),
            'input' => $this->cloneVar($command->input),
            'output' => $this->cloneVar($command->output),
            'interactive_inputs' => array_map($this->cloneVar(...), $command->interactiveInputs),
            'signalable' => $command->getSubscribedSignals(),
            'handled_signals' => $command->handledSignals,
            'helper_set' => array_map($this->cloneVar(...), iterator_to_array($command->getHelperSet())),
        ];

        $baseDefinition = $application->getDefinition();

        foreach ($command->arguments as $argName => $argValue) {
            if ($baseDefinition->hasArgument($argName)) {
                $this->data['application_inputs'][$argName] = $this->cloneVar($argValue);
            } else {
                $this->data['arguments'][$argName] = $this->cloneVar($argValue);
            }
        }

        foreach ($command->options as $optName => $optValue) {
            if ($baseDefinition->hasOption($optName)) {
                $this->data['application_inputs']['--'.$optName] = $this->cloneVar($optValue);
            } else {
                $this->data['options'][$optName] = $this->cloneVar($optValue);
            }
        }
    }

    public function getName(): string
    {
        return 'command';
    }

    /**
     * @return array{
     *     class?: class-string,
     *     executor?: string,
     *     file: string,
     *     line: int,
     * }
     */
    public function getCommand(): array
    {
        if (\is_array($this->data['command'])) {
            return $this->data['command'];
        }

        $class = $this->data['command']->getType();
        $r = new \ReflectionMethod($class, 'execute');

        if (Command::class !== $r->getDeclaringClass()) {
            return [
                'executor' => $class.'::'.$r->name,
                'file' => $r->getFileName(),
                'line' => $r->getStartLine(),
            ];
        }

        $r = new \ReflectionClass($class);

        return [
            'class' => $class,
            'file' => $r->getFileName(),
            'line' => $r->getStartLine(),
        ];
    }

    public function getInterruptedBySignal(): ?string
    {
        if (isset($this->data['interrupted_by_signal'])) {
            return \sprintf('%s (%d)', SignalMap::getSignalName($this->data['interrupted_by_signal']), $this->data['interrupted_by_signal']);
        }

        return null;
    }

    public function getDuration(): string
    {
        return $this->data['duration'];
    }

    public function getMaxMemoryUsage(): string
    {
        return $this->data['max_memory_usage'];
    }

    public function getVerbosityLevel(): string
    {
        return $this->data['verbosity_level'];
    }

    public function getInteractive(): bool
    {
        return $this->data['interactive'];
    }

    public function getValidateInput(): bool
    {
        return $this->data['validate_input'];
    }

    public function getEnabled(): bool
    {
        return $this->data['enabled'];
    }

    public function getVisible(): bool
    {
        return $this->data['visible'];
    }

    public function getInput(): Data
    {
        return $this->data['input'];
    }

    public function getOutput(): Data
    {
        return $this->data['output'];
    }

    /**
     * @return Data[]
     */
    public function getArguments(): array
    {
        return $this->data['arguments'] ?? [];
    }

    /**
     * @return Data[]
     */
    public function getOptions(): array
    {
        return $this->data['options'] ?? [];
    }

    /**
     * @return Data[]
     */
    public function getApplicationInputs(): array
    {
        return $this->data['application_inputs'] ?? [];
    }

    /**
     * @return Data[]
     */
    public function getInteractiveInputs(): array
    {
        return $this->data['interactive_inputs'] ?? [];
    }

    public function getSignalable(): array
    {
        return array_map(
            static fn (int $signal): string => \sprintf('%s (%d)', SignalMap::getSignalName($signal), $signal),
            $this->data['signalable']
        );
    }

    public function getHandledSignals(): array
    {
        $keys = array_map(
            static fn (int $signal): string => \sprintf('%s (%d)', SignalMap::getSignalName($signal), $signal),
            array_keys($this->data['handled_signals'])
        );

        return array_combine($keys, array_values($this->data['handled_signals']));
    }

    /**
     * @return Data[]
     */
    public function getHelperSet(): array
    {
        return $this->data['helper_set'] ?? [];
    }

    public function reset(): void
    {
        $this->data = [];
    }
}
