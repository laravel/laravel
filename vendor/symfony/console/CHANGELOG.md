CHANGELOG
=========

7.4
---

 * Add `Command::getCode()` to get the code set via `setCode()`
 * Allow setting aliases and the hidden flag via the command name passed to the constructor
 * Introduce `Symfony\Component\Console\Application::addCommand()` to simplify using invokable commands when the component is used standalone
 * Deprecate `Symfony\Component\Console\Application::add()` in favor of `Symfony\Component\Console\Application::addCommand()`
 * Add `BackedEnum` support with `#[Argument]` and `#[Option]` inputs in invokable commands
 * Allow Usages to be specified via `#[AsCommand]` attribute.
 * Allow passing invokable commands to `Symfony\Component\Console\Tester\CommandTester`
 * Add `#[MapInput]` attribute to support DTOs in commands
 * Add optional timeout for interaction in `QuestionHelper`
 * Add support for interactive invokable commands with `#[Interact]` and `#[Ask]` attributes
 * Add support for `Cursor` helper in invokable commands

7.3
---

 * Add `TreeHelper` and `TreeStyle` to display tree-like structures
 * Add `SymfonyStyle::createTree()`
 * Add support for invokable commands and add `#[Argument]` and `#[Option]` attributes to define input arguments and options
 * Deprecate not declaring the parameter type in callable commands defined through `setCode` method
 * Add support for help definition via `AsCommand` attribute
 * Deprecate methods `Command::getDefaultName()` and `Command::getDefaultDescription()` in favor of the `#[AsCommand]` attribute
 * Add support for Markdown format in `Table`
 * Add support for `LockableTrait` in invokable commands
 * Deprecate returning a non-integer value from a `\Closure` function set via `Command::setCode()`
 * Mark `#[AsCommand]` attribute as `@final`
 * Add support for `SignalableCommandInterface` with invokable commands

7.2
---

 * Add support for `FORCE_COLOR` environment variable
 * Add `verbosity` argument to `mustRun` process helper method
 * [BC BREAK] Add silent verbosity (`--silent`/`SHELL_VERBOSITY=-2`) to suppress all output, including errors
 * Add `OutputInterface::isSilent()`, `Output::isSilent()`, `OutputStyle::isSilent()` methods
 * Add a configurable finished indicator to the progress indicator to show that the progress is finished
 * Add ability to schedule alarm signals and a `ConsoleAlarmEvent`

7.1
---

 * Add `ArgvInput::getRawTokens()`

7.0
---

 * Add method `__toString()` to `InputInterface`
 * Remove `Command::$defaultName` and `Command::$defaultDescription`, use the `AsCommand` attribute instead
 * Require explicit argument when calling `*Command::setApplication()`, `*FormatterStyle::setForeground/setBackground()`, `Helper::setHelpSet()`, `Input*::setDefault()` and `Question::setAutocompleterCallback/setValidator()`
 * Remove `StringInput::REGEX_STRING`

6.4
---

 * Add `SignalMap` to map signal value to its name
 * Multi-line text in vertical tables is aligned properly
 * The application can also catch errors with `Application::setCatchErrors(true)`
 * Add `RunCommandMessage` and `RunCommandMessageHandler`
 * Dispatch `ConsoleTerminateEvent` after an exit on signal handling and add `ConsoleTerminateEvent::getInterruptingSignal()`

6.3
---

 * Add support for choosing exit code while handling signal, or to not exit at all
 * Add `ProgressBar::setPlaceholderFormatter` to set a placeholder attached to a instance, instead of being global.
 * Add `ReStructuredTextDescriptor`

6.2
---

 * Improve truecolor terminal detection in some cases
 * Add support for 256 color terminals (conversion from Ansi24 to Ansi8 if terminal is capable of it)
 * Deprecate calling `*Command::setApplication()`, `*FormatterStyle::setForeground/setBackground()`, `Helper::setHelpSet()`, `Input*::setDefault()`, `Question::setAutocompleterCallback/setValidator()`without any arguments
 * Change the signature of `OutputFormatterStyleInterface::setForeground/setBackground()` to `setForeground/setBackground(?string)`
 * Change the signature of `HelperInterface::setHelperSet()` to `setHelperSet(?HelperSet)`

