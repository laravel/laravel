<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

use Psy\CodeCleaner\NoReturnValue;
use Psy\Completion\CompletionEngine;
use Psy\Completion\Refiner\CommandContextRefiner;
use Psy\Completion\Source\CommandArgumentSource;
use Psy\Completion\Source\CommandOptionSource;
use Psy\Completion\Source\CommandSource;
use Psy\Completion\Source\HistorySource;
use Psy\Completion\Source\MatcherAdapterSource;
use Psy\Exception\BreakException;
use Psy\Exception\ErrorException;
use Psy\Exception\Exception as PsyException;
use Psy\Exception\InterruptException;
use Psy\Exception\RuntimeException;
use Psy\Exception\ThrowUpException;
use Psy\ExecutionLoop\ProcessForker;
use Psy\ExecutionLoop\RunkitReloader;
use Psy\ExecutionLoop\SignalHandler;
use Psy\ExecutionLoop\UopzReloader;
use Psy\Formatter\TraceFormatter;
use Psy\Input\ShellInput;
use Psy\Input\SilentInput;
use Psy\Output\BuiltinOutputPager;
use Psy\Output\ShellOutput;
use Psy\Readline\InteractiveReadlineInterface;
use Psy\Readline\LegacyReadline;
use Psy\Readline\Readline;
use Psy\Readline\ReadlineAware;
use Psy\Readline\ShellReadlineInterface;
use Psy\Shell\PendingInputState;
use Psy\TabCompletion\AutoCompleter;
use Psy\TabCompletion\Matcher;
use Psy\Util\Tty;
use Psy\VarDumper\Presenter;
use Psy\VarDumper\PresenterAware;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Exception\ExceptionInterface as SymfonyConsoleException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * The Psy Shell application.
 *
 * Usage:
 *
 *     $shell = new Shell;
 *     $shell->run();
 *
 * @author Justin Hileman <justin@justinhileman.info>
 */
class Shell extends Application
{
    const VERSION = 'v0.12.24';

    private Configuration $config;
    private ?CodeCleaner $cleaner = null;
    private OutputInterface $output;
    private ?int $originalVerbosity = null;
    private ?ShellReadlineInterface $readline = null;
    private array $inputBuffer;
    private PendingInputState $pendingInput;
    private string $stdoutBuffer;
    private Context $context;
    private array $includes;
    private bool $outputWantsNewline = false;
    private array $loopListeners;
    private bool $booted = false;
    private bool $autoloadWarmed = false;
    private ?AutoCompleter $autoCompleter = null;
    private ?CompletionEngine $completionEngine = null;
    /** @var Completion\Source\SourceInterface[] */
    private array $pendingCompletionSources = [];
    private array $matchers = [];
    /** @var CommandAware[] */
    private array $commandCompletion = [];
    private bool $lastExecSuccess = true;
    private bool $suppressReturnValue = false;
    private bool $nonInteractive = false;
    private ?int $errorReporting = null;
    private bool $interactiveSignalCharsEnabled = false;
    private bool $outputWritten = false;
    private bool $legacyNeedsPromptSpacer = false;
    private bool $writingLegacySpacer = false;

    /**
     * Create a new Psy Shell.
     *
     * @param Configuration|null $config (default: null)
     */
    public function __construct(?Configuration $config = null)
    {
        $this->config = $config ?: new Configuration();
        $this->context = new Context();
        $this->includes = [];
        $this->inputBuffer = [];
        $this->pendingInput = new PendingInputState();
        $this->stdoutBuffer = '';
        $this->loopListeners = $this->getDefaultLoopListeners();

        parent::__construct('Psy Shell', self::VERSION);

        $this->config->setShell($this);

        // Register the current shell session's config with \Psy\info
        \Psy\info($this->config);
    }

    /**
     * Warm the autoloader by loading classes at startup.
     *
     * This improves tab completion by making classes available via get_declared_classes()
     * rather than maintaining a separate list of available classes.
     */
    private function warmAutoloader(): void
    {
        if ($this->autoloadWarmed) {
            return;
        }
        $this->autoloadWarmed = true;

        $warmers = $this->config->getAutoloadWarmers();
        if (empty($warmers)) {
            return;
        }

        $output = $this->config->getOutput();
        if ($output instanceof ConsoleOutput) {
            $output = $output->getErrorOutput();
        }

        $start = \microtime(true);
        $loadedCount = 0;

        foreach ($warmers as $warmer) {
            try {
                $loadedCount += $warmer->warm();
            } catch (\Throwable $e) {
                $output->writeln($this->formatException($e), OutputInterface::VERBOSITY_DEBUG);
            }
        }

        $message = \sprintf(
            '<whisper>Autoload warming: loaded %d classes in %.1fms</whisper>',
            $loadedCount,
            (\microtime(true) - $start) * 1000
        );

        $output->writeln($message, OutputInterface::VERBOSITY_DEBUG);

        if (!\class_exists('Composer\\ClassMapGenerator\\ClassMapGenerator', false)) {
            $output->writeln('<whisper>Autoload warming works best with composer/class-map-generator installed</whisper>');
        }
    }

    /**
     * Boot the shell, initializing the CodeCleaner and Readline.
     *
     * This is called lazily when commands or methods require these dependencies.
     * If input/output are provided, they'll be used for trust prompts. Otherwise,
     * falls back to config defaults.
     */
    public function boot(?InputInterface $input = null, ?OutputInterface $output = null): void
    {
        if ($this->booted) {
            return;
        }

        $this->loadLocalConfig($input, $output);

        $this->cleaner = $this->config->getCodeCleaner();
        $this->readline = $this->configureReadline($this->config->getReadline());
        $this->booted = true;

        if ($this->readline instanceof LegacyReadline) {
            $this->add(new Command\BufferCommand());
        }

        $this->refreshCommandDependencies();
    }

    /**
     * Load local config with trust prompt if needed.
     */
    private function loadLocalConfig(?InputInterface $input, ?OutputInterface $output): void
    {
        if ($output === null) {
            $output = $this->config->getOutput();
        }

        if ($input === null) {
            $input = new ArrayInput([]);
            // Programmatic callers (e.g. Shell::execute) don't provide a real
            // interactive input stream, so trust prompts must not block.
            $input->setInteractive(false);
        }

        $this->config->loadLocalConfigWithPrompt($input, $output);
    }

    /**
     * Configure a readline instance before assigning it to the shell.
     *
     * This sets up shell awareness, interactive readline dependencies,
     * output/theme integration, and options.
     *
     * @return ShellReadlineInterface The configured readline instance
     */
    private function configureReadline(Readline $readline): ShellReadlineInterface
    {
        if (!($readline instanceof ShellReadlineInterface)) {
            $readline = new LegacyReadline($readline);
        }

        if ($readline instanceof InteractiveReadlineInterface) {
            // setOutput boots the interactive readline, so it must come first
            $readline->setOutput($this->output ?? $this->config->getOutput());
            $readline->setTheme($this->config->theme());
            $readline->setRequireSemicolons($this->config->requireSemicolons());
            $readline->setUseBracketedPaste($this->config->useBracketedPaste());
            if ($readline instanceof \Psy\Readline\InteractiveReadline) {
                $readline->setUseUnicode($this->config->useUnicode());
            }
            $readline->setUseSyntaxHighlighting($this->config->useSyntaxHighlighting());
            $readline->setUseSuggestions($this->config->useSuggestions());
            $this->wireUserlandPagerIfRequested($readline);
        } else {
            $readline->setRequireSemicolons($this->config->requireSemicolons());
        }

        if ($readline instanceof LegacyReadline) {
            $readline->setBufferPrompt($this->config->theme()->bufferPrompt());
            $readline->setOutput($this->output ?? $this->config->getOutput());
        }

        $readline->setShell($this);

        return $readline;
    }

    /**
     * Install the userland BuiltinOutputPager on the ShellOutput if the
     * config asked for `pager => true` (or auto-selected it because the
     * interactive readline is active).
     */
    private function wireUserlandPagerIfRequested(InteractiveReadlineInterface $readline): void
    {
        if ($this->config->getPager() !== true) {
            return;
        }

        $output = $this->output ?? $this->config->getOutput();
        if (!($output instanceof ShellOutput)) {
            return;
        }

        $output->setPager(new BuiltinOutputPager($output, $readline->getPager()));
    }

    /**
     * Refresh dependencies on all registered commands.
     */
    private function refreshCommandDependencies(): void
    {
        foreach ($this->all() as $command) {
            $this->configureCommand($command);
        }
    }

