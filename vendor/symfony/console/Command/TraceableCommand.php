<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @internal
 *
 * @author Jules Pietri <jules@heahprod.com>
 */
final class TraceableCommand extends Command
{
    public readonly Command $command;
    public int $exitCode;
    public ?int $interruptedBySignal = null;
    public bool $ignoreValidation;
    public bool $isInteractive = false;
    public string $duration = 'n/a';
    public string $maxMemoryUsage = 'n/a';
    public InputInterface $input;
    public OutputInterface $output;
    /** @var array<string, mixed> */
    public array $arguments;
    /** @var array<string, mixed> */
    public array $options;
    /** @var array<string, mixed> */
    public array $interactiveInputs = [];
    public array $handledSignals = [];
    public ?array $invokableCommandInfo = null;

    public function __construct(
        Command $command,
        private readonly Stopwatch $stopwatch,
    ) {
        if ($command instanceof LazyCommand) {
            $command = $command->getCommand();
        }

        $this->command = $command;

        // prevent call to self::getDefaultDescription()
        $this->setDescription($command->getDescription());

        parent::__construct($command->getName());

        // init below enables calling {@see parent::run()}
        [$code, $processTitle, $ignoreValidationErrors] = \Closure::bind(fn () => [$this->code, $this->processTitle, $this->ignoreValidationErrors], $command, Command::class)();

        if (\is_callable($code)) {
            $this->setCode($code);
        }

        if ($processTitle) {
            parent::setProcessTitle($processTitle);
        }

        if ($ignoreValidationErrors) {
            parent::ignoreValidationErrors();
        }

        $this->ignoreValidation = $ignoreValidationErrors;
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->command->{$name}(...$arguments);
    }

    public function getSubscribedSignals(): array
    {
        return $this->command->getSubscribedSignals();
    }

    public function handleSignal(int $signal, int|false $previousExitCode = 0): int|false
    {
        $event = $this->stopwatch->start($this->getName().'.handle_signal');

        $exit = $this->command->handleSignal($signal, $previousExitCode);

        $event->stop();

        if (!isset($this->handledSignals[$signal])) {
            $this->handledSignals[$signal] = [
                'handled' => 0,
                'duration' => 0,
                'memory' => 0,
            ];
        }

        ++$this->handledSignals[$signal]['handled'];
        $this->handledSignals[$signal]['duration'] += $event->getDuration();
        $this->handledSignals[$signal]['memory'] = max(
            $this->handledSignals[$signal]['memory'],
            $event->getMemory() >> 20
        );

        return $exit;
    }

    /**
     * {@inheritdoc}
     *
     * Calling parent method is required to be used in {@see parent::run()}.
     */
    public function ignoreValidationErrors(): void
    {
        $this->ignoreValidation = true;
        $this->command->ignoreValidationErrors();

        parent::ignoreValidationErrors();
    }

    public function setApplication(?Application $application = null): void
    {
        $this->command->setApplication($application);
    }

    public function getApplication(): ?Application
    {
        return $this->command->getApplication();
    }

    public function setHelperSet(HelperSet $helperSet): void
    {
        $this->command->setHelperSet($helperSet);
    }

    public function getHelperSet(): ?HelperSet
    {
        return $this->command->getHelperSet();
    }

    public function isEnabled(): bool
    {
        return $this->command->isEnabled();
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        $this->command->complete($input, $suggestions);
    }

    /**
     * {@inheritdoc}
     *
     * Calling parent method is required to be used in {@see parent::run()}.
     */
    public function setCode(callable $code): static
    {
        if ($code instanceof InvokableCommand) {
            $r = \Closure::bind(fn () => $this->invokable, $code, InvokableCommand::class)();

            $this->invokableCommandInfo = [
                'class' => $r->getClosureScopeClass()->name,
                'file' => $r->getFileName(),
                'line' => $r->getStartLine(),
            ];

            // Pass the original callable to avoid double-wrapping in Command::setCode()
            $this->command->setCode($code->getCode());
        } else {
            $this->command->setCode($code);
        }

        return parent::setCode(function (InputInterface $input, OutputInterface $output) use ($code): int {
            $event = $this->stopwatch->start($this->getName().'.code');

            $this->exitCode = $code($input, $output);

            $event->stop();

            return $this->exitCode;
        });
    }

