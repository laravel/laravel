<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\CompleteCommand;
use Symfony\Component\Console\Command\DumpCompletionCommand;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\LazyCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Event\ConsoleAlarmEvent;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleSignalEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Exception\NamespaceNotFoundException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SignalRegistry\SignalRegistry;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * An Application is the container for a collection of commands.
 *
 * It is the main entry point of a Console application.
 *
 * This class is optimized for a standard CLI environment.
 *
 * Usage:
 *
 *     $app = new Application('myapp', '1.0 (stable)');
 *     $app->addCommand(new SimpleCommand());
 *     $app->run();
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Application implements ResetInterface
{
    private array $commands = [];
    private bool $wantHelps = false;
    private ?Command $runningCommand = null;
    private ?CommandLoaderInterface $commandLoader = null;
    private bool $catchExceptions = true;
    private bool $catchErrors = false;
    private bool $autoExit = true;
    private InputDefinition $definition;
    private HelperSet $helperSet;
    private ?EventDispatcherInterface $dispatcher = null;
    private Terminal $terminal;
    private string $defaultCommand;
    private bool $singleCommand = false;
    private bool $initialized = false;
    private ?SignalRegistry $signalRegistry = null;
    private array $signalsToDispatchEvent = [];
    private ?int $alarmInterval = null;

    public function __construct(
        private string $name = 'UNKNOWN',
        private string $version = 'UNKNOWN',
    ) {
        $this->terminal = new Terminal();
        $this->defaultCommand = 'list';
        if (\defined('SIGINT') && SignalRegistry::isSupported()) {
            $this->signalRegistry = new SignalRegistry();
            $this->signalsToDispatchEvent = [\SIGINT, \SIGQUIT, \SIGTERM, \SIGUSR1, \SIGUSR2, \SIGALRM];
        }
    }

    /**
     * @final
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    public function setCommandLoader(CommandLoaderInterface $commandLoader): void
    {
        $this->commandLoader = $commandLoader;
    }

    public function getSignalRegistry(): SignalRegistry
    {
        if (!$this->signalRegistry) {
            throw new RuntimeException('Signals are not supported. Make sure that the "pcntl" extension is installed and that "pcntl_*" functions are not disabled by your php.ini\'s "disable_functions" directive.');
        }

        return $this->signalRegistry;
    }

    public function setSignalsToDispatchEvent(int ...$signalsToDispatchEvent): void
    {
        $this->signalsToDispatchEvent = $signalsToDispatchEvent;
    }

    /**
     * Sets the interval to schedule a SIGALRM signal in seconds.
     */
    public function setAlarmInterval(?int $seconds): void
    {
        $this->alarmInterval = $seconds;
        $this->scheduleAlarm();
    }

    /**
     * Gets the interval in seconds on which a SIGALRM signal is dispatched.
     */
    public function getAlarmInterval(): ?int
    {
        return $this->alarmInterval;
    }

    private function scheduleAlarm(): void
    {
        if (null !== $this->alarmInterval) {
            $this->getSignalRegistry()->scheduleAlarm($this->alarmInterval);
        }
    }

    /**
     * Runs the current application.
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws \Exception When running fails. Bypass this when {@link setCatchExceptions()}.
     */
    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        if (\function_exists('putenv')) {
            @putenv('LINES='.$this->terminal->getHeight());
            @putenv('COLUMNS='.$this->terminal->getWidth());
        }

        $input ??= new ArgvInput();
        $output ??= new ConsoleOutput();

        $renderException = function (\Throwable $e) use ($output) {
            if ($output instanceof ConsoleOutputInterface) {
                $this->renderThrowable($e, $output->getErrorOutput());
            } else {
                $this->renderThrowable($e, $output);
            }
        };
        if ($phpHandler = set_exception_handler($renderException)) {
            restore_exception_handler();
            if (!\is_array($phpHandler) || !$phpHandler[0] instanceof ErrorHandler) {
                $errorHandler = true;
            } elseif ($errorHandler = $phpHandler[0]->setExceptionHandler($renderException)) {
                $phpHandler[0]->setExceptionHandler($errorHandler);
            }
        }

        $empty = new \stdClass();
        $prevShellVerbosity = [$_ENV['SHELL_VERBOSITY'] ?? $empty, $_SERVER['SHELL_VERBOSITY'] ?? $empty, getenv('SHELL_VERBOSITY')];

        try {
            $this->configureIO($input, $output);

            $exitCode = $this->doRun($input, $output);
        } catch (\Throwable $e) {
            if ($e instanceof \Exception && !$this->catchExceptions) {
                throw $e;
            }
            if (!$e instanceof \Exception && !$this->catchErrors) {
                throw $e;
            }

            $renderException($e);

            $exitCode = $e->getCode();
            if (is_numeric($exitCode)) {
                $exitCode = (int) $exitCode;
                if ($exitCode <= 0) {
                    $exitCode = 1;
                }
            } else {
                $exitCode = 1;
            }
        } finally {
            // if the exception handler changed, keep it
            // otherwise, unregister $renderException
            if (!$phpHandler) {
                if (set_exception_handler($renderException) === $renderException) {
                    restore_exception_handler();
                }
                restore_exception_handler();
            } elseif (!$errorHandler) {
                $finalHandler = $phpHandler[0]->setExceptionHandler(null);
                if ($finalHandler !== $renderException) {
                    $phpHandler[0]->setExceptionHandler($finalHandler);
                }
            }

            // SHELL_VERBOSITY is set by Application::configureIO so we need to unset/reset it
            // to its previous value to avoid one command verbosity to spread to other commands
            if ($empty === $_ENV['SHELL_VERBOSITY'] = $prevShellVerbosity[0]) {
                unset($_ENV['SHELL_VERBOSITY']);
            }
            if ($empty === $_SERVER['SHELL_VERBOSITY'] = $prevShellVerbosity[1]) {
                unset($_SERVER['SHELL_VERBOSITY']);
            }
            if (\function_exists('putenv')) {
                @putenv('SHELL_VERBOSITY'.(false === ($prevShellVerbosity[2] ?? false) ? '' : '='.$prevShellVerbosity[2]));
            }
        }

        if ($this->autoExit) {
            if ($exitCode > 255) {
                $exitCode = 255;
            }

            exit($exitCode);
        }

        return $exitCode;
    }

    /**
     * Runs the current application.
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        if (true === $input->hasParameterOption(['--version', '-V'], true)) {
            $output->writeln($this->getLongVersion());

            return 0;
        }

        try {
            // Makes ArgvInput::getFirstArgument() able to distinguish an option from an argument.
            $input->bind($this->getDefinition());
        } catch (ExceptionInterface) {
            // Errors must be ignored, full binding/validation happens later when the command is known.
        }

        $name = $this->getCommandName($input);
        if (true === $input->hasParameterOption(['--help', '-h'], true)) {
            if (!$name) {
                $name = 'help';
                $input = new ArrayInput(['command_name' => $this->defaultCommand]);
            } else {
                $this->wantHelps = true;
            }
        }

        if (!$name) {
            $name = $this->defaultCommand;
            $definition = $this->getDefinition();
            $definition->setArguments(array_merge(
                $definition->getArguments(),
                [
                    'command' => new InputArgument('command', InputArgument::OPTIONAL, $definition->getArgument('command')->getDescription(), $name),
                ]
            ));
        }

        try {
            $this->runningCommand = null;
            // the command name MUST be the first element of the input
            $command = $this->find($name);
        } catch (\Throwable $e) {
            if (($e instanceof CommandNotFoundException && !$e instanceof NamespaceNotFoundException) && 1 === \count($alternatives = $e->getAlternatives()) && $input->isInteractive()) {
                $alternative = $alternatives[0];

                $style = new SymfonyStyle($input, $output);
                $output->writeln('');
                $formattedBlock = (new FormatterHelper())->formatBlock(\sprintf('Command "%s" is not defined.', $name), 'error', true);
                $output->writeln($formattedBlock);
                if (!$style->confirm(\sprintf('Do you want to run "%s" instead? ', $alternative), false)) {
                    if (null !== $this->dispatcher) {
                        $event = new ConsoleErrorEvent($input, $output, $e);
                        $this->dispatcher->dispatch($event, ConsoleEvents::ERROR);

                        return $event->getExitCode();
                    }

                    return 1;
                }

                $command = $this->find($alternative);
            } else {
                if (null !== $this->dispatcher) {
                    $event = new ConsoleErrorEvent($input, $output, $e);
                    $this->dispatcher->dispatch($event, ConsoleEvents::ERROR);

                    if (0 === $event->getExitCode()) {
                        return 0;
                    }

                    $e = $event->getError();
                }

                try {
                    if ($e instanceof CommandNotFoundException && $namespace = $this->findNamespace($name)) {
                        $helper = new DescriptorHelper();
                        $helper->describe($output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output, $this, [
                            'format' => 'txt',
                            'raw_text' => false,
                            'namespace' => $namespace,
                            'short' => false,
                        ]);

                        return isset($event) ? $event->getExitCode() : 1;
                    }

                    throw $e;
                } catch (NamespaceNotFoundException) {
                    throw $e;
                }
            }
        }

        if ($command instanceof LazyCommand) {
            $command = $command->getCommand();
        }

        $this->runningCommand = $command;
        $exitCode = $this->doRunCommand($command, $input, $output);
        $this->runningCommand = null;

        return $exitCode;
    }

    public function reset(): void
    {
    }

    public function setHelperSet(HelperSet $helperSet): void
    {
        $this->helperSet = $helperSet;
    }

    /**
     * Get the helper set associated with the command.
     */
    public function getHelperSet(): HelperSet
    {
        return $this->helperSet ??= $this->getDefaultHelperSet();
    }

    public function setDefinition(InputDefinition $definition): void
    {
        $this->definition = $definition;
    }

    /**
     * Gets the InputDefinition related to this Application.
     */
    public function getDefinition(): InputDefinition
    {
        $this->definition ??= $this->getDefaultInputDefinition();

        if ($this->singleCommand) {
            $this->definition->setArguments();
        }

        return $this->definition;
    }

    /**
     * Adds suggestions to $suggestions for the current completion input (e.g. option or argument).
     */
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        if (
            CompletionInput::TYPE_ARGUMENT_VALUE === $input->getCompletionType()
            && 'command' === $input->getCompletionName()
        ) {
            foreach ($this->all() as $name => $command) {
                // skip hidden commands and aliased commands as they already get added below
                if ($command->isHidden() || $command->getName() !== $name) {
                    continue;
                }
                $suggestions->suggestValue(new Suggestion($command->getName(), $command->getDescription()));
                foreach ($command->getAliases() as $name) {
                    $suggestions->suggestValue(new Suggestion($name, $command->getDescription()));
                }
            }

            return;
        }

        if (CompletionInput::TYPE_OPTION_NAME === $input->getCompletionType()) {
            $suggestions->suggestOptions($this->getDefinition()->getOptions());
        }

        if (
            CompletionInput::TYPE_OPTION_VALUE === $input->getCompletionType()
            && ($definition = $this->getDefinition())->hasOption($input->getCompletionName())
        ) {
            $definition->getOption($input->getCompletionName())->complete($input, $suggestions);

            return;
        }
    }

    /**
     * Gets the help message.
     */
    public function getHelp(): string
    {
        return $this->getLongVersion();
    }

    /**
     * Gets whether to catch exceptions or not during commands execution.
     */
    public function areExceptionsCaught(): bool
    {
        return $this->catchExceptions;
    }

    /**
     * Sets whether to catch exceptions or not during commands execution.
     */
    public function setCatchExceptions(bool $boolean): void
    {
        $this->catchExceptions = $boolean;
    }

    /**
     * Sets whether to catch errors or not during commands execution.
     */
    public function setCatchErrors(bool $catchErrors = true): void
    {
        $this->catchErrors = $catchErrors;
    }

    /**
     * Gets whether to automatically exit after a command execution or not.
     */
    public function isAutoExitEnabled(): bool
    {
        return $this->autoExit;
    }

    /**
     * Sets whether to automatically exit after a command execution or not.
     */
    public function setAutoExit(bool $boolean): void
    {
        $this->autoExit = $boolean;
    }

    /**
     * Gets the name of the application.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the application name.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets the application version.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Sets the application version.
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * Returns the long version of the application.
     */
    public function getLongVersion(): string
    {
        if ('UNKNOWN' !== $this->getName()) {
            if ('UNKNOWN' !== $this->getVersion()) {
                return \sprintf('%s <info>%s</info>', $this->getName(), $this->getVersion());
            }

            return $this->getName();
        }

        return 'Console Tool';
    }

    /**
     * Registers a new command.
     */
    public function register(string $name): Command
    {
        return $this->addCommand(new Command($name));
    }

    /**
     * Adds an array of command objects.
     *
     * If a Command is not enabled it will not be added.
     *
     * @param callable[]|Command[] $commands An array of commands
     */
    public function addCommands(array $commands): void
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
    }

    /**
     * @deprecated since Symfony 7.4, use Application::addCommand() instead
     */
    public function add(Command $command): ?Command
    {
        trigger_deprecation('symfony/console', '7.4', 'The "%s()" method is deprecated and will be removed in Symfony 8.0, use "%s::addCommand()" instead.', __METHOD__, self::class);

        return $this->addCommand($command);
    }

    /**
     * Adds a command object.
     *
     * If a command with the same name already exists, it will be overridden.
     * If the command is not enabled it will not be added.
     */
    public function addCommand(callable|Command $command): ?Command
    {
        $this->init();

        if (!$command instanceof Command) {
            $command = new Command(null, $command);
        }

        $command->setApplication($this);

        if (!$command->isEnabled()) {
            $command->setApplication(null);

            return null;
        }

        if (!$command instanceof LazyCommand) {
            // Will throw if the command is not correctly initialized.
            $command->getDefinition();
        }

        if (!$command->getName()) {
            throw new LogicException(\sprintf('The command defined in "%s" cannot have an empty name.', get_debug_type($command)));
        }

        $this->commands[$command->getName()] = $command;

        foreach ($command->getAliases() as $alias) {
            $this->commands[$alias] = $command;
        }

        return $command;
    }

    /**
     * Returns a registered command by name or alias.
     *
     * @throws CommandNotFoundException When given command name does not exist
     */
    public function get(string $name): Command
    {
        $this->init();

        if (!$this->has($name)) {
            throw new CommandNotFoundException(\sprintf('The command "%s" does not exist.', $name));
        }

        // When the command has a different name than the one used at the command loader level
        if (!isset($this->commands[$name])) {
            throw new CommandNotFoundException(\sprintf('The "%s" command cannot be found because it is registered under multiple names. Make sure you don\'t set a different name via constructor or "setName()".', $name));
        }

        $command = $this->commands[$name];

        if ($this->wantHelps) {
            $this->wantHelps = false;

            $helpCommand = $this->get('help');
            $helpCommand->setCommand($command);

            return $helpCommand;
        }

        return $command;
    }

    /**
     * Returns true if the command exists, false otherwise.
     */
    public function has(string $name): bool
    {
        $this->init();

        return isset($this->commands[$name]) || ($this->commandLoader?->has($name) && $this->addCommand($this->commandLoader->get($name)));
    }

    /**
     * Returns an array of all unique namespaces used by currently registered commands.
     *
     * It does not return the global namespace which always exists.
     *
     * @return string[]
     */
    public function getNamespaces(): array
    {
        $namespaces = [];
        foreach ($this->all() as $command) {
            if ($command->isHidden()) {
                continue;
            }

            $namespaces[] = $this->extractAllNamespaces($command->getName());

            foreach ($command->getAliases() as $alias) {
                $namespaces[] = $this->extractAllNamespaces($alias);
            }
        }

        return array_values(array_unique(array_filter(array_merge([], ...$namespaces))));
    }

    /**
     * Finds a registered namespace by a name or an abbreviation.
     *
     * @throws NamespaceNotFoundException When namespace is incorrect or ambiguous
     */
    public function findNamespace(string $namespace): string
    {
        $allNamespaces = $this->getNamespaces();
        $expr = implode('[^:]*:', array_map('preg_quote', explode(':', $namespace))).'[^:]*';
        $namespaces = preg_grep('{^'.$expr.'}', $allNamespaces);

        if (!$namespaces) {
            $message = \sprintf('There are no commands defined in the "%s" namespace.', $namespace);

            if ($alternatives = $this->findAlternatives($namespace, $allNamespaces)) {
                if (1 == \count($alternatives)) {
                    $message .= "\n\nDid you mean this?\n    ";
                } else {
                    $message .= "\n\nDid you mean one of these?\n    ";
                }

                $message .= implode("\n    ", $alternatives);
            }

            throw new NamespaceNotFoundException($message, $alternatives);
        }

        $exact = \in_array($namespace, $namespaces, true);
        if (\count($namespaces) > 1 && !$exact) {
            throw new NamespaceNotFoundException(\sprintf("The namespace \"%s\" is ambiguous.\nDid you mean one of these?\n%s.", $namespace, $this->getAbbreviationSuggestions(array_values($namespaces))), array_values($namespaces));
        }

        return $exact ? $namespace : reset($namespaces);
    }

    /**
     * Finds a command by name or alias.
     *
     * Contrary to get, this command tries to find the best
     * match if you give it an abbreviation of a name or alias.
     *
     * @throws CommandNotFoundException When command name is incorrect or ambiguous
     */
    public function find(string $name): Command
    {
        $this->init();

        $aliases = [];

        foreach ($this->commands as $command) {
            foreach ($command->getAliases() as $alias) {
                if (!$this->has($alias)) {
                    $this->commands[$alias] = $command;
                }
            }
        }

        if ($this->has($name)) {
            return $this->get($name);
        }

        $allCommands = $this->commandLoader ? array_merge($this->commandLoader->getNames(), array_keys($this->commands)) : array_keys($this->commands);
        $expr = implode('[^:]*:', array_map('preg_quote', explode(':', $name))).'[^:]*';
        $commands = preg_grep('{^'.$expr.'}', $allCommands);

        if (!$commands) {
            $commands = preg_grep('{^'.$expr.'}i', $allCommands);
        }

        // if no commands matched or we just matched namespaces
        if (!$commands || \count(preg_grep('{^'.$expr.'$}i', $commands)) < 1) {
            if (false !== $pos = strrpos($name, ':')) {
                // check if a namespace exists and contains commands
                $this->findNamespace(substr($name, 0, $pos));
            }

            $message = \sprintf('Command "%s" is not defined.', $name);

            if ($alternatives = $this->findAlternatives($name, $allCommands)) {
                $wantHelps = $this->wantHelps;
                $this->wantHelps = false;

                // remove hidden commands
                if ($alternatives = array_filter($alternatives, fn ($name) => !$this->get($name)->isHidden())) {
                    $message .= \sprintf("\n\nDid you mean %s?\n    %s", 1 === \count($alternatives) ? 'this' : 'one of these', implode("\n    ", $alternatives));
                }
                $this->wantHelps = $wantHelps;
            }

            throw new CommandNotFoundException($message, array_values($alternatives));
        }

        // filter out aliases for commands which are already on the list
        if (\count($commands) > 1) {
            $commandList = $this->commandLoader ? array_merge(array_flip($this->commandLoader->getNames()), $this->commands) : $this->commands;
            $commands = array_unique(array_filter($commands, function ($nameOrAlias) use (&$commandList, $commands, &$aliases) {
                if (!$commandList[$nameOrAlias] instanceof Command) {
                    $commandList[$nameOrAlias] = $this->commandLoader->get($nameOrAlias);
                }

                $commandName = $commandList[$nameOrAlias]->getName();

                $aliases[$nameOrAlias] = $commandName;

                return $commandName === $nameOrAlias || !\in_array($commandName, $commands, true);
            }));
        }

        // check whether all commands left are aliases to the same one
        if (\count($commands) > 1) {
            $uniqueCommands = array_unique(array_map(function ($nameOrAlias) use (&$commandList) {
                if (!$commandList[$nameOrAlias] instanceof Command) {
                    $commandList[$nameOrAlias] = $this->commandLoader->get($nameOrAlias);
                }

                return $commandList[$nameOrAlias]->getName();
            }, $commands));

            if (1 === \count($uniqueCommands)) {
                $commands = [reset($uniqueCommands)];
            }
        }

        if (\count($commands) > 1) {
            $usableWidth = $this->terminal->getWidth() - 10;
            $abbrevs = array_values($commands);
            $maxLen = 0;
            foreach ($abbrevs as $abbrev) {
                $maxLen = max(Helper::width($abbrev), $maxLen);
            }
            $abbrevs = array_map(static function ($cmd) use ($commandList, $usableWidth, $maxLen, &$commands) {
                if ($commandList[$cmd]->isHidden()) {
                    unset($commands[array_search($cmd, $commands)]);

                    return false;
                }

                $abbrev = str_pad($cmd, $maxLen, ' ').' '.$commandList[$cmd]->getDescription();

                return Helper::width($abbrev) > $usableWidth ? Helper::substr($abbrev, 0, $usableWidth - 3).'...' : $abbrev;
            }, array_values($commands));

            if (\count($commands) > 1) {
                $suggestions = $this->getAbbreviationSuggestions(array_filter($abbrevs));

                throw new CommandNotFoundException(\sprintf("Command \"%s\" is ambiguous.\nDid you mean one of these?\n%s.", $name, $suggestions), array_values($commands));
            }
        }

        $command = $commands ? $this->get(reset($commands)) : null;

        if (!$command || $command->isHidden()) {
            throw new CommandNotFoundException(\sprintf('The command "%s" does not exist.', $name));
        }

        return $command;
    }

    /**
     * Gets the commands (registered in the given namespace if provided).
     *
     * The array keys are the full names and the values the command instances.
     *
     * @return Command[]
     */
    public function all(?string $namespace = null): array
    {
        $this->init();

        if (null === $namespace) {
            if (!$this->commandLoader) {
                return $this->commands;
            }

            $commands = $this->commands;
            foreach ($this->commandLoader->getNames() as $name) {
                if (!isset($commands[$name]) && $this->has($name)) {
                    $commands[$name] = $this->get($name);
                }
            }

            return $commands;
        }

        $commands = [];
        foreach ($this->commands as $name => $command) {
            if ($namespace === $this->extractNamespace($name, substr_count($namespace, ':') + 1)) {
                $commands[$name] = $command;
            }
        }

        if ($this->commandLoader) {
            foreach ($this->commandLoader->getNames() as $name) {
                if (!isset($commands[$name]) && $namespace === $this->extractNamespace($name, substr_count($namespace, ':') + 1) && $this->has($name)) {
                    $commands[$name] = $this->get($name);
                }
            }
        }

        return $commands;
    }

    /**
     * Returns an array of possible abbreviations given a set of names.
     *
     * @return string[][]
     */
    public static function getAbbreviations(array $names): array
    {
        $abbrevs = [];
        foreach ($names as $name) {
            for ($len = \strlen($name); $len > 0; --$len) {
                $abbrev = substr($name, 0, $len);
                $abbrevs[$abbrev][] = $name;
            }
        }

        return $abbrevs;
    }

    public function renderThrowable(\Throwable $e, OutputInterface $output): void
    {
        $output->writeln('', OutputInterface::VERBOSITY_QUIET);

        $this->doRenderThrowable($e, $output);

        if (null !== $this->runningCommand) {
            $output->writeln(\sprintf('<info>%s</info>', OutputFormatter::escape(\sprintf($this->runningCommand->getSynopsis(), $this->getName()))), OutputInterface::VERBOSITY_QUIET);
            $output->writeln('', OutputInterface::VERBOSITY_QUIET);
        }
    }

    protected function doRenderThrowable(\Throwable $e, OutputInterface $output): void
    {
        do {
            $message = trim($e->getMessage());
            if ('' === $message || OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $class = get_debug_type($e);
                $title = \sprintf('  [%s%s]  ', $class, 0 !== ($code = $e->getCode()) ? ' ('.$code.')' : '');
                $len = Helper::width($title);
            } else {
                $len = 0;
            }

            if (str_contains($message, "@anonymous\0")) {
                $message = preg_replace_callback('/[a-zA-Z_\x7f-\xff][\\\\a-zA-Z0-9_\x7f-\xff]*+@anonymous\x00.*?\.php(?:0x?|:[0-9]++\$)?[0-9a-fA-F]++/', static fn ($m) => class_exists($m[0], false) ? (get_parent_class($m[0]) ?: key(class_implements($m[0])) ?: 'class').'@anonymous' : $m[0], $message);
            }

            $width = $this->terminal->getWidth() ? $this->terminal->getWidth() - 1 : \PHP_INT_MAX;
            $lines = [];
            foreach ('' !== $message ? preg_split('/\r?\n/', $message) : [] as $line) {
                foreach ($this->splitStringByWidth($line, $width - 4) as $line) {
                    // pre-format lines to get the right string length
                    $lineLength = Helper::width($line) + 4;
                    $lines[] = [$line, $lineLength];

                    $len = max($lineLength, $len);
                }
            }

            $messages = [];
            if (!$e instanceof ExceptionInterface || OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $messages[] = \sprintf('<comment>%s</comment>', OutputFormatter::escape(\sprintf('In %s line %s:', basename($e->getFile()) ?: 'n/a', $e->getLine() ?: 'n/a')));
            }
            $messages[] = $emptyLine = \sprintf('<error>%s</error>', str_repeat(' ', $len));
            if ('' === $message || OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $messages[] = \sprintf('<error>%s%s</error>', $title, str_repeat(' ', max(0, $len - Helper::width($title))));
            }
            foreach ($lines as $line) {
                $messages[] = \sprintf('<error>  %s  %s</error>', OutputFormatter::escape($line[0]), str_repeat(' ', $len - $line[1]));
            }
            $messages[] = $emptyLine;
            $messages[] = '';

            $output->writeln($messages, OutputInterface::VERBOSITY_QUIET);

            if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $output->writeln('<comment>Exception trace:</comment>', OutputInterface::VERBOSITY_QUIET);

                // exception related properties
                $trace = $e->getTrace();

                array_unshift($trace, [
                    'function' => '',
                    'file' => $e->getFile() ?: 'n/a',
                    'line' => $e->getLine() ?: 'n/a',
                    'args' => [],
                ]);

                for ($i = 0, $count = \count($trace); $i < $count; ++$i) {
                    $class = $trace[$i]['class'] ?? '';
                    $type = $trace[$i]['type'] ?? '';
                    $function = $trace[$i]['function'] ?? '';
                    $file = $trace[$i]['file'] ?? 'n/a';
                    $line = $trace[$i]['line'] ?? 'n/a';

                    $output->writeln(\sprintf(' %s%s at <info>%s:%s</info>', $class, $function ? $type.$function.'()' : '', $file, $line), OutputInterface::VERBOSITY_QUIET);
                }

                $output->writeln('', OutputInterface::VERBOSITY_QUIET);
            }
        } while ($e = $e->getPrevious());
    }

    /**
     * Configures the input and output instances based on the user arguments and options.
     */
    protected function configureIO(InputInterface $input, OutputInterface $output): void
    {
        if ($input->hasParameterOption(['--ansi'], true)) {
            $output->setDecorated(true);
        } elseif ($input->hasParameterOption(['--no-ansi'], true)) {
            $output->setDecorated(false);
        }

        $shellVerbosity = match (true) {
            $input->hasParameterOption(['--silent'], true) => -2,
            $input->hasParameterOption(['--quiet', '-q'], true) => -1,
            $input->hasParameterOption('-vvv', true) || $input->hasParameterOption('--verbose=3', true) || 3 === $input->getParameterOption('--verbose', false, true) => 3,
            $input->hasParameterOption('-vv', true) || $input->hasParameterOption('--verbose=2', true) || 2 === $input->getParameterOption('--verbose', false, true) => 2,
            $input->hasParameterOption('-v', true) || $input->hasParameterOption('--verbose=1', true) || $input->hasParameterOption('--verbose', true) || $input->getParameterOption('--verbose', false, true) => 1,
            default => (int) ($_ENV['SHELL_VERBOSITY'] ?? $_SERVER['SHELL_VERBOSITY'] ?? getenv('SHELL_VERBOSITY')),
        };

        $output->setVerbosity(match ($shellVerbosity) {
            -2 => OutputInterface::VERBOSITY_SILENT,
            -1 => OutputInterface::VERBOSITY_QUIET,
            1 => OutputInterface::VERBOSITY_VERBOSE,
            2 => OutputInterface::VERBOSITY_VERY_VERBOSE,
            3 => OutputInterface::VERBOSITY_DEBUG,
            default => ($shellVerbosity = 0) ?: $output->getVerbosity(),
        });

        if (0 > $shellVerbosity || $input->hasParameterOption(['--no-interaction', '-n'], true)) {
            $input->setInteractive(false);
        }

        if (\function_exists('putenv')) {
            @putenv('SHELL_VERBOSITY='.$shellVerbosity);
        }
        $_ENV['SHELL_VERBOSITY'] = $shellVerbosity;
        $_SERVER['SHELL_VERBOSITY'] = $shellVerbosity;
    }

    /**
     * Runs the current command.
     *
     * If an event dispatcher has been attached to the application,
     * events are also dispatched during the life-cycle of the command.
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output): int
    {
        foreach ($command->getHelperSet() as $helper) {
            if ($helper instanceof InputAwareInterface) {
                $helper->setInput($input);
            }
        }

        $registeredSignals = false;
        if (($commandSignals = $command->getSubscribedSignals()) || $this->dispatcher && $this->signalsToDispatchEvent) {
            $signalRegistry = $this->getSignalRegistry();

            $registeredSignals = true;
            $this->getSignalRegistry()->pushCurrentHandlers();

            if ($this->dispatcher) {
                // We register application signals, so that we can dispatch the event
                foreach ($this->signalsToDispatchEvent as $signal) {
                    $signalEvent = new ConsoleSignalEvent($command, $input, $output, $signal);
                    $alarmEvent = \SIGALRM === $signal ? new ConsoleAlarmEvent($command, $input, $output) : null;

                    $signalRegistry->register($signal, function ($signal) use ($signalEvent, $alarmEvent, $command, $commandSignals, $input, $output) {
                        $this->dispatcher->dispatch($signalEvent, ConsoleEvents::SIGNAL);
                        $exitCode = $signalEvent->getExitCode();

                        if (null !== $alarmEvent) {
                            if (false !== $exitCode) {
                                $alarmEvent->setExitCode($exitCode);
                            } else {
                                $alarmEvent->abortExit();
                            }
                            $this->dispatcher->dispatch($alarmEvent);
                            $exitCode = $alarmEvent->getExitCode();
                        }

                        // If the command is signalable, we call the handleSignal() method
                        if (\in_array($signal, $commandSignals, true)) {
                            $exitCode = $command->handleSignal($signal, $exitCode);
                        }

                        if (\SIGALRM === $signal) {
                            $this->scheduleAlarm();
                        }

                        if (false !== $exitCode) {
                            $event = new ConsoleTerminateEvent($command, $input, $output, $exitCode, $signal);
                            $this->dispatcher->dispatch($event, ConsoleEvents::TERMINATE);

                            exit($event->getExitCode());
                        }
                    });
                }

                // then we register command signals, but not if already handled after the dispatcher
                $commandSignals = array_diff($commandSignals, $this->signalsToDispatchEvent);
            }

            foreach ($commandSignals as $signal) {
                $signalRegistry->register($signal, function (int $signal) use ($command): void {
                    if (\SIGALRM === $signal) {
                        $this->scheduleAlarm();
                    }

                    if (false !== $exitCode = $command->handleSignal($signal)) {
                        exit($exitCode);
                    }
                });
            }
        }

        if (null === $this->dispatcher) {
            try {
                return $command->run($input, $output);
            } finally {
                if ($registeredSignals) {
                    $this->getSignalRegistry()->popPreviousHandlers();
                }
            }
        }

        // bind before the console.command event, so the listeners have access to input options/arguments
        try {
            $command->mergeApplicationDefinition();
            $input->bind($command->getDefinition());
        } catch (ExceptionInterface) {
            // ignore invalid options/arguments for now, to allow the event listeners to customize the InputDefinition
        }

        $event = new ConsoleCommandEvent($command, $input, $output);
        $e = null;

        try {
            $this->dispatcher->dispatch($event, ConsoleEvents::COMMAND);

            if ($event->commandShouldRun()) {
                $exitCode = $command->run($input, $output);
            } else {
                $exitCode = ConsoleCommandEvent::RETURN_CODE_DISABLED;
            }
        } catch (\Throwable $e) {
            $event = new ConsoleErrorEvent($input, $output, $e, $command);
            $this->dispatcher->dispatch($event, ConsoleEvents::ERROR);
            $e = $event->getError();

            if (0 === $exitCode = $event->getExitCode()) {
                $e = null;
            }
        } finally {
            if ($registeredSignals) {
                $this->getSignalRegistry()->popPreviousHandlers();
            }
        }

        $event = new ConsoleTerminateEvent($command, $input, $output, $exitCode);
        $this->dispatcher->dispatch($event, ConsoleEvents::TERMINATE);

        if (null !== $e) {
            throw $e;
        }

        return $event->getExitCode();
    }

    /**
     * Gets the name of the command based on input.
     */
    protected function getCommandName(InputInterface $input): ?string
    {
        return $this->singleCommand ? $this->defaultCommand : $input->getFirstArgument();
    }

    /**
     * Gets the default input definition.
     */
    protected function getDefaultInputDefinition(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display help for the given command. When no command is given display help for the <info>'.$this->defaultCommand.'</info> command'),
            new InputOption('--silent', null, InputOption::VALUE_NONE, 'Do not output any message'),
            new InputOption('--quiet', '-q', InputOption::VALUE_NONE, 'Only errors are displayed. All other output is suppressed'),
            new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display this application version'),
            new InputOption('--ansi', '', InputOption::VALUE_NEGATABLE, 'Force (or disable --no-ansi) ANSI output', null),
            new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE, 'Do not ask any interactive question'),
        ]);
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return Command[]
     */
    protected function getDefaultCommands(): array
    {
        return [new HelpCommand(), new ListCommand(), new CompleteCommand(), new DumpCompletionCommand()];
    }

    /**
     * Gets the default helper set with the helpers that should always be available.
     */
    protected function getDefaultHelperSet(): HelperSet
    {
        return new HelperSet([
            new FormatterHelper(),
            new DebugFormatterHelper(),
            new ProcessHelper(),
            new QuestionHelper(),
        ]);
    }

    /**
     * Returns abbreviated suggestions in string format.
     */
    private function getAbbreviationSuggestions(array $abbrevs): string
    {
        return '    '.implode("\n    ", $abbrevs);
    }

    /**
     * Returns the namespace part of the command name.
     *
     * This method is not part of public API and should not be used directly.
     */
    public function extractNamespace(string $name, ?int $limit = null): string
    {
        $parts = explode(':', $name, -1);

        return implode(':', null === $limit ? $parts : \array_slice($parts, 0, $limit));
    }

    /**
     * Finds alternative of $name among $collection,
     * if nothing is found in $collection, try in $abbrevs.
     *
     * @return string[]
     */
    private function findAlternatives(string $name, iterable $collection): array
    {
        $threshold = 1e3;
        $alternatives = [];

        $collectionParts = [];
        foreach ($collection as $item) {
            $collectionParts[$item] = explode(':', $item);
        }

        foreach (explode(':', $name) as $i => $subname) {
            foreach ($collectionParts as $collectionName => $parts) {
                $exists = isset($alternatives[$collectionName]);
                if (!isset($parts[$i]) && $exists) {
                    $alternatives[$collectionName] += $threshold;
                    continue;
                } elseif (!isset($parts[$i])) {
                    continue;
                }

                $lev = levenshtein($subname, $parts[$i]);
                if ($lev <= \strlen($subname) / 3 || '' !== $subname && str_contains($parts[$i], $subname)) {
                    $alternatives[$collectionName] = $exists ? $alternatives[$collectionName] + $lev : $lev;
                } elseif ($exists) {
                    $alternatives[$collectionName] += $threshold;
                }
            }
        }

        foreach ($collection as $item) {
            $lev = levenshtein($name, $item);
            if ($lev <= \strlen($name) / 3 || str_contains($item, $name)) {
                $alternatives[$item] = isset($alternatives[$item]) ? $alternatives[$item] - $lev : $lev;
            }
        }

        $alternatives = array_filter($alternatives, static fn ($lev) => $lev < 2 * $threshold);
        ksort($alternatives, \SORT_NATURAL | \SORT_FLAG_CASE);

        return array_keys($alternatives);
    }

    /**
     * Sets the default Command name.
     *
     * @return $this
     */
    public function setDefaultCommand(string $commandName, bool $isSingleCommand = false): static
    {
        $this->defaultCommand = explode('|', ltrim($commandName, '|'))[0];

        if ($isSingleCommand) {
            // Ensure the command exist
            $this->find($commandName);

            $this->singleCommand = true;
        }

        return $this;
    }

    /**
     * @internal
     */
    public function isSingleCommand(): bool
    {
        return $this->singleCommand;
    }

    private function splitStringByWidth(string $string, int $width): array
    {
        // str_split is not suitable for multi-byte characters, we should use preg_split to get char array properly.
        // additionally, array_slice() is not enough as some character has doubled width.
        // we need a function to split string not by character count but by string width
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return str_split($string, $width);
        }

        $utf8String = mb_convert_encoding($string, 'utf8', $encoding);
        $lines = [];
        $line = '';

        $offset = 0;
        while (preg_match('/.{1,10000}/u', $utf8String, $m, 0, $offset)) {
            $offset += \strlen($m[0]);

            foreach (preg_split('//u', $m[0]) as $char) {
                // test if $char could be appended to current line
                if (Helper::width($line.$char) <= $width) {
                    $line .= $char;
                    continue;
                }
                // if not, push current line to array and make new line
                $lines[] = str_pad($line, $width);
                $line = $char;
            }
        }

        $lines[] = \count($lines) ? str_pad($line, $width) : $line;

        return mb_convert_encoding($lines, $encoding, 'utf8');
    }

    /**
     * Returns all namespaces of the command name.
     *
     * @return string[]
     */
    private function extractAllNamespaces(string $name): array
    {
        // -1 as third argument is needed to skip the command short name when exploding
        $parts = explode(':', $name, -1);
        $namespaces = [];

        foreach ($parts as $part) {
            if (\count($namespaces)) {
                $namespaces[] = end($namespaces).':'.$part;
            } else {
                $namespaces[] = $part;
            }
        }

        return $namespaces;
    }

    private function init(): void
    {
        if ($this->initialized) {
            return;
        }
        $this->initialized = true;

        if ((new \ReflectionMethod($this, 'add'))->getDeclaringClass()->getName() !== (new \ReflectionMethod($this, 'addCommand'))->getDeclaringClass()->getName()) {
            $adder = $this->add(...);
        } else {
            $adder = $this->addCommand(...);
        }

        foreach ($this->getDefaultCommands() as $command) {
            $adder($command);
        }
    }
}