    /**
     * Configure a command with context and dependencies.
     */
    private function configureCommand(BaseCommand $command): void
    {
        if ($command instanceof ContextAware) {
            $command->setContext($this->context);
        }

        if ($this->booted) {
            if ($command instanceof CodeCleanerAware && $this->cleaner !== null) {
                $command->setCodeCleaner($this->cleaner);
            }

            if ($command instanceof PresenterAware) {
                $command->setPresenter($this->config->getPresenter());
            }

            if ($command instanceof ReadlineAware && $this->readline !== null) {
                $command->setReadline($this->readline);
            }
        }
    }

    /**
     * Check whether the first thing in a backtrace is an include call.
     *
     * This is used by the psysh bin to decide whether to start a shell on boot,
     * or to simply autoload the library.
     */
    public static function isIncluded(array $trace): bool
    {
        $isIncluded = isset($trace[0]['function']) &&
          \in_array($trace[0]['function'], ['require', 'include', 'require_once', 'include_once']);

        // Detect Composer PHP bin proxies.
        if ($isIncluded && \array_key_exists('_composer_autoload_path', $GLOBALS) && \preg_match('{[\\\\/]psysh$}', $trace[0]['file'])) {
            // If we're in a bin proxy, we'll *always* see one include, but we
            // care if we see a second immediately after that.
            return isset($trace[1]['function']) &&
                \in_array($trace[1]['function'], ['require', 'include', 'require_once', 'include_once']);
        }

        return $isIncluded;
    }

    /**
     * Check if the currently running PsySH bin is a phar archive.
     */
    public static function isPhar(): bool
    {
        return \class_exists("\Phar") && \Phar::running() !== '' && \strpos(__FILE__, \Phar::running(true)) === 0;
    }

    /**
     * Invoke a Psy Shell from the current context.
     *
     * @see Psy\debug
     * @deprecated will be removed in 1.0. Use \Psy\debug instead
     *
     * @param array         $vars   Scope variables from the calling context (default: [])
     * @param object|string $bindTo Bound object ($this) or class (self) value for the shell
     *
     * @return array Scope variables from the debugger session
     */
    public static function debug(array $vars = [], $bindTo = null): array
    {
        @\trigger_error('`Psy\\Shell::debug` is deprecated; call `Psy\\debug` instead.', \E_USER_DEPRECATED);

        return \Psy\debug($vars, $bindTo);
    }

    /**
     * Adds a command object.
     *
     * @deprecated since Symfony Console 7.4, use addCommand() instead
     *
     * @param BaseCommand $command A Symfony Console Command object
     *
     * @return BaseCommand The registered command
     */
    public function add(BaseCommand $command): BaseCommand
    {
        return $this->addCommand($command);
    }

    /**
     * Adds a command object.
     *
     * @param BaseCommand|callable $command A Symfony Console Command object or callable
     *
     * @return BaseCommand|null The registered command, or null
     */
    public function addCommand($command): ?BaseCommand
    {
        // For Symfony Console < 7.4, use parent::add()
        if (\method_exists(Application::class, 'addCommand')) {
            /** @phan-suppress-next-line PhanUndeclaredStaticMethod (Symfony Console 7.4+) */
            $ret = parent::addCommand($command);
        } else {
            $ret = parent::add($command);
        }

        if ($ret) {
            $this->configureCommand($ret);

            $allCommands = $this->all();
            foreach ($this->commandCompletion as $instance) {
                $instance->setCommands($allCommands);
            }
        }

        return $ret;
    }

    /**
     * Gets the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition(): InputDefinition
    {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message.'),
        ]);
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands(): array
    {
        $sudo = new Command\SudoCommand();

        $hist = new Command\HistoryCommand();

        $doc = new Command\DocCommand();
        $doc->setConfiguration($this->config);

        $copy = new Command\CopyCommand();
        $copy->setConfiguration($this->config);

        $config = new Command\ConfigCommand();
        $config->setConfiguration($this->config);

        $commands = [
            new Command\HelpCommand(),
            new Command\ListCommand(),
            new Command\DumpCommand(),
            $config,
            $copy,
            $doc,
            new Command\ShowCommand(),
            new Command\WtfCommand(),
            new Command\WhereamiCommand(),
            new Command\ThrowUpCommand(),
            new Command\TimeitCommand(),
            new Command\TraceCommand(),
            new Command\ClearCommand(),
            new Command\EditCommand($this->config->getRuntimeDir(false)),
            // new Command\PsyVersionCommand(),
            $sudo,
            $hist,
            new Command\ExitCommand(),
        ];

        // Only add yolo command if UopzReloader is supported
        if (UopzReloader::isSupported()) {
            $yolo = new Command\YoloCommand();
            $commands[] = $yolo;
        }

        return $commands;
    }

    /**
     * @deprecated No longer used internally; matchers are registered via the completion engine
     *
     * @return Matcher\AbstractMatcher[]
     */
    protected function getDefaultMatchers(): array
    {
        return [];
    }

    /**
     * Gets the default command loop listeners.
     *
     * @return array An array of Execution Loop Listener instances
     */
    protected function getDefaultLoopListeners(): array
    {
        $listeners = [];

        if ($inputLogger = $this->config->getInputLogger()) {
            $listeners[] = $inputLogger;
        }

        if (ProcessForker::isSupported() && $this->config->usePcntl()) {
            $listeners[] = new ProcessForker();
        } elseif (SignalHandler::isSupported()) {
            // Only use SignalHandler when process forking is disabled
            // ProcessForker handles SIGINT in the parent process, which is cleaner
            $listeners[] = new SignalHandler();
        }

        if (RunkitReloader::isSupported()) {
            $listeners[] = new RunkitReloader();
        } elseif (UopzReloader::isSupported()) {
            $listeners[] = new UopzReloader();
        }

        if ($executionLogger = $this->config->getExecutionLogger()) {
            $listeners[] = $executionLogger;
        }

        return $listeners;
    }

    /**
     * Enable or disable force-reload mode for code reloaders.
     *
     * Used by the `yolo` command to bypass safety warnings when reloading code.
     */
    public function setForceReload(bool $force): void
    {
        foreach ($this->loopListeners as $listener) {
            if (\method_exists($listener, 'setForceReload')) {
                $listener->setForceReload($force);
            }
        }
    }

    /**
     * Apply live service updates after a runtime configuration change.
     */
    public function applyRuntimeConfigChange(string $key): void
    {
        if (isset($this->output)) {
            switch ($key) {
                case 'colorMode':
                    $decorated = $this->config->getOutputDecorated();
                    $this->output->setDecorated($decorated !== null ? $decorated : !$this->config->outputIsPiped());
                    break;

                case 'verbosity':
                    $this->originalVerbosity = $this->config->getOutputVerbosity();
                    $this->output->setVerbosity($this->originalVerbosity);
                    break;

                case 'theme':
                    if ($this->output instanceof ShellOutput) {
                        $this->output->setTheme($this->config->theme());
                    }
                    break;

                case 'pager':
                    if ($this->output instanceof ShellOutput) {
                        $pager = $this->config->getPager();
                        if ($pager === true) {
                            $pager = $this->readline instanceof InteractiveReadlineInterface
                                ? new BuiltinOutputPager($this->output, $this->readline->getPager())
                                : null;
                        }
                        $this->output->setPager($pager === false ? null : $pager);
                    }
                    break;
            }
        }

        if (isset($this->readline) && $this->readline instanceof InteractiveReadlineInterface) {
            switch ($key) {
                case 'theme':
                    $this->readline->setTheme($this->config->theme());
                    break;

                case 'requireSemicolons':
                    $this->readline->setRequireSemicolons($this->config->requireSemicolons());
                    break;

                case 'useBracketedPaste':
                    $this->readline->setUseBracketedPaste($this->config->useBracketedPaste());
                    break;

                case 'useUnicode':
                    if ($this->readline instanceof \Psy\Readline\InteractiveReadline) {
                        $this->readline->setUseUnicode($this->config->useUnicode());
                    }
                    break;

                case 'useSyntaxHighlighting':
                    $this->readline->setUseSyntaxHighlighting($this->config->useSyntaxHighlighting());
                    break;

                case 'useSuggestions':
                    $this->readline->setUseSuggestions($this->config->useSuggestions());
                    break;
            }
        }
    }

    /**
     * Add tab completion matchers.
     *
     * @param array $matchers
     */
    public function addMatchers(array $matchers)
    {
        $matchers = $this->deduplicateObjects($matchers, $this->matchers);
        if ($matchers === []) {
            return;
        }

        $this->matchers = \array_merge($this->matchers, $matchers);

        if (isset($this->completionEngine)) {
            $this->addLegacyMatchersToCompletionEngine($matchers);
        }
    }

    /**
     * @deprecated Call `addMatchers` instead
     *
     * @param array $matchers
     */
    public function addTabCompletionMatchers(array $matchers)
    {
        @\trigger_error('`addTabCompletionMatchers` is deprecated; call `addMatchers` instead.', \E_USER_DEPRECATED);

        $this->addMatchers($matchers);
    }