6.1
---

 * Add support to display table vertically when calling setVertical()
 * Add method `__toString()` to `InputInterface`
 * Added `OutputWrapper` to prevent truncated URL in `SymfonyStyle::createBlock`.
 * Deprecate `Command::$defaultName` and `Command::$defaultDescription`, use the `AsCommand` attribute instead
 * Add suggested values for arguments and options in input definition, for input completion
 * Add `$resumeAt` parameter to `ProgressBar#start()`, so that one can easily 'resume' progress on longer tasks, and still get accurate `getEstimate()` and `getRemaining()` results.

6.0
---

 * `Command::setHidden()` has a default value (`true`) for `$hidden` parameter and is final
 * Remove `Helper::strlen()`, use `Helper::width()` instead
 * Remove `Helper::strlenWithoutDecoration()`, use `Helper::removeDecoration()` instead
 * `AddConsoleCommandPass` can not be configured anymore
 * Remove `HelperSet::setCommand()` and `getCommand()` without replacement

5.4
---

 * Add `TesterTrait::assertCommandIsSuccessful()` to test command
 * Deprecate `HelperSet::setCommand()` and `getCommand()` without replacement

5.3
---

 * Add `GithubActionReporter` to render annotations in a Github Action
 * Add `InputOption::VALUE_NEGATABLE` flag to handle `--foo`/`--no-foo` options
 * Add the `Command::$defaultDescription` static property and the `description` attribute
   on the `console.command` tag to allow the `list` command to instantiate commands lazily
 * Add option `--short` to the `list` command
 * Add support for bright colors
 * Add `#[AsCommand]` attribute for declaring commands on PHP 8
 * Add `Helper::width()` and `Helper::length()`
 * The `--ansi` and `--no-ansi` options now default to `null`.

5.2.0
-----

 * Added `SingleCommandApplication::setAutoExit()` to allow testing via `CommandTester`
 * added support for multiline responses to questions through `Question::setMultiline()`
   and `Question::isMultiline()`
 * Added `SignalRegistry` class to stack signals handlers
 * Added support for signals:
    * Added `Application::getSignalRegistry()` and `Application::setSignalsToDispatchEvent()` methods
    * Added `SignalableCommandInterface` interface
 * Added `TableCellStyle` class to customize table cell
 * Removed `php ` prefix invocation from help messages.

5.1.0
-----

 * `Command::setHidden()` is final since Symfony 5.1
 * Add `SingleCommandApplication`
 * Add `Cursor` class

5.0.0
-----

 * removed support for finding hidden commands using an abbreviation, use the full name instead
 * removed `TableStyle::setCrossingChar()` method in favor of `TableStyle::setDefaultCrossingChar()`
 * removed `TableStyle::setHorizontalBorderChar()` method in favor of `TableStyle::setDefaultCrossingChars()`
 * removed `TableStyle::getHorizontalBorderChar()` method in favor of `TableStyle::getBorderChars()`
 * removed `TableStyle::setVerticalBorderChar()` method in favor of `TableStyle::setVerticalBorderChars()`
 * removed `TableStyle::getVerticalBorderChar()` method in favor of `TableStyle::getBorderChars()`
 * removed support for returning `null` from `Command::execute()`, return `0` instead
 * `ProcessHelper::run()` accepts only `array|Symfony\Component\Process\Process` for its `command` argument
 * `Application::setDispatcher` accepts only `Symfony\Contracts\EventDispatcher\EventDispatcherInterface`
   for its `dispatcher` argument
 * renamed `Application::renderException()` and `Application::doRenderException()`
   to `renderThrowable()` and `doRenderThrowable()` respectively.