    /**
     * @internal
     */
    public function mergeApplicationDefinition(bool $mergeArgs = true): void
    {
        $this->command->mergeApplicationDefinition($mergeArgs);
    }

    public function setDefinition(array|InputDefinition $definition): static
    {
        $this->command->setDefinition($definition);

        return $this;
    }

    public function getDefinition(): InputDefinition
    {
        return $this->command->getDefinition();
    }

    public function getNativeDefinition(): InputDefinition
    {
        return $this->command->getNativeDefinition();
    }

    public function addArgument(string $name, ?int $mode = null, string $description = '', mixed $default = null, array|\Closure $suggestedValues = []): static
    {
        $this->command->addArgument($name, $mode, $description, $default, $suggestedValues);

        return $this;
    }

    public function addOption(string $name, string|array|null $shortcut = null, ?int $mode = null, string $description = '', mixed $default = null, array|\Closure $suggestedValues = []): static
    {
        $this->command->addOption($name, $shortcut, $mode, $description, $default, $suggestedValues);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * Calling parent method is required to be used in {@see parent::run()}.
     */
    public function setProcessTitle(string $title): static
    {
        $this->command->setProcessTitle($title);

        return parent::setProcessTitle($title);
    }

    public function setHelp(string $help): static
    {
        $this->command->setHelp($help);

        return $this;
    }

    public function getHelp(): string
    {
        return $this->command->getHelp();
    }

    public function getProcessedHelp(): string
    {
        return $this->command->getProcessedHelp();
    }

    public function getSynopsis(bool $short = false): string
    {
        return $this->command->getSynopsis($short);
    }

    public function addUsage(string $usage): static
    {
        $this->command->addUsage($usage);

        return $this;
    }

    public function getUsages(): array
    {
        return $this->command->getUsages();
    }

    public function getHelper(string $name): HelperInterface
    {
        return $this->command->getHelper($name);
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $initialArguments = $input->getArguments();
        $initialOptions = $input->getOptions();
        $event = $this->stopwatch->start($this->getName(), 'command');

        try {
            $this->exitCode = $this->command->run($input, $output);
        } finally {
            $event->stop();

            if ($output instanceof ConsoleOutputInterface && $output->isDebug()) {
                $output->getErrorOutput()->writeln((string) $event);
            }

            $this->duration = $event->getDuration().' ms';
            $this->maxMemoryUsage = ($event->getMemory() >> 20).' MiB';

            $this->arguments = $input->getArguments();
            $this->options = $input->getOptions();

            $this->extractInteractiveInputs($initialArguments, $initialOptions);
            $this->isInteractive = $this->isInteractive || $this->interactiveInputs;
        }

        return $this->exitCode;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $event = $this->stopwatch->start($this->getName().'.init', 'command');

        $this->command->initialize($input, $output);

        $event->stop();
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (!$this->isInteractive = Command::class !== (new \ReflectionMethod($this->command, 'interact'))->getDeclaringClass()->getName()) {
            return;
        }

        $event = $this->stopwatch->start($this->getName().'.interact', 'command');

        $this->command->interact($input, $output);

        $event->stop();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $event = $this->stopwatch->start($this->getName().'.execute', 'command');

        $exitCode = $this->command->execute($input, $output);

        $event->stop();

        return $exitCode;
    }

    private function extractInteractiveInputs(array $initialArguments, array $initialOptions): void
    {
        $nativeDefinition = $this->command->getNativeDefinition();

        foreach ($nativeDefinition->getArguments() as $argName => $argument) {
            if (\array_key_exists($argName, $initialArguments) && $initialArguments[$argName] === $this->arguments[$argName]) {
                continue;
            }

            $this->interactiveInputs[$argName] = $this->arguments[$argName];
        }

        foreach ($nativeDefinition->getOptions() as $optName => $option) {
            if (\array_key_exists($optName, $initialOptions) && $initialOptions[$optName] === $this->options[$optName]) {
                continue;
            }

            $this->interactiveInputs['--'.$optName] = $this->options[$optName];
        }
    }
}