    /**
     * Add completion sources to the completion engine.
     *
     * @internal experimental; API may change before Interactive Readline is stable
     *
     * @param Completion\Source\SourceInterface[] $sources
     */
    public function addCompletionSources(array $sources)
    {
        $existing = isset($this->completionEngine) ? [] : $this->pendingCompletionSources;
        $sources = $this->deduplicateObjects($sources, $existing);
        if ($sources === []) {
            return;
        }

        if (!isset($this->completionEngine)) {
            $this->pendingCompletionSources = \array_merge($this->pendingCompletionSources, $sources);

            return;
        }

        foreach ($sources as $source) {
            $this->completionEngine->addSource($source);
        }
    }

    /**
     * Set the Shell output.
     *
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        $this->originalVerbosity = $output->getVerbosity();
    }

    /**
     * Runs PsySH.
     *
     * @param InputInterface|null  $input  An Input instance
     * @param OutputInterface|null $output An Output instance
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        // We'll just ignore the input passed in, and set up our own!
        $input = new ArrayInput([]);
        $input->setInteractive($this->config->getInputInteractive());

        if ($output === null) {
            $output = $this->config->getOutput();
        }

        $this->setAutoExit(false);
        $this->setCatchExceptions(false);

        try {
            return parent::run($input, $output);
        } catch (BreakException $e) {
            // BreakException from ProcessForker or exit() - return its exit code
            return $e->getCode();
        } catch (\Throwable $e) {
            $this->writeException($e);
        }

        return 1;
    }

    /**
     * Runs PsySH.
     *
     * @throws \Throwable if thrown via the `throw-up` command
     *
     * @param InputInterface  $input  An Input instance
     * @param OutputInterface $output An Output instance
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $this->setOutput($output);
        $this->boot($input, $output);
        $this->clearPendingCode();
        $this->warmAutoloader();

        if ($this->config->getInputInteractive()) {
            // @todo should it be possible to have raw output in an interactive run?
            return $this->doInteractiveRun();
        } else {
            return $this->doNonInteractiveRun($this->config->rawOutput());
        }
    }

    /**
     * Run PsySH in interactive mode.
     *
     * Initializes tab completion and readline history, then spins up the
     * execution loop.
     *
     * @throws \Throwable if thrown via the `throw-up` command
     *
     * @return int 0 if everything went fine, or an error code
     */
    private function doInteractiveRun(): int
    {
        if ($this->config->useTabCompletion()) {
            $this->initializeCompletionEngine();
            $this->initializeTabCompletion();
        }

        if ($this->readline instanceof CommandAware) {
            $this->readline->setCommands($this->all());
            $this->commandCompletion[] = $this->readline;
        }

        $this->readline->readHistory();

        $this->output->writeln($this->getHeader());
        $this->writeVersionInfo();
        $this->writeManualUpdateInfo();
        $this->writeStartupMessage();

        try {
            $this->beforeRun();
            $this->loadIncludes();
            $loop = new ExecutionLoopClosure($this);
            $exitCode = $loop->execute();
            $this->afterRun($exitCode ?? 0);

            return $exitCode ?? 0;
        } catch (ThrowUpException $e) {
            throw $e->getPrevious();
        } catch (BreakException $e) {
            // The ProcessForker throws a BreakException to finish the main thread.
            return $e->getCode();
        }
    }

    /**
     * Run PsySH in non-interactive mode.
     *
     * Note that this isn't very useful unless you supply "include" arguments at
     * the command line, or code via stdin.
     *
     * @param bool $rawOutput
     *
     * @return int 0 if everything went fine, or an error code
     */
    private function doNonInteractiveRun(bool $rawOutput): int
    {
        $this->nonInteractive = true;

        // If raw output is enabled (or output is piped) we don't want startup messages.
        if (!$rawOutput && !$this->config->outputIsPiped()) {
            $this->output->writeln($this->getHeader());
            $this->writeVersionInfo();
            $this->writeManualUpdateInfo();
            $this->writeStartupMessage();
        }

        $this->beforeRun();
        $this->loadIncludes();

        // For non-interactive execution, read only from the input buffer or from piped input.
        // Otherwise it'll try to readline and hang, waiting for user input with no indication of
        // what's holding things up.
        if (!empty($this->inputBuffer) || $this->config->inputIsPiped()) {
            $this->getInput(false);
        }

        try {
            if ($this->hasCode()) {
                $ret = $this->execute($this->flushCode());
                $this->writeReturnValue($ret, $rawOutput);
            }
        } catch (BreakException $e) {
            // User called exit() in non-interactive mode
            $this->afterRun($e->getCode());
            $this->nonInteractive = false;

            return $e->getCode();
        }

        $this->afterRun(0);
        $this->nonInteractive = false;

        return 0;
    }

    /**
     * Configures the input and output instances based on the user arguments and options.
     */
    protected function configureIO(InputInterface $input, OutputInterface $output): void
    {
        // @todo overrides via environment variables (or should these happen in config? ... probably config)
        $input->setInteractive($this->config->getInputInteractive());

        if ($this->config->getOutputDecorated() !== null) {
            $output->setDecorated($this->config->getOutputDecorated());
        }

        $output->setVerbosity($this->config->getOutputVerbosity());
    }

    /**
     * Load user-defined includes.
     */
    private function loadIncludes()
    {
        // Load user-defined includes
        $load = function (self $__psysh__) {
            \set_error_handler([$__psysh__, 'handleError']);
            foreach ($__psysh__->getIncludes() as $__psysh_include__) {
                try {
                    include_once $__psysh_include__;
                } catch (\Exception $_e) {
                    $__psysh__->writeException($_e);
                }
            }
            \restore_error_handler();
            unset($__psysh_include__);

            // Override any new local variables with pre-defined scope variables
            \extract($__psysh__->getScopeVariables(false));

            // ... then add the whole mess of variables back.
            $__psysh__->setScopeVariables(\get_defined_vars());
        };

        $load($this);
    }

    /**
     * Read user input.
     *
     * This will continue fetching user input until the code buffer contains
     * valid code.
     *
     * @throws BreakException if user hits Ctrl+D
     *
     * @param bool $interactive
     */
    public function getInput(bool $interactive = true)
    {
        $this->boot();

        while (true) {
            // reset output verbosity (in case it was altered by a subcommand)
            $this->output->setVerbosity($this->originalVerbosity);
            $this->outputWritten = false;
            $this->resetShellOutputWritten();

            $input = $this->readline();

            /*
             * Handle Ctrl+D. It behaves differently in different cases:
             *
             *   1) In an expression, like a function or "if" block, clear the input buffer
             *   2) At top-level session, behave like the exit command
             *   3) When non-interactive, return, because that's the end of stdin
             */
            if ($input === false) {
                if (!$interactive) {
                    return;
                }

                $this->output->writeln('');

                throw new BreakException('Ctrl+D');
            }

            // handle empty input
            if (\trim($input) === '') {
                $this->notifyOutputWritten();
                continue;
            }

            if (!$this->hasCode()) {
                $this->writeLegacyInputSpacer();
            }

            $input = $this->onInput($input);

            if ($this->hasCommand($input) && !$this->inputInOpenStringOrComment($input)) {
                $this->addHistory($input);
                $outputPositions = $this->captureOutputStreamPositions();
                $this->writePhpCommandCollisionHint($input);
                $this->runCommand($input);
                if (!$this->outputWritten && $this->outputWasWrittenSince($outputPositions)) {
                    $this->outputWritten = true;
                    $this->markLegacyOutputWritten();
                }
                $this->notifyOutputWritten();

                if ($interactive && $this->hasValidCode()) {
                    return;
                }

                continue;
            }

            $this->addCode($input);
            if ($interactive) {
                return;
            }
        }
    }

    /**
     * Run execution loop listeners before the shell session.
     */
    protected function beforeRun()
    {
        foreach ($this->loopListeners as $listener) {
            if ($listener instanceof OutputAware) {
                $listener->setOutput($this->output);
            }
        }

        foreach ($this->loopListeners as $listener) {
            $listener->beforeRun($this);
        }
    }

    /**
     * Run execution loop listeners at the start of each loop.
     */
    public function beforeLoop()
    {
        $this->outputWritten = false;
        $this->resetShellOutputWritten();

        foreach ($this->loopListeners as $listener) {
            $listener->beforeLoop($this);
        }
    }

    /**
     * Run execution loop listeners on user input.
     *
     * @param string $input
     */
    public function onInput(string $input): string
    {
        foreach ($this->loopListeners as $listeners) {
            if (($return = $listeners->onInput($this, $input)) !== null) {
                $input = $return;
            }
        }

        return $input;
    }