4.4.0
-----

 * deprecated finding hidden commands using an abbreviation, use the full name instead
 * added `Question::setTrimmable` default to true to allow the answer to be trimmed
 * added method `minSecondsBetweenRedraws()` and `maxSecondsBetweenRedraws()` on `ProgressBar`
 * `Application` implements `ResetInterface`
 * marked all dispatched event classes as `@final`
 * added support for displaying table horizontally
 * deprecated returning `null` from `Command::execute()`, return `0` instead
 * Deprecated the `Application::renderException()` and `Application::doRenderException()` methods,
   use `renderThrowable()` and `doRenderThrowable()` instead.
 * added support for the `NO_COLOR` env var (https://no-color.org/)

4.3.0
-----

 * added support for hyperlinks
 * added `ProgressBar::iterate()` method that simplify updating the progress bar when iterating
 * added `Question::setAutocompleterCallback()` to provide a callback function
   that dynamically generates suggestions as the user types

4.2.0
-----

 * allowed passing commands as `[$process, 'ENV_VAR' => 'value']` to
   `ProcessHelper::run()` to pass environment variables
 * deprecated passing a command as a string to `ProcessHelper::run()`,
   pass it the command as an array of its arguments instead
 * made the `ProcessHelper` class final
 * added `WrappableOutputFormatterInterface::formatAndWrap()` (implemented in `OutputFormatter`)
 * added `capture_stderr_separately` option to `CommandTester::execute()`

4.1.0
-----

 * added option to run suggested command if command is not found and only 1 alternative is available
 * added option to modify console output and print multiple modifiable sections
 * added support for iterable messages in output `write` and `writeln` methods

4.0.0
-----

 * `OutputFormatter` throws an exception when unknown options are used
 * removed `QuestionHelper::setInputStream()/getInputStream()`
 * removed `Application::getTerminalWidth()/getTerminalHeight()` and
   `Application::setTerminalDimensions()/getTerminalDimensions()`
 * removed `ConsoleExceptionEvent`
 * removed `ConsoleEvents::EXCEPTION`

3.4.0
-----

 * added `SHELL_VERBOSITY` env var to control verbosity
 * added `CommandLoaderInterface`, `FactoryCommandLoader` and PSR-11
   `ContainerCommandLoader` for commands lazy-loading
 * added a case-insensitive command name matching fallback
 * added static `Command::$defaultName/getDefaultName()`, allowing for
   commands to be registered at compile time in the application command loader.
   Setting the `$defaultName` property avoids the need for filling the `command`
   attribute on the `console.command` tag when using `AddConsoleCommandPass`.

3.3.0
-----

 * added `ExceptionListener`
 * added `AddConsoleCommandPass` (originally in FrameworkBundle)
 * [BC BREAK] `Input::getOption()` no longer returns the default value for options
   with value optional explicitly passed empty
 * added console.error event to catch exceptions thrown by other listeners
 * deprecated console.exception event in favor of console.error
 * added ability to handle `CommandNotFoundException` through the
   `console.error` event
 * deprecated default validation in `SymfonyQuestionHelper::ask`

3.2.0
------

 * added `setInputs()` method to CommandTester for ease testing of commands expecting inputs
 * added `setStream()` and `getStream()` methods to Input (implement StreamableInputInterface)
 * added StreamableInputInterface
 * added LockableTrait

3.1.0
-----

 * added truncate method to FormatterHelper
 * added setColumnWidth(s) method to Table

2.8.3
-----

 * remove readline support from the question helper as it caused issues

2.8.0
-----

 * use readline for user input in the question helper when available to allow
   the use of arrow keys

2.6.0
-----

 * added a Process helper
 * added a DebugFormatter helper

2.5.0
-----

 * deprecated the dialog helper (use the question helper instead)
 * deprecated TableHelper in favor of Table
 * deprecated ProgressHelper in favor of ProgressBar
 * added ConsoleLogger
 * added a question helper
 * added a way to set the process name of a command
 * added a way to set a default command instead of `ListCommand`

2.4.0
-----

 * added a way to force terminal dimensions
 * added a convenient method to detect verbosity level
 * [BC BREAK] made descriptors use output instead of returning a string

2.3.0
-----

 * added multiselect support to the select dialog helper
 * added Table Helper for tabular data rendering
 * added support for events in `Application`
 * added a way to normalize EOLs in `ApplicationTester::getDisplay()` and `CommandTester::getDisplay()`
 * added a way to set the progress bar progress via the `setCurrent` method
 * added support for multiple InputOption shortcuts, written as `'-a|-b|-c'`
 * added two additional verbosity levels, VERBOSITY_VERY_VERBOSE and VERBOSITY_DEBUG

2.2.0
-----

 * added support for colorization on Windows via ConEmu
 * add a method to Dialog Helper to ask for a question and hide the response
 * added support for interactive selections in console (DialogHelper::select())
 * added support for autocompletion as you type in Dialog Helper

2.1.0
-----

 * added ConsoleOutputInterface
 * added the possibility to disable a command (Command::isEnabled())
 * added suggestions when a command does not exist
 * added a --raw option to the list command
 * added support for STDERR in the console output class (errors are now sent
   to STDERR)
 * made the defaults (helper set, commands, input definition) in Application
   more easily customizable
 * added support for the shell even if readline is not available
 * added support for process isolation in Symfony shell via
   `--process-isolation` switch
 * added support for `--`, which disables options parsing after that point
   (tokens will be parsed as arguments)
