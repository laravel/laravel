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
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for all commands.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Command implements SignalableCommandInterface
{
    // see https://tldp.org/LDP/abs/html/exitcodes.html
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;

    private ?Application $application = null;
    private ?string $name = null;
    private ?string $processTitle = null;
    private array $aliases = [];
    private InputDefinition $definition;
    private bool $hidden = false;
    private string $help = '';
    private string $description = '';
    private ?InputDefinition $fullDefinition = null;
    private bool $ignoreValidationErrors = false;
    private ?InvokableCommand $code = null;
    private array $synopsis = [];
    private array $usages = [];
    private ?HelperSet $helperSet = null;

    /**
     * @deprecated since Symfony 7.3, use the #[AsCommand] attribute instead
     */
    public static function getDefaultName(): ?string
    {
        trigger_deprecation('symfony/console', '7.3', 'Method "%s()" is deprecated and will be removed in Symfony 8.0, use the #[AsCommand] attribute instead.', __METHOD__);

        if ($attribute = (new \ReflectionClass(static::class))->getAttributes(AsCommand::class)) {
            return $attribute[0]->newInstance()->name;
        }

        return null;
    }

    /**
     * @deprecated since Symfony 7.3, use the #[AsCommand] attribute instead
     */
    public static function getDefaultDescription(): ?string
    {
        trigger_deprecation('symfony/console', '7.3', 'Method "%s()" is deprecated and will be removed in Symfony 8.0, use the #[AsCommand] attribute instead.', __METHOD__);

        if ($attribute = (new \ReflectionClass(static::class))->getAttributes(AsCommand::class)) {
            return $attribute[0]->newInstance()->description;
        }

        return null;
    }

    /**
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(?string $name = null, ?callable $code = null)
    {
        if (null !== $code) {
            if (!\is_object($code) || $code instanceof \Closure) {
                throw new InvalidArgumentException(\sprintf('The command must be an instance of "%s" or an invokable object.', self::class));
            }
            /** @var AsCommand $attribute */
            $attribute = ((new \ReflectionObject($code))->getAttributes(AsCommand::class)[0] ?? null)?->newInstance()
                ?? throw new LogicException(\sprintf('The command must use the "%s" attribute.', AsCommand::class));
            $this->setCode($code);
        } else {
            $attribute = ((new \ReflectionClass(static::class))->getAttributes(AsCommand::class)[0] ?? null)?->newInstance();
        }

        $this->definition = new InputDefinition();
        if (null === $name) {
            if (self::class !== (new \ReflectionMethod($this, 'getDefaultName'))->class) {
                trigger_deprecation('symfony/console', '7.3', 'Overriding "Command::getDefaultName()" in "%s" is deprecated and will be removed in Symfony 8.0, use the #[AsCommand] attribute instead.', static::class);

                $name = static::getDefaultName();
            } else {
                $name = $attribute?->name;
            }
        }

        if (null !== $name) {
            $aliases = explode('|', $name);

            if ('' === $name = array_shift($aliases)) {
                $this->setHidden(true);
                $name = array_shift($aliases);
            }

            // we must not overwrite existing aliases, combine new ones with existing ones
            $aliases = array_unique([
                ...$this->aliases,
                ...$aliases,
            ]);

            $this->setAliases($aliases);
        }

        if (null !== $name) {
            $this->setName($name);
        }

        if ('' === $this->description) {
            if (self::class !== (new \ReflectionMethod($this, 'getDefaultDescription'))->class) {
                trigger_deprecation('symfony/console', '7.3', 'Overriding "Command::getDefaultDescription()" in "%s" is deprecated and will be removed in Symfony 8.0, use the #[AsCommand] attribute instead.', static::class);

                $defaultDescription = static::getDefaultDescription();
            } else {
                $defaultDescription = $attribute?->description;
            }

            $this->setDescription($defaultDescription ?? '');
        }

        if ('' === $this->help) {
            $this->setHelp($attribute?->help ?? '');
        }

        foreach ($attribute?->usages ?? [] as $usage) {
            $this->addUsage($usage);
        }

        if (!$code && \is_callable($this) && self::class === (new \ReflectionMethod($this, 'execute'))->getDeclaringClass()->name) {
            $this->code = new InvokableCommand($this, $this(...));
        }

        $this->configure();
    }

    /**
     * Ignores validation errors.
     *
     * This is mainly useful for the help command.
     */
    public function ignoreValidationErrors(): void
    {
        $this->ignoreValidationErrors = true;
    }

    public function setApplication(?Application $application): void
    {
        $this->application = $application;
        if ($application) {
            $this->setHelperSet($application->getHelperSet());
        } else {
            $this->helperSet = null;
        }

        $this->fullDefinition = null;
    }

    public function setHelperSet(HelperSet $helperSet): void
    {
        $this->helperSet = $helperSet;
    }

    /**
     * Gets the helper set.
     */
    public function getHelperSet(): ?HelperSet
    {
        return $this->helperSet;
    }

    /**
     * Gets the application instance for this command.
     */
    public function getApplication(): ?Application
    {
        return $this->application;
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * Override this to check for x or y and return false if the command cannot
     * run properly under the current conditions.
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        throw new LogicException('You must override the execute() method in the concrete command class.');
    }

    /**
     * Interacts with the user.
     *
     * This method is executed before the InputDefinition is validated.
     * This means that this is the only place where the command can
     * interactively ask for values of missing required arguments.
     *
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * Initializes the command after the input has been bound and before the input
     * is validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @see InputInterface::bind()
     * @see InputInterface::validate()
     *
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
    }

    /**
     * Runs the command.
     *
     * The code to execute is either defined directly with the
     * setCode() method or by overriding the execute() method
     * in a sub-class.
     *
     * @return int The command exit code
     *
     * @throws ExceptionInterface When input binding fails. Bypass this by calling {@link ignoreValidationErrors()}.
     *
     * @see setCode()
     * @see execute()
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        // add the application arguments and options
        $this->mergeApplicationDefinition();

        // bind the input against the command specific arguments/options
        try {
            $input->bind($this->getDefinition());
        } catch (ExceptionInterface $e) {
            if (!$this->ignoreValidationErrors) {
                throw $e;
            }
        }

        $this->initialize($input, $output);

        if (null !== $this->processTitle) {
            if (\function_exists('cli_set_process_title')) {
                if (!@cli_set_process_title($this->processTitle)) {
                    if ('Darwin' === \PHP_OS) {
                        $output->writeln('<comment>Running "cli_set_process_title" as an unprivileged user is not supported on MacOS.</comment>', OutputInterface::VERBOSITY_VERY_VERBOSE);
                    } else {
                        cli_set_process_title($this->processTitle);
                    }
                }
            } elseif (\function_exists('setproctitle')) {
                setproctitle($this->processTitle);
            } elseif (OutputInterface::VERBOSITY_VERY_VERBOSE === $output->getVerbosity()) {
                $output->writeln('<comment>Install the proctitle PECL to be able to change the process title.</comment>');
            }
        }

        if ($input->isInteractive()) {
            $this->interact($input, $output);

            if ($this->code?->isInteractive()) {
                $this->code->interact($input, $output);
            }
        }

        // The command name argument is often omitted when a command is executed directly with its run() method.
        // It would fail the validation if we didn't make sure the command argument is present,
        // since it's required by the application.
        if ($input->hasArgument('command') && null === $input->getArgument('command')) {
            $input->setArgument('command', $this->getName());
        }

        $input->validate();

        if ($this->code) {
            return ($this->code)($input, $output);
        }

        return $this->execute($input, $output);
    }

    /**
     * Supplies suggestions when resolving possible completion options for input (e.g. option or argument).
     */
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        $definition = $this->getDefinition();
        if (CompletionInput::TYPE_OPTION_VALUE === $input->getCompletionType() && $definition->hasOption($input->getCompletionName())) {
            $definition->getOption($input->getCompletionName())->complete($input, $suggestions);
        } elseif (CompletionInput::TYPE_ARGUMENT_VALUE === $input->getCompletionType() && $definition->hasArgument($input->getCompletionName())) {
            $definition->getArgument($input->getCompletionName())->complete($input, $suggestions);
        }
    }

    /**
     * Gets the code that is executed by the command.
     *
     * @return ?callable null if the code has not been set with setCode()
     */
    public function getCode(): ?callable
    {
        return $this->code?->getCode();
    }

    /**
     * Sets the code to execute when running this command.
     *
     * If this method is used, it overrides the code defined
     * in the execute() method.
     *
     * @param callable $code A callable(InputInterface $input, OutputInterface $output)
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     *
     * @see execute()
     */
    public function setCode(callable $code): static
    {
        $this->code = new InvokableCommand($this, $code);

        return $this;
    }

    /**
     * Merges the application definition with the command definition.
     *
     * This method is not part of public API and should not be used directly.
     *
     * @param bool $mergeArgs Whether to merge or not the Application definition arguments to Command definition arguments
     *
     * @internal
     */
    public function mergeApplicationDefinition(bool $mergeArgs = true): void
    {
        if (null === $this->application) {
            return;
        }

        $this->fullDefinition = new InputDefinition();
        $this->fullDefinition->setOptions($this->definition->getOptions());
        $this->fullDefinition->addOptions($this->application->getDefinition()->getOptions());

        if ($mergeArgs) {
            $this->fullDefinition->setArguments($this->application->getDefinition()->getArguments());
            $this->fullDefinition->addArguments($this->definition->getArguments());
        } else {
            $this->fullDefinition->setArguments($this->definition->getArguments());
        }
    }

    /**
     * Sets an array of argument and option instances.
     *
     * @return $this
     */
    public function setDefinition(array|InputDefinition $definition): static
    {
        if ($definition instanceof InputDefinition) {
            $this->definition = $definition;
        } else {
            $this->definition->setDefinition($definition);
        }

        $this->fullDefinition = null;

        return $this;
    }

    /**
     * Gets the InputDefinition attached to this Command.
     */
    public function getDefinition(): InputDefinition
    {
        return $this->fullDefinition ?? $this->getNativeDefinition();
    }

    /**
     * Gets the InputDefinition to be used to create representations of this Command.
     *
     * Can be overridden to provide the original command representation when it would otherwise
     * be changed by merging with the application InputDefinition.
     *
     * This method is not part of public API and should not be used directly.
     */
    public function getNativeDefinition(): InputDefinition
    {
        $definition = $this->definition ?? throw new LogicException(\sprintf('Command class "%s" is not correctly initialized. You probably forgot to call the parent constructor.', static::class));

        if ($this->code && !$definition->getArguments() && !$definition->getOptions()) {
            $this->code->configure($definition);
        }

        return $definition;
    }

    /**
     * Adds an argument.
     *
     * @param                                                                               $mode            The argument mode: InputArgument::REQUIRED or InputArgument::OPTIONAL
     * @param                                                                               $default         The default value (for InputArgument::OPTIONAL mode only)
     * @param array|\Closure(CompletionInput,CompletionSuggestions):list<string|Suggestion> $suggestedValues The values used for input completion
     *
     * @return $this
     *
     * @throws InvalidArgumentException When argument mode is not valid
     */
    public function addArgument(string $name, ?int $mode = null, string $description = '', mixed $default = null, array|\Closure $suggestedValues = []): static
    {
        $this->definition->addArgument(new InputArgument($name, $mode, $description, $default, $suggestedValues));
        $this->fullDefinition?->addArgument(new InputArgument($name, $mode, $description, $default, $suggestedValues));

        return $this;
    }

    /**
     * Adds an option.
     *
     * @param                                                                               $shortcut        The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
     * @param                                                                               $mode            The option mode: One of the InputOption::VALUE_* constants
     * @param                                                                               $default         The default value (must be null for InputOption::VALUE_NONE)
     * @param array|\Closure(CompletionInput,CompletionSuggestions):list<string|Suggestion> $suggestedValues The values used for input completion
     *
     * @return $this
     *
     * @throws InvalidArgumentException If option mode is invalid or incompatible
     */
    public function addOption(string $name, string|array|null $shortcut = null, ?int $mode = null, string $description = '', mixed $default = null, array|\Closure $suggestedValues = []): static
    {
        $this->definition->addOption(new InputOption($name, $shortcut, $mode, $description, $default, $suggestedValues));
        $this->fullDefinition?->addOption(new InputOption($name, $shortcut, $mode, $description, $default, $suggestedValues));

        return $this;
    }

    /**
     * Sets the name of the command.
     *
     * This method can set both the namespace and the name if
     * you separate them by a colon (:)
     *
     *     $command->setName('foo:bar');
     *
     * @return $this
     *
     * @throws InvalidArgumentException When the name is invalid
     */
    public function setName(string $name): static
    {
        $this->validateName($name);

        $this->name = $name;

        return $this;
    }

    /**
     * Sets the process title of the command.
     *
     * This feature should be used only when creating a long process command,
     * like a daemon.
     *
     * @return $this
     */
    public function setProcessTitle(string $title): static
    {
        $this->processTitle = $title;

        return $this;
    }

    /**
     * Returns the command name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param bool $hidden Whether or not the command should be hidden from the list of commands
     *
     * @return $this
     */
    public function setHidden(bool $hidden = true): static
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * @return bool whether the command should be publicly shown or not
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Sets the description for the command.
     *
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns the description for the command.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the help for the command.
     *
     * @return $this
     */
    public function setHelp(string $help): static
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Returns the help for the command.
     */
    public function getHelp(): string
    {
        return $this->help;
    }

    /**
     * Returns the processed help for the command replacing the %command.name% and
     * %command.full_name% patterns with the real values dynamically.
     */
    public function getProcessedHelp(): string
    {
        $name = $this->name;
        $isSingleCommand = $this->application?->isSingleCommand();

        $placeholders = [
            '%command.name%',
            '%command.full_name%',
        ];
        $replacements = [
            $name,
            $isSingleCommand ? $_SERVER['PHP_SELF'] : $_SERVER['PHP_SELF'].' '.$name,
        ];

        return str_replace($placeholders, $replacements, $this->getHelp() ?: $this->getDescription());
    }

    /**
     * Sets the aliases for the command.
     *
     * @param string[] $aliases An array of aliases for the command
     *
     * @return $this
     *
     * @throws InvalidArgumentException When an alias is invalid
     */
    public function setAliases(iterable $aliases): static
    {
        $list = [];

        foreach ($aliases as $alias) {
            $this->validateName($alias);
            $list[] = $alias;
        }

        $this->aliases = \is_array($aliases) ? $aliases : $list;

        return $this;
    }

    /**
     * Returns the aliases for the command.
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * Returns the synopsis for the command.
     *
     * @param bool $short Whether to show the short version of the synopsis (with options folded) or not
     */
    public function getSynopsis(bool $short = false): string
    {
        $key = $short ? 'short' : 'long';

        if (!isset($this->synopsis[$key])) {
            $this->synopsis[$key] = trim(\sprintf('%s %s', $this->name, $this->definition->getSynopsis($short)));
        }

        return $this->synopsis[$key];
    }

    /**
     * Add a command usage example, it'll be prefixed with the command name.
     *
     * @return $this
     */
    public function addUsage(string $usage): static
    {
        if (!str_starts_with($usage, $this->name)) {
            $usage = \sprintf('%s %s', $this->name, $usage);
        }

        $this->usages[] = $usage;

        return $this;
    }

    /**
     * Returns alternative usages of the command.
     */
    public function getUsages(): array
    {
        return $this->usages;
    }

    /**
     * Gets a helper instance by name.
     *
     * @throws LogicException           if no HelperSet is defined
     * @throws InvalidArgumentException if the helper is not defined
     */
    public function getHelper(string $name): HelperInterface
    {
        if (null === $this->helperSet) {
            throw new LogicException(\sprintf('Cannot retrieve helper "%s" because there is no HelperSet defined. Did you forget to add your command to the application or to set the application on the command using the setApplication() method? You can also set the HelperSet directly using the setHelperSet() method.', $name));
        }

        return $this->helperSet->get($name);
    }

    public function getSubscribedSignals(): array
    {
        return $this->code?->getSubscribedSignals() ?? [];
    }

    public function handleSignal(int $signal, int|false $previousExitCode = 0): int|false
    {
        return $this->code?->handleSignal($signal, $previousExitCode) ?? false;
    }

    /**
     * Validates a command name.
     *
     * It must be non-empty and parts can optionally be separated by ":".
     *
     * @throws InvalidArgumentException When the name is invalid
     */
    private function validateName(string $name): void
    {
        if (!preg_match('/^[^\:]++(\:[^\:]++)*$/', $name)) {
            throw new InvalidArgumentException(\sprintf('Command name "%s" is invalid.', $name));
        }
    }
}