    /**
     * Run execution loop listeners on code to be executed.
     *
     * @param string $code
     */
    public function onExecute(string $code): string
    {
        $this->errorReporting = \error_reporting();
        $this->enableInteractiveSignalCharsIfNeeded();

        foreach ($this->loopListeners as $listener) {
            if (($return = $listener->onExecute($this, $code)) !== null) {
                $code = $return;
            }
        }

        $output = $this->output;
        if ($output instanceof ConsoleOutput) {
            $output = $output->getErrorOutput();
        }

        $output->writeln(\sprintf('<whisper>%s</whisper>', OutputFormatter::escape($code)), ConsoleOutput::VERBOSITY_DEBUG);

        return $code;
    }

    /**
     * Run execution loop listeners after each loop.
     */
    public function afterLoop()
    {
        $this->disableInteractiveSignalCharsIfNeeded();

        foreach (\array_reverse($this->loopListeners) as $listener) {
            $listener->afterLoop($this);
        }

        $this->notifyOutputWritten();
    }

    /**
     * Report to the interactive readline whether visible output was written.
     */
    private function notifyOutputWritten(): void
    {
        if ($this->output instanceof ShellOutput && $this->output->consumeVisibleOutputWritten()) {
            $this->outputWritten = true;
            $this->markLegacyOutputWritten();
        }

        if ($this->readline instanceof InteractiveReadlineInterface) {
            $this->readline->setOutputWritten($this->outputWritten);
        }
    }

    /**
     * Reset ShellOutput's visible-output tracker.
     */
    private function resetShellOutputWritten(): void
    {
        if ($this->output instanceof ShellOutput) {
            $this->output->consumeVisibleOutputWritten();
        }
    }

    /**
     * Capture write positions for output streams not covered by ShellOutput tracking.
     *
     * @return array<int, int>|null
     */
    private function captureOutputStreamPositions(): ?array
    {
        $outputs = [$this->output];

        if ($this->output instanceof ConsoleOutput) {
            $outputs[] = $this->output->getErrorOutput();
        }

        $positions = [];

        foreach ($outputs as $output) {
            if (!$output instanceof StreamOutput) {
                continue;
            }

            $stream = $output->getStream();
            if (!\is_resource($stream) || \get_resource_type($stream) !== 'stream') {
                continue;
            }

            $position = @\ftell($stream);
            if (!\is_int($position)) {
                continue;
            }

            $positions[(int) $stream] = $position;
        }

        return $positions !== [] ? $positions : null;
    }

    /**
     * Determine whether a command wrote output based on fallback stream movement.
     *
     * This covers outputs that don't report writes explicitly, such as plain
     * StreamOutput instances and stderr writes routed around ShellOutput.
     * If stream positions are unavailable, assume output may have been written
     * to avoid false "no output" frame continuation.
     *
     * @param array<int, int>|null $before
     */
    private function outputWasWrittenSince(?array $before): bool
    {
        if ($before === null) {
            return true;
        }

        $after = $this->captureOutputStreamPositions();
        if ($after === null) {
            return true;
        }

        foreach ($before as $streamId => $position) {
            if (($after[$streamId] ?? $position) > $position) {
                return true;
            }
        }

        foreach ($after as $streamId => $position) {
            if (!isset($before[$streamId]) && $position > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Run execution loop listers after the shell session.
     *
     * @param int $exitCode Exit code from the execution loop
     */
    protected function afterRun(int $exitCode = 0)
    {
        $this->disableInteractiveSignalCharsIfNeeded();

        foreach (\array_reverse($this->loopListeners) as $listener) {
            $listener->afterRun($this, $exitCode);
        }
    }

    /**
     * Enable terminal signal chars during code execution when no SIGINT listener is active.
     *
     * Interactive readline raw mode disables terminal-generated SIGINT by default.
     * When ProcessForker/SignalHandler are unavailable, we temporarily re-enable
     * signal chars so Ctrl-C can still interrupt long-running code.
     */
    private function enableInteractiveSignalCharsIfNeeded(): void
    {
        if (
            $this->interactiveSignalCharsEnabled
            || $this->nonInteractive
            || !($this->readline instanceof InteractiveReadlineInterface)
            || $this->hasSigintExecutionListener()
            || !Tty::supportsStty()
        ) {
            return;
        }

        @\shell_exec('stty isig 2>/dev/null');
        $this->interactiveSignalCharsEnabled = true;
    }

    /**
     * Restore prompt-time terminal signal behavior after execution.
     */
    private function disableInteractiveSignalCharsIfNeeded(): void
    {
        if (!$this->interactiveSignalCharsEnabled) {
            return;
        }

        @\shell_exec('stty -isig 2>/dev/null');
        $this->interactiveSignalCharsEnabled = false;
    }

    /**
     * Check whether any loop listener handles SIGINT during execution.
     */
    private function hasSigintExecutionListener(): bool
    {
        foreach ($this->loopListeners as $listener) {
            if ($listener instanceof ProcessForker || $listener instanceof SignalHandler) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the variables currently in scope.
     *
     * @param array $vars
     */
    public function setScopeVariables(array $vars)
    {
        $this->context->setAll($vars);
    }

    /**
     * Return the set of variables currently in scope.
     *
     * @param bool $includeBoundObject Pass false to exclude 'this'. If you're
     *                                 passing the scope variables to `extract`
     *                                 you _must_ exclude 'this'
     *
     * @return array Associative array of scope variables
     */
    public function getScopeVariables(bool $includeBoundObject = true): array
    {
        $vars = $this->context->getAll();

        if (!$includeBoundObject) {
            unset($vars['this']);
        }

        return $vars;
    }

    /**
     * Return the set of magic variables currently in scope.
     *
     * @param bool $includeBoundObject Pass false to exclude 'this'. If you're
     *                                 passing the scope variables to `extract`
     *                                 you _must_ exclude 'this'
     *
     * @return array Associative array of magic scope variables
     */
    public function getSpecialScopeVariables(bool $includeBoundObject = true): array
    {
        $vars = $this->context->getSpecialVariables();

        if (!$includeBoundObject) {
            unset($vars['this']);
        }

        return $vars;
    }

    /**
     * Return the set of variables currently in scope which differ from the
     * values passed as $currentVars.
     *
     * This is used inside the Execution Loop Closure to pick up scope variable
     * changes made by commands while the loop is running.
     *
     * @param array $currentVars
     *
     * @return array Associative array of scope variables which differ from $currentVars
     */
    public function getScopeVariablesDiff(array $currentVars): array
    {
        $newVars = [];

        foreach ($this->getScopeVariables(false) as $key => $value) {
            if (!\array_key_exists($key, $currentVars) || $currentVars[$key] !== $value) {
                $newVars[$key] = $value;
            }
        }

        return $newVars;
    }

    /**
     * Get the set of unused command-scope variable names.
     *
     * @return array Array of unused variable names
     */
    public function getUnusedCommandScopeVariableNames(): array
    {
        return $this->context->getUnusedCommandScopeVariableNames();
    }

    /**
     * Get the set of variable names currently in scope.
     *
     * @return array Array of variable names
     */
    public function getScopeVariableNames(): array
    {
        return \array_keys($this->context->getAll());
    }

    /**
     * Get a scope variable value by name.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getScopeVariable(string $name)
    {
        return $this->context->get($name);
    }

    /**
     * Set the bound object ($this variable) for the interactive shell.
     *
     * @param object|null $boundObject
     */
    public function setBoundObject($boundObject)
    {
        $this->context->setBoundObject($boundObject);
    }

    /**
     * Get the bound object ($this variable) for the interactive shell.
     *
     * @return object|null
     */
    public function getBoundObject()
    {
        return $this->context->getBoundObject();
    }

    /**
     * Set the bound class (self) for the interactive shell.
     *
     * @param string|null $boundClass
     */
    public function setBoundClass($boundClass)
    {
        $this->context->setBoundClass($boundClass);
    }

    /**
     * Get the bound class (self) for the interactive shell.
     *
     * @return string|null
     */
    public function getBoundClass()
    {
        return $this->context->getBoundClass();
    }

    /**
     * Add includes, to be parsed and executed before running the interactive shell.
     *
     * @param array $includes
     */
    public function setIncludes(array $includes = [])
    {
        $this->includes = $includes;
    }

    /**
     * Get PHP files to be parsed and executed before running the interactive shell.
     *
     * @return string[]
     */
    public function getIncludes(): array
    {
        return \array_merge($this->config->getDefaultIncludes(), $this->includes);
    }

    /**
     * Check whether this shell's code buffer contains code.
     *
     * @return bool True if the code buffer contains code
     */
    public function hasCode(): bool
    {
        return $this->pendingInput->hasCode();
    }

    /**
     * Check whether the code in this shell's code buffer is valid.
     *
     * If the code is valid, the code buffer should be flushed and evaluated.
     *
     * @return bool True if the code buffer content is valid
     */
    protected function hasValidCode(): bool
    {
        return $this->pendingInput->hasValidCode();
    }

    /**
     * Add code to the code buffer.
     *
     * @param string $code
     * @param bool   $silent
     */
    public function addCode(string $code, bool $silent = false)
    {
        $this->appendCode($code, $silent);
    }

    /**
     * Add code to the pending buffer or active legacy continuation buffer.
     *
     * @param string $code
     * @param bool   $silent
     * @param bool   $allowLegacyBufferAppend
     */
    private function appendCode(string $code, bool $silent = false, bool $allowLegacyBufferAppend = true): void
    {
        $this->boot();

        if ($allowLegacyBufferAppend && $this->readline instanceof LegacyReadline && $this->readline->hasBuffer()) {
            $this->readline->append($code);

            return;
        }

        try {
            $this->pendingInput->appendLine($code, $silent);
            $cleanedCode = $this->cleaner->clean($this->pendingInput->getPendingCodeBuffer(), $this->config->requireSemicolons());
            $this->pendingInput->setPendingCode($cleanedCode);

            if (!$silent && $cleanedCode !== false) {
                $this->suppressReturnValue = $this->shouldSuppressReturnValue();
                $this->writeCleanerMessages();
            }
        } catch (\Throwable $e) {
            // Add failed pending code blocks to the readline history.
            $this->addPendingCodeBufferToHistory();

            throw $e;
        }
    }

    /**
     * Check whether the current code buffer ends with an unnecessary semicolon.
     *
     * @see Configuration::semicolonsSuppressReturn()
     */
    private function shouldSuppressReturnValue(): bool
    {
        if ($this->config->semicolonsSuppressReturn() === false) {
            return false;
        }

        $tokens = @\token_get_all('<?php '.\implode(\PHP_EOL, $this->pendingInput->getPendingCodeBuffer()));
        [$lastToken, $index] = $this->lastNonCommentToken($tokens);

        if ($lastToken !== ';') {
            return false;
        }

        $requireDouble = $this->config->semicolonsSuppressReturn() === Configuration::SEMICOLONS_SUPPRESS_RETURN_DOUBLE
            || $this->config->requireSemicolons();

        if (!$requireDouble) {
            // When semicolons are optional, a single ; is unnecessary
            return true;
        }

        // Require a double semicolon (`;;`) to suppress
        return $index !== null && $this->lastNonCommentToken($tokens, $index - 1)[0] === ';';
    }

    /**
     * Get the last non-comment token from a tokenized PHP snippet.
     *
     * @param array $tokens Token array from token_get_all()
     *
     * @return array Token and index pair: [token, index] or [null, null]
     */
    private function lastNonCommentToken(array $tokens, ?int $offset = null): array
    {
        $offset ??= \count($tokens) - 1;

        for ($i = $offset; $i >= 0; $i--) {
            $token = $tokens[$i];

            if (\is_array($token) && \in_array($token[0], [\T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT, \T_OPEN_TAG], true)) {
                continue;
            }

            return [$token, $i];
        }

        return [null, null];
    }

    /**
     * Check whether the pending code buffer plus current input is in an open string or comment.
     */
    private function inputInOpenStringOrComment(string $input): bool
    {
        if (!$this->hasCode()) {
            return false;
        }

        $code = $this->pendingInput->getPendingCodeBuffer();
        $code[] = $input;
        $tokens = @\token_get_all('<?php '.\implode(\PHP_EOL, $code));
        $last = \array_pop($tokens);

        return $last === '"' || $last === '`' ||
            (\is_array($last) && \in_array($last[0], [\T_ENCAPSED_AND_WHITESPACE, \T_START_HEREDOC, \T_COMMENT], true));
    }

    /**
     * Set the pending code buffer.
     *
     * This is mostly used by `Shell::execute`. Any existing code in the input
     * buffer is pushed onto a stack and will come back after this new code is
     * executed.
     *
     * @throws \InvalidArgumentException if $code isn't a complete statement
     *
     * @param string $code
     * @param bool   $silent
     */
    private function setCode(string $code, bool $silent = false)
    {
        if ($this->hasCode()) {
            $this->pendingInput->pushCurrentCode();
        }

        $this->clearPendingCode();
        try {
            $this->appendCode($code, $silent, false);
        } catch (\Throwable $e) {
            $this->popCodeStack();

            throw $e;
        }

        if (!$this->hasValidCode()) {
            $this->popCodeStack();

            throw new \InvalidArgumentException('Unexpected end of input');
        }
    }

    /**
     * Get the current code buffer.
     *
     * This is useful for callers which still inspect the shell's pending code.
     *
     * @return string[]
     *
     * @deprecated pending input inspection is being removed from Shell internals
     */
    public function getCodeBuffer(): array
    {
        return $this->getPendingCodeBuffer();
    }

    /**
     * Get the current executable pending code buffer.
     *
     * @return string[]
     */
    public function getPendingCodeBuffer(): array
    {
        return $this->pendingInput->getPendingCodeBuffer();
    }

    /**
     * Run a Psy Shell command given the user input.
     *
     * @throws \InvalidArgumentException if the input is not a valid command
     *
     * @param string $input User input string
     *
     * @return mixed Who knows?
     */
    protected function runCommand(string $input)
    {
        $command = $this->getCommand($input);

        if (empty($command)) {
            throw new \InvalidArgumentException('Command not found: '.$input);
        }

        if ($logger = $this->config->getLogger()) {
            $logger->logCommand($input);
        }

        $input = new ShellInput(\str_replace('\\', '\\\\', \rtrim($input, " \t\n\r\0\x0B;")));

        if (!$input->hasParameterOption(['--help', '-h'])) {
            try {
                return $command->run($input, $this->output);
            } catch (\Exception $e) {
                if (!self::needsInputHelp($e)) {
                    throw $e;
                }

                $this->writeException($e);

                $this->writeSeparator($this->output);
            }
        }

        $helpCommand = $this->get('help');
        if (!$helpCommand instanceof Command\HelpCommand) {
            throw new RuntimeException('Invalid help command instance');
        }
        $helpCommand->setCommand($command);
        $helpCommand->setCommandInput($input);

        return $helpCommand->run(new StringInput(''), $this->output);
    }

    /**
     * Check whether a given input error would benefit from --help.
     *
     * @return bool
     */
    private static function needsInputHelp(\Exception $e): bool
    {
        if (!($e instanceof \RuntimeException || $e instanceof SymfonyConsoleException)) {
            return false;
        }

        $inputErrors = [
            'Not enough arguments',
            'option does not accept a value',
            'option does not exist',
            'option requires a value',
        ];

        $msg = $e->getMessage();
        foreach ($inputErrors as $errorMsg) {
            if (\strpos($msg, $errorMsg) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whisper messages from CodeCleaner passes.
     */
    private function writeCleanerMessages(): void
    {
        if (!isset($this->output)) {
            return;
        }

        $output = $this->output;
        if ($output instanceof ConsoleOutput) {
            $output = $output->getErrorOutput();
        }

        foreach ($this->cleaner->getMessages() as $message) {
            $output->writeln(\sprintf('<whisper>%s</whisper>', OutputFormatter::escape($message)));
        }
    }

    /**
     * Reset the current pending code buffer.
     *
     * This should be run after evaluating user input, catching exceptions, or
     * on demand by commands such as BufferCommand.
     *
     * @deprecated pending input reset is being removed from Shell internals
     */
    public function resetCodeBuffer()
    {
        $this->clearPendingCode();
    }

    /**
     * Clear the current executable pending code buffer.
     */
    public function clearPendingCodeBuffer(): void
    {
        $this->clearPendingCode();
    }

    /**
     * Inject input into the input buffer.
     *
     * This is useful for commands which want to replay history.
     *
     * @param string|array $input
     * @param bool         $silent
     */
    public function addInput($input, bool $silent = false)
    {
        foreach ((array) $input as $line) {
            $this->inputBuffer[] = $silent ? new SilentInput($line) : $line;
        }
    }

    /**
     * Flush the current executable pending code buffer.
     *
     * If the code buffer is valid, resets the code buffer and returns the
     * current code.
     *
     * @return string|null PHP code buffer contents
     */
    public function flushCode()
    {
        if ($this->hasValidCode()) {
            $this->addPendingCodeBufferToHistory();
            $code = $this->pendingInput->getPendingCode();
            $this->popCodeStack();

            return $code;
        }

        return null;
    }

    /**
     * Reset pending code and restore any code pushed during `execute` calls.
     */
    private function popCodeStack()
    {
        $this->pendingInput->restorePreviousCode();
    }

    /**
     * (Possibly) add a line to the readline history.
     *
     * Like Bash, if the line starts with a space character, it will be omitted
     * from history. Note that an entire block multi-line code input will be
     * omitted iff the first line begins with a space.
     *
     * Additionally, if a line is "silent", i.e. it was initially added with the
     * silent flag, it will also be omitted.
     *
     * @param string|SilentInput $line
     */
    private function addHistory($line)
    {
        if ($line instanceof SilentInput) {
            return;
        }

        // Skip empty lines and lines starting with a space
        if (\trim($line) !== '' && \substr($line, 0, 1) !== ' ') {
            $this->readline->addHistory($line);
        }
    }

    /**
     * Filter silent input from code buffer, write the rest to readline history.
     */
    private function addPendingCodeBufferToHistory()
    {
        $codeBuffer = \array_filter($this->pendingInput->getPendingCodeBuffer(), fn ($line) => !$line instanceof SilentInput);

        $this->addHistory(\implode("\n", $codeBuffer));
    }

    /**
     * Clear the shell's pending execution state.
     */
    private function clearPendingCode(): void
    {
        $this->pendingInput->clear();
    }

    /**
     * Get the current evaluation scope namespace.
     *
     * @see CodeCleaner::getNamespace
     *
     * @return string|null Current code namespace
     */
    public function getNamespace()
    {
        $this->boot();

        if ($namespace = $this->cleaner->getNamespace()) {
            return \implode('\\', $namespace);
        }

        return null;
    }

    /**
     * Write a string to stdout.
     *
     * This is used by the shell loop for rendering output from evaluated code.
     *
     * @param string $out
     * @param int    $phase Output buffering phase
     *
     * @return string Empty string
     */
    public function writeStdout(string $out, int $phase = \PHP_OUTPUT_HANDLER_END): string
    {
        if ($phase & \PHP_OUTPUT_HANDLER_START) {
            if ($this->output instanceof ShellOutput) {
                $this->output->startPaging();
            }
        }

        $isCleaning = $phase & \PHP_OUTPUT_HANDLER_CLEAN;

        // Incremental flush
        if ($out !== '' && !$isCleaning) {
            $this->markLegacyOutputWritten();
            $this->output->write($out, false, OutputInterface::OUTPUT_RAW);
            $this->outputWantsNewline = (\substr($out, -1) !== "\n");
            $this->stdoutBuffer .= $out;
            $this->outputWritten = true;
        }

        // Output buffering is done!
        if ($phase & \PHP_OUTPUT_HANDLER_END) {
            // Write an extra newline if stdout didn't end with one
            if ($this->outputWantsNewline) {
                if (!$this->config->rawOutput() && !$this->config->outputIsPiped()) {
                    $this->output->writeln(\sprintf('<whisper>%s</whisper>', $this->config->useUnicode() ? '⏎' : '\\n'));
                } else {
                    $this->output->writeln('');
                }
                $this->outputWantsNewline = false;
            }

            // Save the stdout buffer as $__out
            if ($this->stdoutBuffer !== '') {
                $this->context->setLastStdout($this->stdoutBuffer);
                $this->stdoutBuffer = '';
            }

            if ($this->output instanceof ShellOutput) {
                $this->output->stopPaging();
            }
        }

        return '';
    }

    /**
     * Write a return value to stdout.
     *
     * The return value is formatted or pretty-printed, and rendered in a
     * visibly distinct manner (in this case, as cyan).
     *
     * @see self::presentValue
     *
     * @param mixed $ret
     * @param bool  $rawOutput Write raw var_export-style values
     */
    public function writeReturnValue($ret, bool $rawOutput = false)
    {
        $this->lastExecSuccess = true;

        if ($ret instanceof NoReturnValue) {
            $this->suppressReturnValue = false;

            return;
        }

        $this->context->setReturnValue($ret);

        // Don't display the return value, but $_ is still captured above.
        if ($this->suppressReturnValue) {
            $this->suppressReturnValue = false;

            return;
        }

        if ($rawOutput) {
            $formatted = \var_export($ret, true);
        } else {
            $prompt = $this->config->theme()->returnValue();
            $indent = \str_repeat(' ', \strlen($prompt));
            $formatted = $this->presentValue($ret);
            $formatter = $this->output->getFormatter();
            $formattedPrompt = ($formatter->hasStyle('whisper') && $formatter->isDecorated())
                ? $formatter->getStyle('whisper')->apply($prompt)
                : $prompt;

            $formatted = $formattedPrompt.\str_replace(\PHP_EOL, \PHP_EOL.$indent, $formatted);
        }

        $this->outputWritten = true;
        $this->markLegacyOutputWritten();

        if ($this->output instanceof ShellOutput) {
            $this->output->page($formatted, OutputInterface::OUTPUT_RAW);
        } else {
            $this->output->writeln($formatted, OutputInterface::OUTPUT_RAW);
        }
    }

    /**
     * Renders a caught Exception or Error.
     *
     * Exceptions are formatted according to severity. ErrorExceptions which were
     * warnings or Strict errors aren't rendered as harshly as real errors.
     *
     * Stores $e as the last Exception in the Shell Context.
     *
     * @param \Throwable $e An exception or error instance
     */
    public function writeException(\Throwable $e)
    {
        // No need to write the break exception during a non-interactive run.
        if ($e instanceof BreakException && $this->nonInteractive) {
            $this->clearPendingCode();

            return;
        }

        // Break exceptions don't count :)
        if (!$e instanceof BreakException) {
            $this->lastExecSuccess = false;
            $this->context->setLastException($e);
            $this->outputWritten = true;
        }

        $this->markLegacyOutputWritten();

        $output = $this->output;
        if ($output instanceof ConsoleOutput) {
            $output = $output->getErrorOutput();
        }

        $this->writeExceptionHeader($output, $e);
        if ($e instanceof BreakException) {
            $this->writeSpacer($output);
        }

        // Include an exception trace (as long as this isn't a BreakException).
        if (!$e instanceof BreakException && $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $trace = TraceFormatter::formatTrace($e);
            if (\count($trace) !== 0) {
                $this->writeSeparator($output);
                $output->write($trace, true);
            }
        }

        $this->clearPendingCode();
    }

    /**
     * Check whether the last exec was successful.
     *
     * Returns true if a return value was logged rather than an exception.
     */
    public function getLastExecSuccess(): bool
    {
        return $this->lastExecSuccess;
    }

    /**
     * Check whether the shell is using a compact theme.
     */
    public function isCompactTheme(): bool
    {
        return $this->config->theme()->compact();
    }

    /**
     * Write a formatted exception header with optional details and compact-aware spacing.
     */
    public function writeExceptionHeader(OutputInterface $output, \Throwable $e): void
    {
        $output->writeln($this->formatException($e));

        if ($details = $this->formatExceptionDetails($e)) {
            $output->writeln($details, OutputInterface::OUTPUT_RAW);
        }
    }

    /**
     * Helper for formatting an exception or error for writeException().
     *
     * @todo extract this to somewhere it makes more sense
     *
     * @param \Throwable $e
     */
    public function formatException(\Throwable $e): string
    {
        $indent = $this->config->theme()->compact() ? '' : '  ';

        if ($e instanceof BreakException) {
            return \sprintf('%s<fg=black;bg=cyan> INFO </> %s.', $indent, \rtrim($e->getRawMessage(), '.'));
        } elseif ($e instanceof InterruptException) {
            return \sprintf('%s<warning> INTERRUPT </warning> %s.', $indent, $e->getRawMessage());
        } elseif ($e instanceof PsyException) {
            $message = $e->getLine() > 1
                ? \sprintf('%s in %s on line %d', $e->getRawMessage(), $e->getFile(), $e->getLine())
                : \sprintf('%s in %s', $e->getRawMessage(), $e->getFile());

            $messageLabel = \strtoupper($this->getMessageLabel($e));
        } else {
            $message = $e->getMessage();
            $messageLabel = $this->getMessageLabel($e);
        }

        $message = \preg_replace(
            [
                "#(?:[A-Za-z]:)?[\\\\/][^\\s]*?[\\\\/]src[\\\\/]Execution(?:Loop)?Closure\\.php\\(\\d+\\) : eval\\(\\)'d code#",
                "#\\bsrc[\\\\/]Execution(?:Loop)?Closure\\.php\\(\\d+\\) : eval\\(\\)'d code#",
            ],
            "eval()'d code",
            $message
        );

        $message = \str_replace(" in eval()'d code", '', $message);
        $message = \trim($message);

        // Ensures the given string ends with punctuation...
        if (!empty($message) && !\in_array(\substr($message, -1), ['.', '?', '!', ':'])) {
            $message = "$message.";
        }

        // Ensures the given message only contains relative paths...
        $message = \str_replace(\getcwd().\DIRECTORY_SEPARATOR, '', $message);

        $severity = ($e instanceof \ErrorException) ? $this->getSeverity($e) : 'error';

        return \sprintf('%s<%s> %s </%s> %s', $indent, $severity, $messageLabel, $severity, OutputFormatter::escape($message));
    }

    /**
     * Format exception details (if provided) for display.
     */
    protected function formatExceptionDetails(\Throwable $e): ?string
    {
        $formatter = $this->config->getExceptionDetails();
        if ($formatter === null) {
            return null;
        }

        try {
            $details = $formatter($e);
        } catch (\Throwable $_e) {
            return null;
        }

        if ($details === null) {
            return null;
        }

        $rendered = $this->presentValue($details);
        $compact = $this->config->theme()->compact();
        $indent = $compact ? '  ' : '    ';
        $prefix = $compact ? '' : \PHP_EOL;

        return $prefix.\implode(\PHP_EOL, \array_map(static function ($line) use ($indent) {
            return $indent.$line;
        }, \explode(\PHP_EOL, $rendered)));
    }

    /**
     * Write a single blank spacer line in non-compact mode.
     */
    public function writeSpacer(OutputInterface $output): void
    {
        if (!$this->isCompactTheme()) {
            $output->writeln('');
        }
    }

    /**
     * Write a separator line with compact-aware spacing.
     */
    public function writeSeparator(OutputInterface $output): void
    {
        $this->writeSpacer($output);
        $output->writeln('--');
        $this->writeSpacer($output);
    }

    /**
     * Check whether the shell is using legacy readline with non-compact spacing.
     */
    private function usesLegacySpacerLayout(): bool
    {
        return $this->readline instanceof LegacyReadline && !$this->isCompactTheme();
    }

    /**
     * Write a single blank spacer line for legacy readline.
     */
    private function writeLegacySpacer(): void
    {
        if (!$this->usesLegacySpacerLayout() || $this->writingLegacySpacer) {
            return;
        }

        $this->writingLegacySpacer = true;

        try {
            $this->output->writeln('');
        } finally {
            $this->resetShellOutputWritten();
            $this->writingLegacySpacer = false;
        }
    }

    /**
     * Write the spacer separating the previous output block from the next prompt.
     */
    private function writeLegacyPromptSpacer(): void
    {
        if (!$this->legacyNeedsPromptSpacer) {
            return;
        }

        $this->writeLegacySpacer();
        $this->legacyNeedsPromptSpacer = false;
    }

    /**
     * Write the spacer separating submitted input from subsequent output.
     */
    private function writeLegacyInputSpacer(): void
    {
        $this->writeLegacySpacer();
        $this->legacyNeedsPromptSpacer = false;
    }

    /**
     * Mark that visible output was written and the next prompt needs spacing.
     */
    private function markLegacyOutputWritten(): void
    {
        if ($this->usesLegacySpacerLayout()) {
            $this->legacyNeedsPromptSpacer = true;
        }
    }

    /**
     * Helper for getting an output style for the given ErrorException's level.
     *
     * @param \ErrorException $e
     */
    protected function getSeverity(\ErrorException $e): string
    {
        $severity = $e->getSeverity();
        if ($severity & \error_reporting()) {
            switch ($severity) {
                case \E_WARNING:
                case \E_NOTICE:
                case \E_CORE_WARNING:
                case \E_COMPILE_WARNING:
                case \E_USER_WARNING:
                case \E_USER_NOTICE:
                case \E_USER_DEPRECATED:
                case \E_DEPRECATED:
                    return 'warning';

                default:
                    if ((\PHP_VERSION_ID < 80400) && $severity === \E_STRICT) {
                        return 'warning';
                    }

                    return 'error';
            }
        } else {
            // Since this is below the user's reporting threshold, it's always going to be a warning.
            return 'warning';
        }
    }

    /**
     * Helper for getting an output style for the given ErrorException's level.
     *
     * @param \Throwable $e
     */
    protected function getMessageLabel(\Throwable $e): string
    {
        if ($e instanceof \ErrorException) {
            $severity = $e->getSeverity();

            if ($severity & \error_reporting()) {
                switch ($severity) {
                    case \E_WARNING:
                        return 'Warning';
                    case \E_NOTICE:
                        return 'Notice';
                    case \E_CORE_WARNING:
                        return 'Core Warning';
                    case \E_COMPILE_WARNING:
                        return 'Compile Warning';
                    case \E_USER_WARNING:
                        return 'User Warning';
                    case \E_USER_NOTICE:
                        return 'User Notice';
                    case \E_USER_DEPRECATED:
                        return 'User Deprecated';
                    case \E_DEPRECATED:
                        return 'Deprecated';
                    default:
                        if ((\PHP_VERSION_ID < 80400) && $severity === \E_STRICT) {
                            return 'Strict';
                        }
                }
            }
        }

        if ($e instanceof PsyException || $e instanceof SymfonyConsoleException) {
            $exceptionShortName = (new \ReflectionClass($e))->getShortName();
            $typeParts = \preg_split('/(?=[A-Z])/', $exceptionShortName);

            switch ($exceptionShortName) {
                case 'RuntimeException':
                case 'LogicException':
                    // These ones look weird without 'Exception'
                    break;
                default:
                    if (\end($typeParts) === 'Exception') {
                        \array_pop($typeParts);
                    }
                    break;
            }

            return \trim(\strtoupper(\implode(' ', $typeParts)));
        }

        return \get_class($e);
    }

    /**
     * Execute code in the shell execution context.
     *
     * @param string $code
     * @param bool   $throwExceptions
     *
     * @return mixed
     */
    public function execute(string $code, bool $throwExceptions = false)
    {
        $this->boot();

        $this->setCode($code, true);

        if ($logger = $this->config->getLogger()) {
            $logger->logExecute($code);
        }

        $closure = new ExecutionClosure($this);

        if ($throwExceptions) {
            return $closure->execute();
        }

        try {
            return $closure->execute();
        } catch (BreakException $_e) {
            // Re-throw BreakException so it can propagate exit codes
            throw $_e;
        } catch (\Throwable $_e) {
            $this->writeException($_e);
        }
    }

    /**
     * Helper for throwing an ErrorException.
     *
     * This allows us to:
     *
     *     set_error_handler([$psysh, 'handleError']);
     *
     * Unlike ErrorException::throwException, this error handler respects error
     * levels; i.e. it logs warnings and notices, but doesn't throw exceptions.
     * This should probably only be used in the inner execution loop of the
     * shell, as most of the time a thrown exception is much more useful.
     *
     * If the error type matches the `errorLoggingLevel` config, it will be
     * logged as well, regardless of the `error_reporting` level.
     *
     * @see \Psy\Exception\ErrorException::throwException
     * @see \Psy\Shell::writeException
     *
     * @throws \Psy\Exception\ErrorException depending on the error level
     *
     * @param int    $errno   Error type
     * @param string $errstr  Message
     * @param string $errfile Filename
     * @param int    $errline Line number
     */
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        // This is an error worth throwing.
        //
        // n.b. Technically we can't handle all of these in userland code, but
        // we'll list 'em all for good measure
        if ($errno & (\E_ERROR | \E_PARSE | \E_CORE_ERROR | \E_COMPILE_ERROR | \E_USER_ERROR | \E_RECOVERABLE_ERROR)) {
            ErrorException::throwException($errno, $errstr, $errfile, $errline);
        }

        // When errors are suppressed, the error_reporting value will differ
        // from when we started executing. In that case, we won't log errors.
        $errorsSuppressed = $this->errorReporting !== null && $this->errorReporting !== \error_reporting();

        // Otherwise log it and continue.
        if ($errno & \error_reporting() || (!$errorsSuppressed && ($errno & $this->config->errorLoggingLevel()))) {
            $this->writeException(new ErrorException($errstr, 0, $errno, $errfile, $errline));
        }
    }

    /**
     * Format a value for display.
     *
     * @see Presenter::present
     *
     * @param mixed $val
     *
     * @return string Formatted value
     */
    protected function presentValue($val): string
    {
        return $this->config->getPresenter()->present($val, null, Presenter::RAW);
    }

    /**
     * Get a command (if one exists) for the current input string.
     *
     * @param string $input
     *
     * @return BaseCommand|null
     */
    protected function getCommand(string $input)
    {
        $input = new StringInput($input);
        if ($name = $input->getFirstArgument()) {
            return $this->get($name);
        }

        return null;
    }

    /**
     * Check whether a command is set for the current input string.
     *
     * @param string $input
     *
     * @return bool True if the shell has a command for the given input
     */
    public function hasCommand(string $input): bool
    {
        $name = $this->extractCommandName($input);

        return $name !== null && $this->has($name);
    }

    /**
     * Extract the command name (first word) from input.
     */
    private function extractCommandName(string $input): ?string
    {
        if (\preg_match('/([^\s]+?)(?:\s|$)/A', \ltrim($input), $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * Write a hint if the input collides with a callable PHP function.
     */
    private function writePhpCommandCollisionHint(string $input): void
    {
        $function = $this->getPhpCommandCollisionFunction($input);
        if ($function === null) {
            return;
        }

        $label = OutputFormatter::escape($function.'()');
        $this->output->writeln(\sprintf(
            '<whisper>Input also matches PHP function %s; prefix with ";" to execute PHP instead.</whisper>',
            $label
        ));
    }

    /**
     * Return the callable PHP function name when a command input also resolves as a direct PHP call.
     */
    private function getPhpCommandCollisionFunction(string $input): ?string
    {
        $commandName = $this->extractCommandName($input);
        if ($commandName === null || $this->cleaner === null) {
            return null;
        }

        return $this->cleaner->getCallableFunctionForInput($input, $commandName);
    }

    /**
     * Get the current input prompt.
     *
     * @return string|null
     */
    protected function getPrompt()
    {
        if ($this->output->isQuiet()) {
            return null;
        }

        return $this->config->theme()->prompt();
    }

    /**
     * Read a line of user input.
     *
     * This will return a line from the input buffer (if any exist). Otherwise,
     * it will ask the user for input.
     *
     * If readline is enabled, this delegates to readline. Otherwise, it's an
     * ugly `fgets` call.
     *
     * @param bool $interactive
     *
     * @return string|false One line of user input
     */
    protected function readline(bool $interactive = true)
    {
        $prompt = $this->config->theme()->replayPrompt();

        if (!empty($this->inputBuffer)) {
            $line = \array_shift($this->inputBuffer);
            if (!$line instanceof SilentInput) {
                $this->output->writeln(\sprintf('<whisper>%s</whisper><aside>%s</aside>', $prompt, OutputFormatter::escape($line)));
            }

            return $line;
        }

        $this->writeLegacyPromptSpacer();

        // Interactive readline manages bracketed paste internally
        $usesInteractiveReadline = $this->readline instanceof InteractiveReadlineInterface;
        $bracketedPaste = $interactive && $this->config->useBracketedPaste() && !$usesInteractiveReadline;

        if ($bracketedPaste) {
            \printf("\e[?2004h"); // Enable bracketed paste
        }

        $line = $this->readline->readline($this->getPrompt());

        if ($bracketedPaste) {
            \printf("\e[?2004l"); // ... and disable it again
        }

        return $line;
    }

    /**
     * Get the shell output header.
     */
    protected function getHeader(): string
    {
        return \sprintf('<whisper>%s by Justin Hileman</whisper>', self::getVersionHeader($this->config->useUnicode()));
    }

    /**
     * Get the current version of Psy Shell.
     *
     * @deprecated call self::getVersionHeader instead
     */
    public function getVersion(): string
    {
        @\trigger_error('`getVersion` is deprecated; call `self::getVersionHeader` instead.', \E_USER_DEPRECATED);

        return self::getVersionHeader($this->config->useUnicode());
    }

    /**
     * Get a pretty header including the current version of Psy Shell.
     *
     * @param bool $useUnicode
     */
    public static function getVersionHeader(bool $useUnicode = false): string
    {
        $separator = $useUnicode ? '—' : '-';

        return \sprintf('Psy Shell %s (PHP %s %s %s)', self::VERSION, \PHP_VERSION, $separator, \PHP_SAPI);
    }

    /**
     * Get a PHP manual database instance.
     *
     * @deprecated Use getManual() instead for unified access to all manual formats
     *
     * @return \PDO|null
     */
    public function getManualDb()
    {
        return $this->config->getManualDb();
    }

    /**
     * Get a PHP manual loader.
     *
     * @return Manual\ManualInterface|null
     */
    public function getManual()
    {
        return $this->config->getManual();
    }

    /**
     * Initialize tab completion matchers.
     *
     * If tab completion is enabled this adds tab completion matchers to the
     * auto completer and sets context if needed.
     */
    protected function initializeTabCompletion()
    {
        if (!$this->config->useTabCompletion() || $this->readline instanceof InteractiveReadlineInterface) {
            return;
        }

        $this->autoCompleter = $this->config->getAutoCompleter();
        if ($this->completionEngine === null) {
            throw new \LogicException('Completion engine must be initialized before tab completion.');
        }

        $this->autoCompleter->setCompletionEngine($this->completionEngine);
        $this->autoCompleter->activate();
    }

    /**
     * Initialize context-aware completion for the active readline frontend.
     */
    private function initializeCompletionEngine(): void
    {
        $completion = new CompletionEngine($this->context, $this->cleaner);
        $this->completionEngine = $completion;

        $allCommands = $this->all();
        $commandContextRefiner = new CommandContextRefiner($allCommands);
        $commandSource = new CommandSource($allCommands);
        $commandOptionSource = new CommandOptionSource($allCommands);
        $commandArgumentSource = new CommandArgumentSource($allCommands);
        $completion->addRefiner($commandContextRefiner);
        $this->commandCompletion[] = $commandContextRefiner;
        $this->commandCompletion[] = $commandSource;
        $this->commandCompletion[] = $commandOptionSource;
        $this->commandCompletion[] = $commandArgumentSource;

        $sources = [
            $commandSource,
            $commandOptionSource,
            $commandArgumentSource,
        ];

        if ($this->readline instanceof InteractiveReadlineInterface) {
            $sources[] = new HistorySource($this->readline->getHistory());
        }

        $completion->registerDefaultSources($sources);

        foreach ($this->pendingCompletionSources as $source) {
            $completion->addSource($source);
        }
        $this->pendingCompletionSources = [];

        $this->addLegacyMatchersToCompletionEngine($this->getDefaultCompletionCompatibilityMatchers());

        if (!empty($this->matchers)) {
            $this->addLegacyMatchersToCompletionEngine($this->matchers);
        }

        if ($this->readline instanceof InteractiveReadlineInterface) {
            $this->readline->setCompletionEngine($completion);
        }
    }

    /**
     * Filter out objects already present in an existing array.
     */
    protected function deduplicateObjects(array $new, array $existing): array
    {
        $seen = [];
        foreach ($existing as $item) {
            if (\is_object($item)) {
                $seen[\spl_object_id($item)] = true;
            }
        }

        return \array_values(\array_filter(
            $new,
            fn ($item) => !\is_object($item) || !isset($seen[\spl_object_id($item)])
        ));
    }

    /**
     * Add legacy matchers to completion engine via adapter.
     *
     * @param array $matchers Legacy matchers to adapt
     */
    private function addLegacyMatchersToCompletionEngine(array $matchers): void
    {
        if ($this->completionEngine === null) {
            throw new \LogicException('Completion engine is not set');
        }

        // Set context on context-aware matchers
        foreach ($matchers as $matcher) {
            if ($matcher instanceof ContextAware) {
                $matcher->setContext($this->context);
            }
        }

        // MatcherAdapterSource filters out matchers superseded by new-style sources
        $this->completionEngine->addSource(new MatcherAdapterSource($matchers));
    }

    /**
     * Matcher-only built-ins that do not yet have source-based equivalents.
     *
     * @return Matcher\AbstractMatcher[]
     */
    protected function getDefaultCompletionCompatibilityMatchers(): array
    {
        return [
            new Matcher\ClassMethodDefaultParametersMatcher(),
            new Matcher\ObjectMethodDefaultParametersMatcher(),
            new Matcher\FunctionDefaultParametersMatcher(),
        ];
    }

    /**
     * @todo Implement prompt to start update
     *
     * @return void|string
     */
    protected function writeVersionInfo()
    {
        if (\PHP_SAPI !== 'cli') {
            return;
        }

        try {
            $client = $this->config->getChecker();
            if (!$client->isLatest()) {
                $this->output->writeln(\sprintf('<whisper>New version is available at psysh.org/psysh (current: %s, latest: %s)</whisper>', self::VERSION, $client->getLatest()));
            }
        } catch (\InvalidArgumentException $e) {
            $this->output->writeln($e->getMessage());
        }
    }

    /**
     * Check for manual updates and write notification if available.
     */
    protected function writeManualUpdateInfo()
    {
        if (\PHP_SAPI !== 'cli') {
            return;
        }

        try {
            $checker = $this->config->getManualChecker();
            if ($checker && !$checker->isLatest()) {
                $this->output->writeln(\sprintf('<whisper>New PHP manual is available (latest: %s). Update with `doc --update-manual`</whisper>', $checker->getLatest()));
            }
        } catch (\Exception $e) {
            // Silently ignore manual update check failures
        }
    }

    /**
     * Write a startup message if set.
     */
    protected function writeStartupMessage()
    {
        $message = $this->config->getStartupMessage();
        if ($message !== null && $message !== '') {
            $this->output->writeln($message);
        }
    }
}
