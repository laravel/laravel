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

use Psy\Clipboard\ClipboardMethod;
use Psy\Clipboard\CommandClipboardMethod;
use Psy\Clipboard\NullClipboardMethod;
use Psy\Clipboard\Osc52ClipboardMethod;
use Psy\Exception\DeprecatedException;
use Psy\Exception\InvalidManualException;
use Psy\Exception\RuntimeException;
use Psy\ExecutionLoop\ExecutionLoggingListener;
use Psy\ExecutionLoop\InputLoggingListener;
use Psy\ExecutionLoop\ProcessForker;
use Psy\Formatter\SignatureFormatter;
use Psy\Logger\CallbackLogger;
use Psy\Manual\ManualInterface;
use Psy\Manual\V2Manual;
use Psy\Manual\V3Manual;
use Psy\Output\OutputPager;
use Psy\Output\ShellOutput;
use Psy\Output\Theme;
use Psy\Readline\Interactive\Input\History as InteractiveHistory;
use Psy\TabCompletion\AutoCompleter;
use Psy\Util\Tty;
use Psy\VarDumper\Presenter;
use Psy\VersionUpdater\Checker;
use Psy\VersionUpdater\GitHubChecker;
use Psy\VersionUpdater\IntervalChecker;
use Psy\VersionUpdater\NoopChecker;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The Psy Shell configuration.
 */
class Configuration
{
    const COLOR_MODE_AUTO = 'auto';
    const COLOR_MODE_FORCED = 'forced';
    const COLOR_MODE_DISABLED = 'disabled';

    const INTERACTIVE_MODE_AUTO = 'auto';
    const INTERACTIVE_MODE_FORCED = 'forced';
    const INTERACTIVE_MODE_DISABLED = 'disabled';

    const PROJECT_TRUST_PROMPT = 'prompt';
    const PROJECT_TRUST_ALWAYS = 'always';
    const PROJECT_TRUST_NEVER = 'never';

    const VERBOSITY_QUIET = 'quiet';
    const VERBOSITY_NORMAL = 'normal';
    const VERBOSITY_VERBOSE = 'verbose';
    const VERBOSITY_VERY_VERBOSE = 'very_verbose';
    const VERBOSITY_DEBUG = 'debug';

    const SEMICOLONS_SUPPRESS_RETURN_DOUBLE = 'double';

    private const KNOWN_CLIPBOARD_COMMANDS = [
        'wl-copy',
        'xsel --clipboard --input',
        'xclip -selection clipboard',
        'pbcopy',
        'clip.exe',
    ];

    private const AVAILABLE_OPTIONS = [
        'codeCleaner',
        'colorMode',
        'configDir',
        'dataDir',
        'defaultIncludes',
        'eraseDuplicates',
        'exceptionDetails',
        'errorLoggingLevel',
        'useExperimentalReadline',
        'useSyntaxHighlighting',
        'useSuggestions',
        'forceArrayIndexes',
        'formatterStyles',
        'historyFile',
        'historySize',
        'implicitUse',
        'interactiveMode',
        'logging',
        'manualDbFile',
        'useDeprecatedMultilineStrings',
        'trustProject',
        'pager',
        'prompt',
        'rawOutput',
        'requireSemicolons',
        'runtimeDir',
        'semicolonsSuppressReturn',
        'startupMessage',
        'strictTypes',
        'theme',
        'updateCheck',
        'updateManualCheck',
        'clipboardCommand',
        'useBracketedPaste',
        'useOsc52Clipboard',
        'usePcntl',
        'useReadline',
        'useTabCompletion',
        'useUnicode',
        'verbosity',
        'warmAutoload',
        'warnOnMultipleConfigs',
        'yolo',
    ];

    private ?array $defaultIncludes = null;
    private ?string $configDir = null;
    private ?string $dataDir = null;
    private ?string $runtimeDir = null;
    private ?string $configFile = null;
    /** @var string|false|null */
    private $historyFile;
    private int $historySize = 0;
    private ?bool $eraseDuplicates = null;
    private bool $useExperimentalReadline = false;
    private bool $useSyntaxHighlighting = true;
    private bool $useSuggestions = false;
    private ?string $manualDbFile = null;
    private bool $hasReadline;
    private ?bool $useReadline = null;
    private bool $useBracketedPaste = false;
    private bool $hasPcntl;
    private ?bool $usePcntl = null;
    private array $commands = [];
    private array $matchers = [];
    /** @var Completion\Source\SourceInterface[] */
    private array $completionSources = [];
    private ?bool $pipedInput = null;
    private ?bool $pipedOutput = null;
    private bool $rawOutput = false;
    private bool $requireSemicolons = false;
    /** @var bool|string */
    private $semicolonsSuppressReturn = false;
    private bool $strictTypes = false;
    private ?string $clipboardCommand = null;
    private ?bool $useUnicode = null;
    private ?bool $useTabCompletion = null;
    private bool $useOsc52Clipboard = false;
    private ?array $autoloadWarmers = null;
    private $implicitUse = false;
    private ?ShellLogger $logger = null;
    private ?\Closure $exceptionDetails = null;
    private int $errorLoggingLevel = \E_ALL;
    private bool $warnOnMultipleConfigs = false;
    private string $colorMode = self::COLOR_MODE_AUTO;
    private string $interactiveMode = self::INTERACTIVE_MODE_AUTO;
    private ProjectTrust $projectTrust;
    private ?string $updateCheck = null;
    private ?string $updateManualCheck = null;
    private ?string $startupMessage = null;
    private bool $forceArrayIndexes = false;
    private bool $useDeprecatedMultilineStrings = false;
    /** @deprecated */
    private array $formatterStyles = [];
    private string $verbosity = self::VERBOSITY_NORMAL;
    private bool $yolo = false;
    private ?Theme $theme = null;
    private bool $localConfigLoaded = false;

    // services
    private ?Readline\Readline $readline = null;
    private ?ShellOutput $output = null;
    private ?Shell $shell = null;
    private ?CodeCleaner $cleaner = null;
    /** @var string|OutputPager|false|null */
    private $pager = null;
    private ?\PDO $manualDb = null;
    private ?ManualInterface $manual = null;
    private ?Presenter $presenter = null;
    private array $casters = [];
    private ?AutoCompleter $autoCompleter = null;
    private ?Checker $checker = null;
    private ?ClipboardMethod $clipboard = null;
    /** @deprecated */
    private ?string $prompt = null;
    private ConfigPaths $configPaths;

    /**
     * Construct a Configuration instance.
     *
     * Optionally, supply an array of configuration values to load.
     *
     * @param array $config Optional array of configuration values
     */
    public function __construct(array $config = [])
    {
        $this->configPaths = new ConfigPaths();
        $this->projectTrust = new ProjectTrust($this->configPaths);

        // explicit configFile option
        if (isset($config['configFile'])) {
            $this->configFile = $config['configFile'];
        } elseif (isset($_SERVER['PSYSH_CONFIG']) && $_SERVER['PSYSH_CONFIG']) {
            $this->configFile = $_SERVER['PSYSH_CONFIG'];
        } elseif (\PHP_SAPI === 'cli-server' && ($configFile = \getenv('PSYSH_CONFIG'))) {
            $this->configFile = $configFile;
        }

        $trustOverride = null;
        if (isset($config['trustProject'])) {
            $this->setTrustProject($config['trustProject']);
            $trustOverride = $this->projectTrust->getMode();
        } elseif (isset($_SERVER['PSYSH_TRUST_PROJECT']) && $_SERVER['PSYSH_TRUST_PROJECT'] !== '') {
            $this->projectTrust->setModeFromEnv($_SERVER['PSYSH_TRUST_PROJECT']);
            $trustOverride = $this->projectTrust->getMode();
        } elseif (\PHP_SAPI === 'cli-server') {
            $trust = \getenv('PSYSH_TRUST_PROJECT');
            if ($trust !== false && $trust !== '') {
                $this->projectTrust->setModeFromEnv($trust);
                $trustOverride = $this->projectTrust->getMode();
            }
        }

        // legacy baseDir option
        if (isset($config['baseDir'])) {
            $msg = "The 'baseDir' configuration option is deprecated; ".
                "please specify 'configDir' and 'dataDir' options instead";
            throw new DeprecatedException($msg);
        }

        unset(
            $config['configFile'],
            $config['baseDir'],
            $config['trustProject']
        );

        // go go gadget, config!
        $this->loadConfig($config);

        // Ensure that explicit project trust settings are applied
        if ($trustOverride !== null && $this->projectTrust->getMode() !== $trustOverride) {
            $this->projectTrust->setMode($trustOverride);
        }

        $this->init();
    }

    /**
     * Construct a Configuration object from Symfony Console input.
     *
     * This is great for adding psysh-compatible command line options to framework- or app-specific
     * wrappers.
     *
     * $input should already be bound to an appropriate InputDefinition (see self::getInputOptions
     * if you want to build your own) before calling this method. It's not required, but things work
     * a lot better if we do.
     *
     * @see self::getInputOptions
     *
     * @throws \InvalidArgumentException
     *
     * @param InputInterface $input
     */
    public static function fromInput(InputInterface $input): self
    {
        $configOptions = [
            'configFile'   => self::getConfigFileFromInput($input),
            'trustProject' => self::getProjectTrustFromInput($input),
        ];

        $config = new self($configOptions);

        // Handle --color and --no-color (and --ansi and --no-ansi aliases)
        if (self::getOptionFromInput($input, ['color', 'ansi'])) {
            $config->setColorMode(self::COLOR_MODE_FORCED);
        } elseif (self::getOptionFromInput($input, ['no-color', 'no-ansi'])) {
            $config->setColorMode(self::COLOR_MODE_DISABLED);
        }

        // Handle verbosity options
        if ($verbosity = self::getVerbosityFromInput($input)) {
            $config->setVerbosity($verbosity);
        }

        // Handle interactive mode
        if (self::getOptionFromInput($input, ['interactive', 'interaction'], ['-a', '-i'])) {
            $config->setInteractiveMode(self::INTERACTIVE_MODE_FORCED);
        } elseif (self::getOptionFromInput($input, ['no-interactive', 'no-interaction'], ['-n'])) {
            $config->setInteractiveMode(self::INTERACTIVE_MODE_DISABLED);
        }

        // Handle --compact
        if (self::getOptionFromInput($input, ['compact'])) {
            $config->setTheme('compact');
        }

        // Handle --pager and --no-pager
        if (self::hasPagerOption($input)) {
            $pager = self::getPagerFromInput($input);
            if ($pager === null) {
                $config->setDefaultPager();
            } else {
                $config->setPager($pager);
            }
        } elseif (self::getOptionFromInput($input, ['no-pager'])) {
            $config->setPager(false);
        }

        // Handle --raw-output
        // @todo support raw output with interactive input?
        if (!$config->getInputInteractive()) {
            if (self::getOptionFromInput($input, ['raw-output'], ['-r'])) {
                $config->setRawOutput(true);
            }
        }

        // Handle --warm-autoload
        if (self::getOptionFromInput($input, ['warm-autoload'])) {
            $config->setWarmAutoload(true);
        }

        // Handle --yolo
        if (self::getOptionFromInput($input, ['yolo'])) {
            $config->setYolo(true);
        }

        // Handle --experimental-readline
        if (self::getOptionFromInput($input, ['experimental-readline'])) {
            $config->setUseExperimentalReadline(true);
        }

        return $config;
    }

    /**
     * Get the desired config file from the given input.
     *
     * @return string|null config file path, or null if none is specified
     */
    private static function getConfigFileFromInput(InputInterface $input)
    {
        // Best case, input is properly bound and validated.
        if ($input->hasOption('config')) {
            return $input->getOption('config');
        }

        return $input->getParameterOption('--config', null, true) ?: $input->getParameterOption('-c', null, true);
    }

    /**
     * Get the desired project trust from the given input.
     *
     * @return bool|null project trust, or null if none is specified
     */
    private static function getProjectTrustFromInput(InputInterface $input): ?bool
    {
        if (self::getOptionFromInput($input, ['no-trust-project'])) {
            return false;
        } elseif (self::getOptionFromInput($input, ['trust-project'])) {
            return true;
        }

        return null;
    }

    /**
     * Get the desired pager from the given input.
     *
     * @return string|null pager command, or null to use default pager resolution
     */
    private static function getPagerFromInput(InputInterface $input): ?string
    {
        // Best case, input is properly bound and validated.
        if ($input->hasOption('pager')) {
            $pager = $input->getOption('pager');
            if (\is_string($pager)) {
                return $pager === '' ? null : $pager;
            }

            if ($pager === true) {
                return null;
            }
        }

        if ($input->hasParameterOption('--pager', true)) {
            $pager = $input->getParameterOption('--pager', null, true);

            if (\is_string($pager) && isset($pager[0]) && $pager[0] === '-') {
                return null;
            }

            return ($pager === null || $pager === '') ? null : $pager;
        }

        return null;
    }

    private static function hasPagerOption(InputInterface $input): bool
    {
        if ($input->hasOption('pager')) {
            $pager = $input->getOption('pager');
            if (\is_string($pager) || $pager === true) {
                return true;
            }
        }

        if ($input->hasParameterOption('--pager', true)) {
            return true;
        }

        return false;
    }

    /**
     * Get a boolean option from the given input.
     *
     * This helper allows fallback for unbound and unvalidated input. It's not perfect--for example,
     * it can't deal with several short options squished together--but it's better than falling over
     * any time someone gives us unbound input.
     *
     * @return bool true if the option (or an alias) is present
     */
    private static function getOptionFromInput(InputInterface $input, array $names, array $otherParams = []): bool
    {
        // Best case, input is properly bound and validated.
        foreach ($names as $name) {
            if ($input->hasOption($name) && $input->getOption($name)) {
                return true;
            }
        }

        foreach ($names as $name) {
            $otherParams[] = '--'.$name;
        }

        foreach ($otherParams as $name) {
            if ($input->hasParameterOption($name, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the desired verbosity from the given input.
     *
     * This is a bit more complext than the other options parsers. It handles `--quiet` and
     * `--verbose`, along with their short aliases, and fancy things like `-vvv`.
     *
     * @return string|null configuration constant, or null if no verbosity option is specified
     */
    private static function getVerbosityFromInput(InputInterface $input)
    {
        // --quiet wins!
        if (self::getOptionFromInput($input, ['quiet'], ['-q'])) {
            return self::VERBOSITY_QUIET;
        }

        // Best case, input is properly bound and validated.
        //
        // Note that if the `--verbose` option is incorrectly defined as `VALUE_NONE` rather than
        // `VALUE_OPTIONAL` (as it is in Symfony Console by default) it doesn't actually work with
        // multiple verbosity levels as it claims.
        //
        // We can detect this by checking whether the the value === true, and fall back to unbound
        // parsing for this option.
        if ($input->hasOption('verbose') && $input->getOption('verbose') !== true) {
            switch ($input->getOption('verbose')) {
                case '-1':
                    return self::VERBOSITY_QUIET;
                case '0': // explicitly normal, overrides config file default
                    return self::VERBOSITY_NORMAL;
                case '1':
                case null: // `--verbose` and `-v`
                    return self::VERBOSITY_VERBOSE;
                case '2':
                case 'v': // `-vv`
                    return self::VERBOSITY_VERY_VERBOSE;
                case '3':
                case 'vv': // `-vvv`
                case 'vvv':
                case 'vvvv':
                case 'vvvvv':
                case 'vvvvvv':
                case 'vvvvvvv':
                    return self::VERBOSITY_DEBUG;
                default: // implicitly normal, config file default wins
                    return null;
            }
        }

        // quiet and normal have to come before verbose, because it eats everything else.
        if ($input->hasParameterOption('--verbose=-1', true) || $input->getParameterOption('--verbose', false, true) === '-1') {
            return self::VERBOSITY_QUIET;
        }

        if ($input->hasParameterOption('--verbose=0', true) || $input->getParameterOption('--verbose', false, true) === '0') {
            return self::VERBOSITY_NORMAL;
        }

        // `-vvv`, `-vv` and `-v` have to come in descending length order, because `hasParameterOption` matches prefixes.
        if ($input->hasParameterOption('-vvv', true) || $input->hasParameterOption('--verbose=3', true) || $input->getParameterOption('--verbose', false, true) === '3') {
            return self::VERBOSITY_DEBUG;
        }

        if ($input->hasParameterOption('-vv', true) || $input->hasParameterOption('--verbose=2', true) || $input->getParameterOption('--verbose', false, true) === '2') {
            return self::VERBOSITY_VERY_VERBOSE;
        }

        if ($input->hasParameterOption('-v', true) || $input->hasParameterOption('--verbose=1', true) || $input->hasParameterOption('--verbose', true)) {
            return self::VERBOSITY_VERBOSE;
        }

        return null;
    }

    /**
     * Get a list of input options expected when initializing Configuration via input.
     *
     * @see self::fromInput
     *
     * @return InputOption[]
     */
    public static function getInputOptions(): array
    {
        return [
            new InputOption('config', 'c', InputOption::VALUE_REQUIRED, 'Use an alternate PsySH config file location.'),
            new InputOption('cwd', null, InputOption::VALUE_REQUIRED, 'Use an alternate working directory.'),
            new InputOption('trust-project', null, InputOption::VALUE_NONE, 'Trust the current project for this run.'),
            new InputOption('no-trust-project', null, InputOption::VALUE_NONE, 'Run in Restricted Mode for this project.'),

            new InputOption('color', null, InputOption::VALUE_NONE, 'Force colors in output.'),
            new InputOption('no-color', null, InputOption::VALUE_NONE, 'Disable colors in output.'),
            // --ansi and --no-ansi aliases to match Symfony, Composer, etc.
            new InputOption('ansi', null, InputOption::VALUE_NONE, 'Force colors in output.'),
            new InputOption('no-ansi', null, InputOption::VALUE_NONE, 'Disable colors in output.'),

            new InputOption('quiet', 'q', InputOption::VALUE_NONE, 'Shhhhhh.'),
            new InputOption('verbose', 'v|vv|vvv', InputOption::VALUE_OPTIONAL, 'Increase the verbosity of messages.', '0'),
            new InputOption('compact', null, InputOption::VALUE_NONE, 'Run PsySH with compact output.'),
            new InputOption('interactive', 'i|a', InputOption::VALUE_NONE, 'Force PsySH to run in interactive mode.'),
            new InputOption('no-interactive', 'n', InputOption::VALUE_NONE, 'Run PsySH without interactive input. Requires input from stdin.'),
            // --interaction and --no-interaction aliases for compatibility with Symfony, Composer, etc
            new InputOption('interaction', null, InputOption::VALUE_NONE, 'Force PsySH to run in interactive mode.'),
            new InputOption('no-interaction', null, InputOption::VALUE_NONE, 'Run PsySH without interactive input. Requires input from stdin.'),
            new InputOption('pager', null, InputOption::VALUE_OPTIONAL, 'Use an alternate output pager command. Without a value, use the default pager selection.', false),
            new InputOption('no-pager', null, InputOption::VALUE_NONE, 'Disable paging output for this run.'),
            new InputOption('raw-output', 'r', InputOption::VALUE_NONE, 'Print var_export-style return values (for non-interactive input)'),

            new InputOption('self-update', 'u', InputOption::VALUE_NONE, 'Update to the latest version'),

            new InputOption('experimental-readline', null, InputOption::VALUE_NONE, 'Use experimental interactive readline implementation.'),
            new InputOption('yolo', null, InputOption::VALUE_NONE, 'Run PsySH with minimal input validation. You probably don\'t want this.'),
            new InputOption('warm-autoload', null, InputOption::VALUE_NONE, 'Enable autoload warming for better tab completion.'),
            new InputOption('info', null, InputOption::VALUE_NONE, 'Display PsySH environment and configuration info.'),
        ];
    }

    /**
     * Initialize the configuration.
     *
     * This checks for the presence of Readline and Pcntl extensions.
     *
     * If a config file is available, it will be loaded and merged with the current config.
     *
     * If no custom config file was specified and a local project config file
     * is available, it may be loaded and merged with the current config
     * depending on project trust settings.
     */
    public function init()
    {
        // feature detection
        $this->hasReadline = \function_exists('readline');
        $this->hasPcntl = ProcessForker::isSupported();

        if ($configFile = $this->getConfigFile()) {
            $this->loadConfigFile($configFile);
        }

        $this->loadLocalConfigIfTrusted();

        $this->configPaths->overrideDirs([
            'configDir'  => $this->configDir,
            'dataDir'    => $this->dataDir,
            'runtimeDir' => $this->runtimeDir,
        ]);
    }

    /**
     * Get the current PsySH config file.
     *
     * If a `configFile` option was passed to the Configuration constructor,
     * this file will be returned. If not, all possible config directories will
     * be searched, and the first `config.php` or `rc.php` file which exists
     * will be returned.
     *
     * If you're trying to decide where to put your config file, pick
     *
     *     ~/.config/psysh/config.php
     *
     * @return string|null
     */
    public function getConfigFile()
    {
        if (isset($this->configFile)) {
            return $this->configFile;
        }

        $files = $this->configPaths->configFiles(['config.php', 'rc.php']);

        if (!empty($files)) {
            if ($this->warnOnMultipleConfigs && \count($files) > 1) {
                $prettyFiles = \array_map([ConfigPaths::class, 'prettyPath'], $files);
                $msg = \sprintf('Multiple configuration files found: %s. Using %s', \implode(', ', $prettyFiles), $prettyFiles[0]);
                \trigger_error($msg, \E_USER_NOTICE);
            }

            return $files[0];
        }

        return null;
    }

    /**
     * Get the local PsySH config file.
     *
     * Searches for a project specific config file `.psysh.php` in the current
     * working directory.
     *
     * @return string|null
     */
    public function getLocalConfigFile()
    {
        $localConfig = \getcwd().'/.psysh.php';

        if (@\is_file($localConfig)) {
            return $localConfig;
        }

        return null;
    }

    /**
     * Configure the project trust mode.
     *
     * Accepts boolean values or one of: 'prompt', 'always', 'never'.
     *
     * @param bool|string|null $mode
     */
    public function setTrustProject($mode): void
    {
        if ($mode === null) {
            $this->projectTrust->setMode(self::PROJECT_TRUST_PROMPT);

            return;
        }

        if (\is_bool($mode)) {
            $this->projectTrust->setMode($mode ? self::PROJECT_TRUST_ALWAYS : self::PROJECT_TRUST_NEVER);

            return;
        }

        if (!\is_string($mode)) {
            throw new \InvalidArgumentException('Invalid trustProject value. Expected: prompt, always, or never');
        }

        switch (\strtolower(\trim($mode))) {
            case 'prompt':
                $this->projectTrust->setMode(self::PROJECT_TRUST_PROMPT);
                break;
            case 'always':
                $this->projectTrust->setMode(self::PROJECT_TRUST_ALWAYS);
                break;
            case 'never':
                $this->projectTrust->setMode(self::PROJECT_TRUST_NEVER);
                break;
            default:
                throw new \InvalidArgumentException('Invalid trustProject value. Expected: prompt, always, or never');
        }
    }

    /**
     * Get the current project trust mode.
     */
    public function getProjectTrustMode(): string
    {
        return $this->projectTrust->getMode();
    }

    /**
     * @deprecated explicit autoload warming always respects project trust restrictions
     */
    public function setForceWarmAutoload(bool $force = true): void
    {
    }

    /**
     * Load local config with an interactive trust prompt when appropriate.
     *
     * When stdin is not interactive, loads local config without prompting and
     * writes a warning to stderr.
     */
    public function loadLocalConfigWithPrompt(InputInterface $input, OutputInterface $output): void
    {
        if ($this->localConfigLoaded) {
            return;
        }

        $localConfigRoot = $this->projectTrust->getLocalConfigRoot();
        $composerRoot = $this->projectTrust->getProjectRoot();
        $checkLocalPsyshBinary = $this->projectTrust->shouldPromptForLocalPsyshBinary();

        // Collect features by root that need trust
        $featuresByRoot = [];

        // Check for local config (cwd only)
        if ($localConfigRoot !== null && $this->hasLocalConfig($localConfigRoot)) {
            $trustStatus = $this->projectTrust->getProjectTrustStatus($localConfigRoot);
            if ($trustStatus === null) {
                $featuresByRoot[$localConfigRoot][] = 'Local config (.psysh.php)';
            }
        }

        // Check for local PsySH binary (at Composer root, where vendor/bin lives)
        if ($checkLocalPsyshBinary && $composerRoot !== null && $this->projectTrust->getLocalPsyshProjectRoot($composerRoot) !== null) {
            $trustStatus = $this->projectTrust->getProjectTrustStatus($composerRoot);
            if ($trustStatus === null) {
                $featuresByRoot[$composerRoot][] = 'local PsySH binary';
            }
        }

        // Check for autoload warming (Composer root, may differ from cwd)
        if ($composerRoot !== null && $this->shouldReportAutoloadWarming($composerRoot)) {
            $trustStatus = $this->projectTrust->getProjectTrustStatus($composerRoot);
            if ($trustStatus === null) {
                $featuresByRoot[$composerRoot][] = 'Project autoload (vendor/autoload.php)';
            }
        }

        // If nothing needs trust, we're done
        if (empty($featuresByRoot)) {
            // Load local config if it's already trusted
            if ($localConfigRoot !== null && $this->projectTrust->getProjectTrustStatus($localConfigRoot) === true) {
                $this->loadLocalConfigIfPresent($localConfigRoot);
            }

            return;
        }

        // Handle force untrust
        if ($this->projectTrust->getForceUntrust() || $this->projectTrust->getMode() === self::PROJECT_TRUST_NEVER) {
            return;
        }

        // Non-interactive: warn and skip untrusted features (do not auto-trust)
        if (!$this->getInputInteractive() || !$input->isInteractive()) {
            $errorOutput = $output;
            if ($errorOutput instanceof ConsoleOutput) {
                $errorOutput = $errorOutput->getErrorOutput();
            }

            foreach ($featuresByRoot as $root => $features) {
                $prettyDir = ConfigPaths::prettyPath($root);
                $errorOutput->writeln(
                    "<comment>Restricted Mode: skipping untrusted project features from {$prettyDir} (non-interactive mode). Use --trust-project to allow.</comment>"
                );
            }

            if ($localConfigRoot !== null && $this->projectTrust->getProjectTrustStatus($localConfigRoot) === true) {
                $this->loadLocalConfigIfPresent($localConfigRoot);
            }

            return;
        }

        // Interactive: prompt per-root
        foreach ($featuresByRoot as $root => $features) {
            if ($this->projectTrust->promptForTrust($input, $output, $root, $features)) {
                // Check for local PsySH binary that needs re-run message
                if ($checkLocalPsyshBinary && ($localPsyshRoot = $this->projectTrust->getLocalPsyshProjectRoot($root))) {
                    $prettyLocal = ConfigPaths::prettyPath($localPsyshRoot);
                    $output->writeln('');
                    $output->writeln("<comment>Local PsySH version detected at {$prettyLocal}.</comment>");
                    $output->writeln('Re-run PsySH to use the trusted local version, or pass --trust-project to use it immediately.');
                    $output->writeln('');
                }
            }
        }

        // Load local config if now trusted
        if ($localConfigRoot !== null && $this->projectTrust->getProjectTrustStatus($localConfigRoot) === true) {
            $this->loadLocalConfigIfPresent($localConfigRoot);
        }
    }

    /**
     * Check if a local config file exists at the given root.
     */
    private function hasLocalConfig(string $root): bool
    {
        if (isset($this->configFile)) {
            return false;
        }

        return @\is_file($root.'/.psysh.php');
    }

    private function loadLocalConfigIfTrusted(): void
    {
        if ($this->localConfigLoaded || isset($this->configFile)) {
            return;
        }

        $localConfigRoot = $this->projectTrust->getLocalConfigRoot();
        if ($localConfigRoot === null) {
            return;
        }

        if ($this->projectTrust->getProjectTrustStatus($localConfigRoot) === true) {
            $this->loadLocalConfigIfPresent($localConfigRoot);
        }
    }

    private function loadLocalConfigIfPresent(string $projectRoot): void
    {
        if ($this->localConfigLoaded || isset($this->configFile)) {
            return;
        }

        $localConfig = $projectRoot.'/.psysh.php';
        if (@\is_file($localConfig)) {
            $this->loadConfigFile($localConfig);
            $this->localConfigLoaded = true;
            $this->configPaths->overrideDirs([
                'configDir'  => $this->configDir,
                'dataDir'    => $this->dataDir,
                'runtimeDir' => $this->runtimeDir,
            ]);
        }
    }

    private function shouldReportAutoloadWarming(string $projectRoot): bool
    {
        if (!$this->hasComposerAutoloadWarmerConfigured()) {
            return false;
        }

        return $this->projectTrust->hasComposerAutoloadFiles($projectRoot);
    }

    private function hasComposerAutoloadWarmerConfigured(): bool
    {
        if ($this->autoloadWarmers === null) {
            $this->autoloadWarmers = $this->parseWarmAutoloadConfig(false);
        }

        foreach ($this->autoloadWarmers as $warmer) {
            if ($warmer instanceof TabCompletion\AutoloadWarmer\ComposerAutoloadWarmer) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load configuration values from an array of options.
     *
     * @param array $options
     */
    public function loadConfig(array $options)
    {
        foreach (self::AVAILABLE_OPTIONS as $option) {
            if (isset($options[$option])) {
                $method = 'set'.\ucfirst($option);
                $this->$method($options[$option]);
            }
        }

        // legacy `tabCompletion` option
        if (isset($options['tabCompletion'])) {
            $msg = '`tabCompletion` is deprecated; use `useTabCompletion` instead.';
            @\trigger_error($msg, \E_USER_DEPRECATED);

            $this->setUseTabCompletion($options['tabCompletion']);
        }

        foreach (['commands', 'matchers', 'completionSources', 'casters'] as $option) {
            if (isset($options[$option])) {
                $method = 'add'.\ucfirst($option);
                $this->$method($options[$option]);
            }
        }

        // legacy `tabCompletionMatchers` option
        if (isset($options['tabCompletionMatchers'])) {
            $msg = '`tabCompletionMatchers` is deprecated; use `matchers` instead.';
            @\trigger_error($msg, \E_USER_DEPRECATED);

            $this->addMatchers($options['tabCompletionMatchers']);
        }
    }

    /**
     * Load a configuration file (default: `$HOME/.config/psysh/config.php`).
     *
     * This configuration instance will be available to the config file as $config.
     * The config file may directly manipulate the configuration, or may return
     * an array of options which will be merged with the current configuration.
     *
     * @throws \InvalidArgumentException if the config file does not exist or returns a non-array result
     *
     * @param string $file
     */
    public function loadConfigFile(string $file)
    {
        if (!\is_file($file)) {
            throw new \InvalidArgumentException(\sprintf('Invalid configuration file specified, %s does not exist', ConfigPaths::prettyPath($file)));
        }

        $__psysh_config_file__ = $file;
        $load = function ($config) use ($__psysh_config_file__) {
            $result = require $__psysh_config_file__;
            if ($result !== 1) {
                return $result;
            }
        };
        $result = $load($this);

        if (!empty($result)) {
            if (\is_array($result)) {
                $this->loadConfig($result);
            } else {
                throw new \InvalidArgumentException('Psy Shell configuration must return an array of options');
            }
        }
    }

    /**
     * Set files to be included by default at the start of each shell session.
     *
     * @param array $includes
     */
    public function setDefaultIncludes(array $includes = [])
    {
        $this->defaultIncludes = $includes;
    }

    /**
     * Get files to be included by default at the start of each shell session.
     *
     * @return string[]
     */
    public function getDefaultIncludes(): array
    {
        return $this->defaultIncludes ?: [];
    }

    /**
     * Set the shell's config directory location.
     *
     * @param string $dir
     */
    public function setConfigDir(string $dir)
    {
        $this->configDir = (string) $dir;

        $this->configPaths->overrideDirs([
            'configDir'  => $this->configDir,
            'dataDir'    => $this->dataDir,
            'runtimeDir' => $this->runtimeDir,
        ]);
    }

    /**
     * Get the current configuration directory, if any is explicitly set.
     *
     * @return string|null
     */
    public function getConfigDir()
    {
        return $this->configDir;
    }

    /**
     * Set the shell's data directory location.
     *
     * @param string $dir
     */
    public function setDataDir(string $dir)
    {
        $this->dataDir = (string) $dir;

        $this->configPaths->overrideDirs([
            'configDir'  => $this->configDir,
            'dataDir'    => $this->dataDir,
            'runtimeDir' => $this->runtimeDir,
        ]);
    }

    /**
     * Get the current data directory, if any is explicitly set.
     *
     * @return string|null
     */
    public function getDataDir()
    {
        return $this->dataDir;
    }

    /**
     * Set the shell's temporary directory location.
     *
     * @param string $dir
     */
    public function setRuntimeDir(string $dir)
    {
        $this->runtimeDir = (string) $dir;

        $this->configPaths->overrideDirs([
            'configDir'  => $this->configDir,
            'dataDir'    => $this->dataDir,
            'runtimeDir' => $this->runtimeDir,
        ]);
    }

    /**
     * Get the shell's temporary directory location.
     *
     * Defaults to `/psysh` inside the system's temp dir unless explicitly
     * overridden.
     *
     * @throws RuntimeException if no temporary directory is set and it is not possible to create one
     *
     * @param bool $create False to suppress directory creation if it does not exist
     */
    public function getRuntimeDir($create = true): string
    {
        $runtimeDir = $this->configPaths->runtimeDir();

        if ($create) {
            if (!@ConfigPaths::ensureDir($runtimeDir)) {
                throw new RuntimeException(\sprintf('Unable to create PsySH runtime directory. Make sure PHP is able to write to %s in order to continue.', \dirname($runtimeDir)));
            }
        }

        return $runtimeDir;
    }

    /**
     * Set the readline history file path.
     *
     * @param string $file
     */
    public function setHistoryFile(string $file)
    {
        $this->historyFile = ConfigPaths::touchFileWithMkdir($file);
    }

    /**
     * Get the readline history file path.
     *
     * Defaults to `/psysh_history` (standard readline) or
     * `/psysh_history.jsonl` (interactive readline) in the shell's config dir
     * unless explicitly overridden.
     */
    public function getHistoryFile(): ?string
    {
        if (isset($this->historyFile)) {
            return $this->historyFile;
        }

        $configDir = $this->configPaths->currentConfigDir();
        if ($configDir === null) {
            return null;
        }

        if ($this->usesInteractiveHistoryFormat()) {
            $jsonlHistoryFile = $configDir.'/psysh_history.jsonl';
            $this->bootstrapInteractiveHistoryFile($jsonlHistoryFile);
            $this->setHistoryFile($jsonlHistoryFile);

            return $this->historyFile;
        }

        $files = $this->configPaths->configFiles(['psysh_history', 'history']);

        if (!empty($files)) {
            if ($this->warnOnMultipleConfigs && \count($files) > 1) {
                $prettyFiles = \array_map([ConfigPaths::class, 'prettyPath'], $files);
                $msg = \sprintf('Multiple history files found: %s. Using %s', \implode(', ', $prettyFiles), $prettyFiles[0]);
                \trigger_error($msg, \E_USER_NOTICE);
            }

            $this->setHistoryFile($files[0]);
        } else {
            $this->setHistoryFile($configDir.'/psysh_history');
        }

        return $this->historyFile;
    }

    /**
     * Determine whether history should use the interactive JSONL format.
     */
    private function usesInteractiveHistoryFormat(): bool
    {
        if (isset($this->readline) && $this->readline instanceof Readline\InteractiveReadlineInterface) {
            return true;
        }

        return $this->getReadlineClass() === Readline\InteractiveReadline::class;
    }

    /**
     * Bootstrap interactive JSONL history from legacy history file if needed.
     *
     * This is intentionally one-way: once JSONL exists, legacy and JSONL
     * history files diverge.
     */
    private function bootstrapInteractiveHistoryFile(string $jsonlHistoryFile): void
    {
        if (@\is_file($jsonlHistoryFile)) {
            return;
        }

        $legacyFiles = $this->configPaths->configFiles(['psysh_history', 'history']);
        if (empty($legacyFiles)) {
            return;
        }

        $legacyHistoryFile = $legacyFiles[0];
        if (!@\is_readable($legacyHistoryFile)) {
            return;
        }

        $history = new InteractiveHistory($this->getHistorySize(), $this->getEraseDuplicates() ?? false);
        $history->importFromFile($legacyHistoryFile);
        $history->saveToFile($jsonlHistoryFile);
    }

    /**
     * Set the readline max history size.
     *
     * @param int $value
     */
    public function setHistorySize(int $value)
    {
        $this->historySize = (int) $value;
    }

    /**
     * Get the readline max history size.
     *
     * @return int
     */
    public function getHistorySize()
    {
        return $this->historySize;
    }

    /**
     * Sets whether readline erases old duplicate history entries.
     *
     * @param bool $value
     */
    public function setEraseDuplicates(bool $value)
    {
        $this->eraseDuplicates = $value;
    }

    /**
     * Get whether readline erases old duplicate history entries.
     *
     * @return bool|null
     */
    public function getEraseDuplicates()
    {
        return $this->eraseDuplicates;
    }

    /**
     * Get a temporary file of type $type for process $pid.
     *
     * The file will be created inside the current temporary directory.
     *
     * @see self::getRuntimeDir
     *
     * @param string $type
     * @param int    $pid
     *
     * @return string Temporary file name
     */
    public function getTempFile(string $type, int $pid): string
    {
        return \tempnam($this->getRuntimeDir(), $type.'_'.$pid.'_');
    }

    /**
     * Get a filename suitable for a FIFO pipe of $type for process $pid.
     *
     * The pipe will be created inside the current temporary directory.
     *
     * @param string $type
     * @param int    $pid
     *
     * @return string Pipe name
     */
    public function getPipe(string $type, int $pid): string
    {
        return \sprintf('%s/%s_%s', $this->getRuntimeDir(), $type, $pid);
    }

    /**
     * Check whether this PHP instance has Readline available.
     *
     * @return bool True if Readline is available
     */
    public function hasReadline(): bool
    {
        return $this->hasReadline;
    }

    /**
     * Enable or disable Readline usage.
     *
     * @param bool $useReadline
     */
    public function setUseReadline(bool $useReadline)
    {
        $this->useReadline = (bool) $useReadline;
    }

    /**
     * Check whether to use Readline.
     *
     * If `setUseReadline` as been set to true, but Readline is not actually
     * available, this will return false.
     *
     * @return bool True if the current Shell should use Readline
     */
    public function useReadline(): bool
    {
        return isset($this->useReadline) ? ($this->hasReadline && $this->useReadline) : $this->hasReadline;
    }

    /**
     * Set the Psy Shell readline service.
     *
     * @param Readline\Readline $readline
     */
    public function setReadline(Readline\Readline $readline)
    {
        $this->readline = $readline;
    }

    /**
     * Get the Psy Shell readline service.
     *
     * By default, this service uses (in order of preference):
     *
     *  * GNU Readline
     *  * Libedit
     *  * A transient array-based readline emulation.
     *
     * @return Readline\Readline
     */
    public function getReadline(): Readline\Readline
    {
        if (!isset($this->readline)) {
            $className = $this->getReadlineClass();

            $this->readline = new $className(
                $this->getHistoryFile(),
                $this->getHistorySize(),
                $this->getEraseDuplicates() ?? false
            );
        }

        return $this->readline;
    }

    /**
     * Get the appropriate Readline implementation class name.
     *
     * @see self::getReadline
     */
    private function getReadlineClass(): string
    {
        // Experimental interactive readline (opt-in via config or --experimental-readline)
        if ($this->useExperimentalReadline() && Readline\InteractiveReadline::isSupported()) {
            return Readline\InteractiveReadline::class;
        }

        if ($this->useReadline()) {
            if (Readline\GNUReadline::isSupported()) {
                return Readline\GNUReadline::class;
            } elseif (Readline\Libedit::isSupported()) {
                return Readline\Libedit::class;
            }
        }

        if (Readline\Userland::isSupported()) {
            return Readline\Userland::class;
        }

        return Readline\Transient::class;
    }

    /**
     * Enable or disable bracketed paste.
     *
     * Note that this only works with readline (not libedit) integration for now.
     *
     * @param bool $useBracketedPaste
     */
    public function setUseBracketedPaste(bool $useBracketedPaste)
    {
        $this->useBracketedPaste = (bool) $useBracketedPaste;
    }

    /**
     * Check whether to use bracketed paste with readline.
     *
     * When this works, it's magical. Tabs in pastes don't try to autcomplete.
     * Newlines in paste don't execute code until you get to the end. It makes
     * readline act like you'd expect when pasting.
     *
     * But it often (usually?) does not work. And when it doesn't, it just spews
     * escape codes all over the place and generally makes things ugly :(
     *
     * If `useBracketedPaste` has been set to true, but the current readline
     * implementation is anything besides GNU readline, this will return false.
     *
     * @return bool True if the shell should use bracketed paste
     */
    public function useBracketedPaste(): bool
    {
        $readlineClass = $this->getReadlineClass();

        return $this->useBracketedPaste && $readlineClass::supportsBracketedPaste();

        // @todo mebbe turn this on by default some day?
        // return $readlineClass::supportsBracketedPaste() && $this->useBracketedPaste !== false;
    }

    /**
     * Set a custom clipboard command.
     *
     * @param string|null $clipboardCommand
     */
    public function setClipboardCommand(?string $clipboardCommand)
    {
        $this->clipboardCommand = $clipboardCommand !== null && \trim($clipboardCommand) !== ''
            ? $clipboardCommand
            : null;
        $this->clipboard = null;
    }

    /**
     * Get the configured clipboard command, if any.
     */
    public function clipboardCommand(): ?string
    {
        return $this->clipboardCommand;
    }

    /**
     * Set the ClipboardMethod service.
     */
    public function setClipboard(ClipboardMethod $clipboard)
    {
        $this->clipboard = $clipboard;
    }

    /**
     * Get the ClipboardMethod service.
     */
    public function getClipboard(): ClipboardMethod
    {
        if (isset($this->clipboard)) {
            return $this->clipboard;
        }

        if ($this->clipboardCommand !== null && \function_exists('proc_open')) {
            return $this->clipboard = new CommandClipboardMethod($this->clipboardCommand);
        }

        $isSsh = $this->isSshSession();
        if ($isSsh) {
            return $this->clipboard = $this->useOsc52Clipboard()
                ? new Osc52ClipboardMethod()
                : new NullClipboardMethod(true);
        }

        if (\function_exists('proc_open')) {
            foreach (self::KNOWN_CLIPBOARD_COMMANDS as $command) {
                if ($this->clipboardCommandExists($command)) {
                    return $this->clipboard = new CommandClipboardMethod($command);
                }
            }
        }

        if ($this->useOsc52Clipboard()) {
            return $this->clipboard = new Osc52ClipboardMethod();
        }

        return $this->clipboard = new NullClipboardMethod(
            false,
            $this->clipboardCommand !== null
                ? NullClipboardMethod::REASON_NO_COMMAND_SUPPORT
                : NullClipboardMethod::REASON_NO_COMMAND
        );
    }

    /**
     * Enable or disable OSC 52 clipboard support.
     *
     * @param bool $useOsc52Clipboard
     */
    public function setUseOsc52Clipboard(bool $useOsc52Clipboard)
    {
        $this->useOsc52Clipboard = (bool) $useOsc52Clipboard;
        $this->clipboard = null;
    }

    /**
     * Check whether to use OSC 52 clipboard support.
     */
    public function useOsc52Clipboard(): bool
    {
        return $this->useOsc52Clipboard;
    }

    private function isSshSession(): bool
    {
        return \getenv('SSH_TTY') !== false
            || \getenv('SSH_CLIENT') !== false
            || \getenv('SSH_CONNECTION') !== false;
    }

    /**
     * @phpstan-param non-empty-string $command
     */
    private function clipboardCommandExists(string $command): bool
    {
        $bin = \explode(' ', $command, 2)[0];

        return $this->configPaths->which($bin) !== null;
    }

    /**
     * Check whether this PHP instance has Pcntl available.
     *
     * @return bool True if Pcntl is available
     */
    public function hasPcntl(): bool
    {
        return $this->hasPcntl;
    }

    /**
     * Enable or disable Pcntl usage.
     *
     * @param bool $usePcntl
     */
    public function setUsePcntl(bool $usePcntl)
    {
        $this->usePcntl = (bool) $usePcntl;
    }

    /**
     * Check whether to use Pcntl.
     *
     * If `setUsePcntl` has been set to true, but Pcntl is not actually
     * available, this will return false.
     *
     * @return bool True if the current Shell should use Pcntl
     */
    public function usePcntl(): bool
    {
        if (!isset($this->usePcntl)) {
            // Unless pcntl is explicitly *enabled*, don't use it while XDebug is debugging.
            // See https://github.com/bobthecow/psysh/issues/742
            if (\function_exists('xdebug_is_debugger_active') && \xdebug_is_debugger_active()) {
                return false;
            }

            return $this->hasPcntl;
        }

        return $this->hasPcntl && $this->usePcntl;
    }

    /**
     * Check whether to use raw output.
     *
     * This is set by the --raw-output (-r) flag, and really only makes sense
     * when non-interactive, e.g. executing stdin.
     *
     * @return bool true if raw output is enabled
     */
    public function rawOutput(): bool
    {
        return $this->rawOutput;
    }

    /**
     * Enable or disable raw output.
     *
     * @param bool $rawOutput
     */
    public function setRawOutput(bool $rawOutput)
    {
        $this->rawOutput = (bool) $rawOutput;
    }

    /**
     * Enable or disable strict requirement of semicolons.
     *
     * @see self::requireSemicolons()
     *
     * @param bool $requireSemicolons
     */
    public function setRequireSemicolons(bool $requireSemicolons)
    {
        $this->requireSemicolons = (bool) $requireSemicolons;
    }

    /**
     * Check whether to require semicolons on all statements.
     *
     * By default, PsySH will automatically insert semicolons at the end of
     * statements if they're missing. To strictly require semicolons, set
     * `requireSemicolons` to true.
     */
    public function requireSemicolons(): bool
    {
        return $this->requireSemicolons;
    }

    /**
     * Enable or disable semicolons suppressing return value display.
     *
     * @see self::semicolonsSuppressReturn()
     */
    public function setSemicolonsSuppressReturn($semicolonsSuppressReturn)
    {
        if (\is_bool($semicolonsSuppressReturn)) {
            $this->semicolonsSuppressReturn = $semicolonsSuppressReturn;

            return;
        }

        if ($semicolonsSuppressReturn === self::SEMICOLONS_SUPPRESS_RETURN_DOUBLE) {
            $this->semicolonsSuppressReturn = self::SEMICOLONS_SUPPRESS_RETURN_DOUBLE;

            return;
        }

        $given = \is_scalar($semicolonsSuppressReturn)
            ? (string) $semicolonsSuppressReturn
            : \gettype($semicolonsSuppressReturn);

        throw new \InvalidArgumentException(\sprintf('Invalid semicolonsSuppressReturn value: %s. Accepted values: true, false, \'%s\'', $given, self::SEMICOLONS_SUPPRESS_RETURN_DOUBLE));
    }

    /**
     * Check whether semicolons suppress return value display.
     *
     * When enabled, an unnecessary trailing semicolon suppresses the return
     * value display (like Pry and MATLAB). The return value is still captured
     * in $_. In `double` mode, a double semicolon (`;;`) is always required
     * to suppress, regardless of the `requireSemicolons` setting.
     *
     * @return bool|string
     */
    public function semicolonsSuppressReturn()
    {
        return $this->semicolonsSuppressReturn;
    }

    /**
     * Enable or disable strict types enforcement.
     */
    public function setStrictTypes($strictTypes)
    {
        $this->strictTypes = (bool) $strictTypes;
    }

    /**
     * Check whether to enforce strict types.
     */
    public function strictTypes(): bool
    {
        return $this->strictTypes;
    }

    /**
     * Enable or disable Unicode in PsySH specific output.
     *
     * Note that this does not disable Unicode output in general, it just makes
     * it so PsySH won't output any itself.
     *
     * @param bool $useUnicode
     */
    public function setUseUnicode(bool $useUnicode)
    {
        $this->useUnicode = (bool) $useUnicode;
    }

    /**
     * Check whether to use Unicode in PsySH specific output.
     *
     * Note that this does not disable Unicode output in general, it just makes
     * it so PsySH won't output any itself.
     */
    public function useUnicode(): bool
    {
        if (isset($this->useUnicode)) {
            return $this->useUnicode;
        }

        // @todo detect `chsh` != 65001 on Windows and return false
        return true;
    }

    /**
     * Set the error logging level.
     *
     * @see self::errorLoggingLevel
     *
     * @param int $errorLoggingLevel
     */
    public function setErrorLoggingLevel($errorLoggingLevel)
    {
        if (\PHP_VERSION_ID < 80400) {
            $this->errorLoggingLevel = (\E_ALL | \E_STRICT) & $errorLoggingLevel;
        } else {
            $this->errorLoggingLevel = \E_ALL & $errorLoggingLevel;
        }
    }

    /**
     * Get the current error logging level.
     *
     * By default, PsySH will automatically log all errors, regardless of the
     * current `error_reporting` level.
     *
     * Set `errorLoggingLevel` to 0 to prevent logging non-thrown errors. Set it
     * to any valid error_reporting value to log only errors which match that
     * level.
     *
     *     http://php.net/manual/en/function.error-reporting.php
     */
    public function errorLoggingLevel(): int
    {
        return $this->errorLoggingLevel;
    }

    /**
     * Set a CodeCleaner service instance.
     *
     * @param CodeCleaner $cleaner
     */
    public function setCodeCleaner(CodeCleaner $cleaner)
    {
        $this->cleaner = $cleaner;
    }

    /**
     * Get a CodeCleaner service instance.
     *
     * If none has been explicitly defined, this will create a new instance.
     */
    public function getCodeCleaner(): CodeCleaner
    {
        if (!isset($this->cleaner)) {
            $this->cleaner = new CodeCleaner(null, null, null, $this->yolo(), $this->strictTypes(), $this->implicitUse);
        }

        return $this->cleaner;
    }

    /**
     * Enable or disable running PsySH without input validation.
     *
     * You don't want this.
     */
    public function setYolo($yolo)
    {
        $this->yolo = (bool) $yolo;
    }

    /**
     * Check whether to disable input validation.
     */
    public function yolo(): bool
    {
        return $this->yolo;
    }

    /**
     * Enable experimental interactive readline implementation.
     */
    public function setUseExperimentalReadline(bool $enabled): void
    {
        $this->useExperimentalReadline = $enabled;
    }

    /**
     * Check whether to use experimental interactive readline.
     */
    public function useExperimentalReadline(): bool
    {
        return $this->useExperimentalReadline;
    }

    /**
     * Enable or disable syntax highlighting in interactive readline.
     */
    public function setUseSyntaxHighlighting(bool $enabled): void
    {
        $this->useSyntaxHighlighting = $enabled;
    }

    /**
     * Check whether syntax highlighting is enabled in interactive readline.
     */
    public function useSyntaxHighlighting(): bool
    {
        return $this->useSyntaxHighlighting;
    }

    /**
     * Enable inline suggestions in interactive readline.
     */
    public function setUseSuggestions(bool $enabled): void
    {
        $this->useSuggestions = $enabled;
    }

    /**
     * Check whether inline suggestions are enabled.
     */
    public function useSuggestions(): bool
    {
        return $this->useSuggestions;
    }

    /**
     * Enable or disable tab completion.
     *
     * @param bool $useTabCompletion
     */
    public function setUseTabCompletion(bool $useTabCompletion)
    {
        $this->useTabCompletion = (bool) $useTabCompletion;
    }

    /**
     * @deprecated Call `setUseTabCompletion` instead
     *
     * @param bool $useTabCompletion
     */
    public function setTabCompletion(bool $useTabCompletion)
    {
        @\trigger_error('`setTabCompletion` is deprecated; call `setUseTabCompletion` instead.', \E_USER_DEPRECATED);

        $this->setUseTabCompletion($useTabCompletion);
    }

    /**
     * Check whether to use tab completion.
     *
     * If `setUseTabCompletion` has been set to true, but readline is not
     * actually available, this will return false.
     *
     * @return bool True if the current Shell should use tab completion
     */
    public function useTabCompletion(): bool
    {
        if (isset($this->readline) && $this->readline instanceof Readline\InteractiveReadlineInterface) {
            return $this->useTabCompletion ?? true;
        }

        return isset($this->useTabCompletion) ? ($this->hasReadline && $this->useTabCompletion) : $this->hasReadline;
    }

    /**
     * @deprecated Call `useTabCompletion` instead
     */
    public function getTabCompletion(): bool
    {
        @\trigger_error('`getTabCompletion` is deprecated; call `useTabCompletion` instead.', \E_USER_DEPRECATED);

        return $this->useTabCompletion();
    }

    /**
     * Set the Shell Output service.
     *
     * @param ShellOutput $output
     */
    public function setOutput(ShellOutput $output)
    {
        $this->output = $output;
        $this->pipedOutput = null; // Reset cached pipe info

        if (isset($this->theme)) {
            $output->setTheme($this->theme);
        }

        $this->applyFormatterStyles();
        $this->invalidatePresenter();
    }

    /**
     * Get a Shell Output service instance.
     *
     * If none has been explicitly provided, this will create a new instance
     * with the configured verbosity and output pager supplied by self::getPager
     *
     * @see self::verbosity
     * @see self::getPager
     */
    public function getOutput(): ShellOutput
    {
        if (!isset($this->output)) {
            // `true` means "use the built-in userland pager"; we can't
            // construct it here because the interactive readline hasn't
            // booted yet. Start with no pager and let Shell wire it up.
            $pagerArg = $this->getPager();
            if ($pagerArg === true) {
                $pagerArg = null;
            }
            $this->setOutput(new ShellOutput(
                $this->getOutputVerbosity(),
                null,
                null,
                $pagerArg ?: null,
                $this->theme()
            ));

            // This is racy because `getOutputDecorated` needs access to the
            // output stream to figure out if it's piped or not, so create it
            // first, then update after we have a stream.
            $decorated = $this->getOutputDecorated();
            if ($decorated !== null && $this->output !== null) {
                $this->output->setDecorated($decorated);
            }
        }

        return $this->output;
    }

    /**
     * Get the decoration (i.e. color) setting for the Shell Output service.
     *
     * @return bool|null 3-state boolean corresponding to the current color mode
     */
    public function getOutputDecorated()
    {
        switch ($this->colorMode()) {
            case self::COLOR_MODE_FORCED:
                return true;
            case self::COLOR_MODE_DISABLED:
                return false;
            case self::COLOR_MODE_AUTO:
            default:
                return $this->outputIsPiped() ? false : null;
        }
    }

    /**
     * Get the interactive setting for shell input.
     */
    public function getInputInteractive(): bool
    {
        switch ($this->interactiveMode()) {
            case self::INTERACTIVE_MODE_FORCED:
                return true;
            case self::INTERACTIVE_MODE_DISABLED:
                return false;
            case self::INTERACTIVE_MODE_AUTO:
            default:
                return !$this->inputIsPiped();
        }
    }

    /**
     * Set the OutputPager service.
     *
     * Accepted values:
     *   - `false` (or `null`, or `'cat'`): never page.
     *   - `true`: use the built-in userland pager when interactive readline
     *     is active.
     *   - a string command: shell out to that command via ProcOutputPager.
     *   - an OutputPager instance: use it directly.
     *
     * @throws \InvalidArgumentException if $pager is not one of the above
     *
     * @param string|OutputPager|bool|null $pager
     */
    public function setPager($pager)
    {
        if ($pager === null || $pager === false || $pager === 'cat') {
            $pager = false;
        }

        if ($pager !== false && $pager !== true && !\is_string($pager) && !$pager instanceof OutputPager) {
            throw new \InvalidArgumentException('Unexpected pager instance');
        }

        $this->pager = $pager;
    }

    /**
     * Use the default pager resolution for this configuration.
     */
    public function setDefaultPager(): void
    {
        $this->pager = null;
    }

    /**
     * Get an OutputPager instance or a command for an external Proc pager.
     *
     * If no Pager has been explicitly provided, and Pcntl is available, this
     * will default to `cli.pager` ini value, falling back to `which less`.
     *
     * @return string|OutputPager|bool
     */
    public function getPager()
    {
        // When the interactive readline is configured, prefer the built-in
        // userland pager. Wired up by Shell after the readline boots.
        if (!isset($this->pager)
            && $this->useExperimentalReadline()
            && $this->getInputInteractive()
            && Readline\InteractiveReadline::isSupported()
        ) {
            return true;
        }

        if (!isset($this->pager) && $this->usePcntl()) {
            if (\getenv('TERM') === 'dumb') {
                return false;
            }

            if ($pager = \ini_get('cli.pager')) {
                // use the default pager
                $this->pager = $pager;
            } elseif ($less = $this->configPaths->which('less')) {
                // check for the presence of less...

                // n.b. The busybox less implementation is a bit broken, so
                // let's not use it by default.
                //
                // See https://github.com/bobthecow/psysh/issues/778
                if (@\is_link($less)) {
                    $link = @\readlink($less);
                    if ($link !== false && \strpos($link, 'busybox') !== false) {
                        return false;
                    }
                }

                $this->pager = $less.' -R -F -X';
            }
        }

        return $this->pager;
    }

    /**
     * Set the Shell AutoCompleter service.
     *
     * @param AutoCompleter $autoCompleter
     */
    public function setAutoCompleter(AutoCompleter $autoCompleter)
    {
        $this->autoCompleter = $autoCompleter;
    }

    /**
     * Get an AutoCompleter service instance.
     */
    public function getAutoCompleter(): AutoCompleter
    {
        if (!isset($this->autoCompleter)) {
            $this->autoCompleter = new AutoCompleter();
        }

        return $this->autoCompleter;
    }

    /**
     * @deprecated Nothing should be using this anymore
     */
    public function getTabCompletionMatchers(): array
    {
        @\trigger_error('`getTabCompletionMatchers` is no longer used.', \E_USER_DEPRECATED);

        return [];
    }

    /**
     * Add tab completion matchers to the AutoCompleter.
     *
     * This will buffer new matchers in the event that the Shell has not yet
     * been instantiated. This allows the user to specify matchers in their
     * config rc file, despite the fact that their file is needed in the Shell
     * constructor.
     *
     * @param array $matchers
     */
    public function addMatchers(array $matchers)
    {
        $this->matchers = \array_merge($this->matchers, $matchers);
        if (isset($this->shell)) {
            $this->syncShellExtensions();
        }
    }

    /**
     * Add completion sources.
     *
     * @internal experimental; API may change before Interactive Readline is stable
     *
     * @param Completion\Source\SourceInterface[] $sources
     */
    public function addCompletionSources(array $sources)
    {
        $this->completionSources = \array_merge($this->completionSources, $sources);
        if (isset($this->shell)) {
            $this->syncShellExtensions();
        }
    }

    /**
     * Configure autoload warming.
     *
     * @param bool|array $config False to disable, true for defaults, or array for custom config
     */
    public function setWarmAutoload($config): void
    {
        if (!\is_bool($config) && !\is_array($config)) {
            throw new \InvalidArgumentException('warmAutoload must be a boolean or configuration array');
        }

        // Parse and store warmers immediately
        $this->autoloadWarmers = $this->parseWarmAutoloadConfig($config);
    }

    /**
     * Get configured autoload warmers.
     *
     * If no warmers are explicitly configured, returns a default ComposerAutoloadWarmer
     * with smart settings that work for most projects.
     *
     * To disable autoload warming, set 'warmAutoload' to false.
     *
     * @return TabCompletion\AutoloadWarmer\AutoloadWarmerInterface[]
     */
    public function getAutoloadWarmers(): array
    {
        if ($this->autoloadWarmers === null) {
            $this->autoloadWarmers = $this->parseWarmAutoloadConfig(false);
        }

        if ($this->projectTrust->getForceTrust() || $this->projectTrust->getMode() === self::PROJECT_TRUST_ALWAYS) {
            return $this->autoloadWarmers;
        }

        $projectRoot = $this->projectTrust->getProjectRoot();
        if ($projectRoot !== null && ($this->projectTrust->getForceUntrust() || !$this->projectTrust->isProjectTrusted($projectRoot))) {
            $filtered = \array_values(\array_filter(
                $this->autoloadWarmers,
                fn ($warmer) => !$warmer instanceof TabCompletion\AutoloadWarmer\ComposerAutoloadWarmer
            ));

            if (\count($filtered) !== \count($this->autoloadWarmers)) {
                $this->projectTrust->warnUntrustedAutoloadWarming($projectRoot, $this->getOutput());
            }

            return $filtered;
        }

        return $this->autoloadWarmers;
    }

    /**
     * Parse warmAutoload configuration into autoload warmers.
     *
     * Accepts three types of configuration:
     * - true: Enable with default ComposerAutoloadWarmer
     * - false: Disable warming entirely (default)
     * - array: Custom configuration for ComposerAutoloadWarmer and/or custom warmers
     *
     * When a config array is provided:
     * - Empty array [] disables warming
     * - 'warmers' key provides custom warmer instances
     * - Other keys configure a ComposerAutoloadWarmer (implicitly enables)
     * - Both can be combined: custom warmers + configured ComposerAutoloadWarmer
     *
     * @param bool|array $config Configuration value
     *
     * @return TabCompletion\AutoloadWarmer\AutoloadWarmerInterface[]
     */
    private function parseWarmAutoloadConfig($config): array
    {
        // false = disable entirely
        if ($config === false) {
            return [];
        }

        // true = use default ComposerAutoloadWarmer
        if ($config === true) {
            return [new TabCompletion\AutoloadWarmer\ComposerAutoloadWarmer()];
        }

        // array = custom configuration
        if (!\is_array($config)) {
            throw new \InvalidArgumentException('warmAutoload must be a boolean or configuration array');
        }

        $warmers = [];

        // Extract explicit warmers if provided
        if (isset($config['warmers'])) {
            $explicitWarmers = $config['warmers'];
            if (!\is_array($explicitWarmers)) {
                throw new \InvalidArgumentException('warmAutoload[\'warmers\'] must be an array');
            }

            foreach ($explicitWarmers as $warmer) {
                if (!$warmer instanceof TabCompletion\AutoloadWarmer\AutoloadWarmerInterface) {
                    throw new \InvalidArgumentException('Autoload warmers must implement AutoloadWarmerInterface');
                }
                $warmers[] = $warmer;
            }

            unset($config['warmers']);
        }

        // If there are remaining config options, create a ComposerAutoloadWarmer with them
        if (!empty($config)) {
            $warmers[] = new TabCompletion\AutoloadWarmer\ComposerAutoloadWarmer($config);
        }

        return $warmers;
    }

    /**
     * Set implicit use statement configuration.
     *
     * Automatically adds use statements for unqualified class references when
     * a single, non-ambiguous match is found among currently defined classes,
     * interfaces, and traits within the configured namespaces.
     *
     * Works great with autoload warming (--warm-autoload) to pre-load classes
     * for better resolution. Also works with dynamically defined classes.
     *
     * Examples:
     *
     *     // Disable implicit use (default)
     *     $config->setImplicitUse(false);
     *
     *     // Enable for specific namespaces
     *     $config->setImplicitUse([
     *         'includeNamespaces' => ['App\\', 'Domain\\'],
     *     ]);
     *
     *     // Enable with exclusions
     *     $config->setImplicitUse([
     *         'includeNamespaces' => ['App\\'],
     *         'excludeNamespaces' => ['App\\Legacy\\'],
     *     ]);
     *
     * Note: At least one of includeNamespaces or excludeNamespaces must be provided.
     * If neither is provided, implicit use effectively does nothing.
     *
     * @param false|array $config False to disable, or array with includeNamespaces/excludeNamespaces
     */
    public function setImplicitUse($config): void
    {
        if ($config === false) {
            $this->implicitUse = false;

            return;
        }

        if (!\is_array($config)) {
            throw new \InvalidArgumentException('implicitUse must be false or a configuration array with includeNamespaces and/or excludeNamespaces');
        }

        $this->implicitUse = $config;
    }

    /**
     * Get implicit use configuration.
     *
     * @return bool|array Implicit use configuration
     */
    public function getImplicitUse()
    {
        return $this->implicitUse;
    }

    /**
     * Configure logging.
     *
     * Logs PsySH input, commands, and executed code to the provided logger.
     * Accepts a PSR-3 logger, a simple callback, or an array for more control
     * over log levels.
     *
     * Examples:
     *
     *     // Simple callback logging
     *     $config->setLogging(function ($kind, $data) {
     *         $line = sprintf("[%s] %s\n", $kind, $data);
     *         file_put_contents('/tmp/psysh.log', $line, FILE_APPEND);
     *     });
     *
     *     // PSR-3 logger with defaults (input=info, command=info, execute=debug)
     *     $config->setLogging($psrLogger);
     *
     *     // Set single level for all event types
     *     $config->setLogging([
     *         'logger' => $psrLogger,
     *         'level' => 'debug',
     *     ]);
     *
     *     // Granular control over each event type
     *     $config->setLogging([
     *         'logger' => $psrLogger,
     *         'level' => [
     *             'input'   => 'info',
     *             'command' => false, // disable logging
     *             'execute' => 'debug',
     *         ],
     *     ]);
     *
     * @param \Psr\Log\LoggerInterface|callable|array $logging
     */
    public function setLogging($logging): void
    {
        $this->logger = $this->parseLoggingConfig($logging);
    }

    /**
     * Get a ShellLogger instance if logging is configured.
     *
     * @return ShellLogger|null
     */
    public function getLogger(): ?ShellLogger
    {
        return $this->logger;
    }

    /**
     * Configure a callback that returns additional structured exception details.
     *
     * The callback receives the thrown exception and may return any dumpable
     * value; returning null suppresses additional output.
     */
    public function setExceptionDetails(callable $exceptionDetails): void
    {
        $this->exceptionDetails = \Closure::fromCallable($exceptionDetails);
    }

    /**
     * Get the configured exception details callback, if any.
     */
    public function getExceptionDetails(): ?\Closure
    {
        return $this->exceptionDetails;
    }

    /**
     * Get an InputLoggingListener if input logging is enabled.
     *
     * @return InputLoggingListener|null
     */
    public function getInputLogger(): ?InputLoggingListener
    {
        $logger = $this->getLogger();
        if ($logger === null || $logger->isInputDisabled()) {
            return null;
        }

        return new InputLoggingListener($logger);
    }

    /**
     * Get an ExecutionLoggingListener if execution logging is enabled.
     *
     * @return ExecutionLoggingListener|null
     */
    public function getExecutionLogger(): ?ExecutionLoggingListener
    {
        $logger = $this->getLogger();
        if ($logger === null || $logger->isExecuteDisabled()) {
            return null;
        }

        return new ExecutionLoggingListener($logger);
    }

    /**
     * Parse logging configuration.
     *
     * @param \Psr\Log\LoggerInterface|Logger\CallbackLogger|callable|array $config
     *
     * @return ShellLogger
     */
    private function parseLoggingConfig($config): ShellLogger
    {
        if (!\is_array($config)) {
            $config = ['logger' => $config];
        }

        if (!isset($config['logger'])) {
            throw new \InvalidArgumentException('Logging config array must include a "logger" key');
        }

        $logger = $config['logger'];

        if (\is_callable($logger)) {
            $logger = new CallbackLogger($logger);
        }

        if (!$this->isLogger($logger)) {
            throw new \InvalidArgumentException('Logging "logger" must be a logger instance or callable');
        }

        $defaults = [
            'input'   => 'info',
            'command' => 'info',
            'execute' => 'debug',
        ];

        if (isset($config['level'])) {
            $level = $config['level'];

            // String: apply same level to all types
            if (\is_string($level)) {
                $levels = [
                    'input'   => $level,
                    'command' => $level,
                    'execute' => $level,
                ];
            } elseif (\is_array($level)) {
                // Array: granular per-type levels
                $levels = [
                    'input'   => $level['input'] ?? $defaults['input'],
                    'command' => $level['command'] ?? $defaults['command'],
                    'execute' => $level['execute'] ?? $defaults['execute'],
                ];
            } else {
                throw new \InvalidArgumentException('Logging "level" must be a string or array');
            }
        } else {
            $levels = $defaults;
        }

        return new ShellLogger($logger, $levels);
    }

    /**
     * Check if a value is a valid logger instance.
     *
     * @param mixed $logger
     *
     * @return bool
     */
    private function isLogger($logger): bool
    {
        if ($logger instanceof CallbackLogger) {
            return true;
        }

        // Safe check for LoggerInterface without requiring psr/log as a dependency
        return \interface_exists('Psr\Log\LoggerInterface') && $logger instanceof \Psr\Log\LoggerInterface;
    }

    /**
     * @deprecated Use `addMatchers` instead
     *
     * @param array $matchers
     */
    public function addTabCompletionMatchers(array $matchers)
    {
        @\trigger_error('`addTabCompletionMatchers` is deprecated; call `addMatchers` instead.', \E_USER_DEPRECATED);

        $this->addMatchers($matchers);
    }

    /**
     * Add commands to the Shell.
     *
     * This will buffer new commands in the event that the Shell has not yet
     * been instantiated. This allows the user to specify commands in their
     * config rc file, despite the fact that their file is needed in the Shell
     * constructor.
     *
     * @param array $commands
     */
    public function addCommands(array $commands)
    {
        $this->commands = \array_merge($this->commands, $commands);
        if (isset($this->shell)) {
            $this->syncShellExtensions();
        }
    }

    /**
     * Sync configuration-driven extensions into the active shell.
     *
     * The shell handles deduplication, so we can safely forward everything.
     */
    private function syncShellExtensions(): void
    {
        $this->shell->addCommands($this->commands);
        $this->shell->addMatchers($this->matchers);
        $this->shell->addCompletionSources($this->completionSources);
    }

    /**
     * Set the Shell backreference and add any new commands to the Shell.
     *
     * @param Shell $shell
     */
    public function setShell(Shell $shell)
    {
        $this->shell = $shell;
        $this->syncShellExtensions();

        // Configure SignatureFormatter for hyperlinks
        SignatureFormatter::setManual($this->getManual());
    }

    /**
     * Set the PHP manual database file.
     *
     * This file should be an SQLite database generated from the phpdoc source
     * with the `bin/build_manual` script.
     *
     * @param string $filename
     */
    public function setManualDbFile(string $filename)
    {
        $this->manualDbFile = (string) $filename;

        // Reconfigure SignatureFormatter with new manual database
        try {
            SignatureFormatter::setManual($this->getManual());
        } catch (InvalidManualException $e) {
            // Show user-friendly error for invalid explicitly configured manual
            throw new \InvalidArgumentException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Get the current PHP manual database file.
     *
     * Searches for manual files in order of preference:
     *  1. php_manual.php (v3 format)
     *  2. php_manual.sqlite (v2 format, legacy)
     *
     * @return string|null Default: '~/.local/share/psysh/php_manual.*'
     */
    public function getManualDbFile()
    {
        if (isset($this->manualDbFile)) {
            return $this->manualDbFile;
        }

        // Prefer v3 format over v2
        $files = $this->configPaths->dataFiles(['php_manual.php', 'php_manual.sqlite']);
        if (!empty($files)) {
            if ($this->warnOnMultipleConfigs && \count($files) > 1) {
                $prettyFiles = \array_map([ConfigPaths::class, 'prettyPath'], $files);
                $msg = \sprintf('Multiple manual database files found: %s. Using %s', \implode(', ', $prettyFiles), $prettyFiles[0]);
                \trigger_error($msg, \E_USER_NOTICE);
            }

            return $this->manualDbFile = $files[0];
        }

        return null;
    }

    /**
     * Get a PHP manual database connection.
     *
     * @deprecated Use getManual() instead for unified access to all manual formats
     *
     * @return \PDO|null
     */
    public function getManualDb()
    {
        if (!isset($this->manualDb)) {
            $dbFile = $this->getManualDbFile();
            if ($dbFile !== null && \is_file($dbFile) && \substr($dbFile, -7) === '.sqlite') {
                try {
                    $this->manualDb = new \PDO('sqlite:'.$dbFile);

                    // Validate the database has the required structure
                    $result = $this->manualDb->query("SELECT name FROM sqlite_master WHERE type='table' AND name='php_manual'");
                    if ($result === false || $result->fetchColumn() === false) {
                        throw new InvalidManualException('Manual database is missing required tables', $dbFile);
                    }
                } catch (\PDOException $e) {
                    if ($e->getMessage() === 'could not find driver') {
                        throw new RuntimeException('SQLite PDO driver not found', 0, $e);
                    } else {
                        throw new InvalidManualException('Invalid SQLite manual database: '.$e->getMessage(), $dbFile, 0, $e);
                    }
                }
            }
        }

        return $this->manualDb;
    }

    /**
     * Get a PHP manual instance.
     *
     * Automatically detects the manual format and returns the appropriate manual type.
     * Supports v2 (SQLite) and v3 (PHP) formats.
     *
     * @return ManualInterface|null
     */
    public function getManual()
    {
        if (!isset($this->manual)) {
            $this->manual = $this->loadManual();
        }

        return $this->manual;
    }

    /**
     * Load manual from filesystem or bundled Phar, preferring newest English version.
     *
     * Priority:
     *  1. Explicit config: if user configured a specific file, use it
     *  2. Local non-English: user downloaded a specific language
     *  3. Newest English: compare local vs bundled
     *
     * @return ManualInterface|null
     */
    private function loadManual()
    {
        // Priority 1: If user explicitly configured a manual file, use it
        if (isset($this->manualDbFile)) {
            $manual = $this->loadManualFromFile($this->manualDbFile);
            if ($manual !== null) {
                return $manual;
            }
        }

        // Check filesystem locations (auto-discovered)
        $localFile = $this->getManualDbFile();
        $localManual = null;
        $localMeta = null;

        if ($localFile !== null && \is_file($localFile)) {
            try {
                $localManual = $this->loadManualFromFile($localFile);
                if ($localManual !== null) {
                    $localMeta = $localManual->getMeta();
                }
            } catch (InvalidManualException $e) {
                // Auto-discovered file is invalid - fall back to bundled
            }
        }

        // Check bundled manual in Phar
        $bundledManual = null;
        $bundledMeta = null;

        if (\Phar::running(false)) {
            $bundledFile = 'phar://'.\Phar::running(false).'/php_manual.php';
            if (\is_file($bundledFile)) {
                $bundledManual = $this->loadManualFromFile($bundledFile);
                if ($bundledManual !== null) {
                    $bundledMeta = $bundledManual->getMeta();
                }
            }
        }

        // Priority 2: If local exists and is not English, use local (user wants that language)
        // Priority 3: Otherwise, use newest English (compare local vs bundled)

        if ($localManual !== null) {
            $localLang = $localMeta['lang'] ?? 'en';

            // Non-English local manual takes priority
            if ($localLang !== 'en') {
                return $localManual;
            }

            // Both are English, pick newest
            $localTimestamp = $localMeta['built_at'] ?? 0;
            $bundledTimestamp = $bundledMeta['built_at'] ?? 0;

            if ($localTimestamp >= $bundledTimestamp) {
                return $localManual;
            } else {
                return $bundledManual;
            }
        }

        // No local manual, use bundled if available
        return $bundledManual;
    }

    /**
     * Load a manual from a file path.
     *
     * @param string $file
     *
     * @return ManualInterface|null
     *
     * @throws InvalidManualException if manual file is invalid
     */
    private function loadManualFromFile(string $file)
    {
        // Detect format by extension
        if (\substr($file, -4) === '.php') {
            return new V3Manual($file);
        } elseif (\substr($file, -7) === '.sqlite') {
            // Legacy v2 format
            if ($db = $this->getManualDb()) {
                return new V2Manual($db);
            }
        }

        return null;
    }

    /**
     * Add an array of casters definitions.
     *
     * @param array $casters
     */
    public function addCasters(array $casters)
    {
        $this->casters = \array_merge($this->casters, $casters);

        if (isset($this->presenter)) {
            $this->presenter->addCasters($casters);
        }
    }

    /**
     * Get the Presenter service.
     */
    public function getPresenter(): Presenter
    {
        if (!isset($this->presenter)) {
            $this->presenter = new Presenter($this->getOutput()->getFormatter(), $this->forceArrayIndexes(), $this->useDeprecatedMultilineStrings());
            if ($this->casters !== []) {
                $this->presenter->addCasters($this->casters);
            }
        }

        return $this->presenter;
    }

    /**
     * Enable or disable warnings on multiple configuration or data files.
     *
     * @see self::warnOnMultipleConfigs()
     *
     * @param bool $warnOnMultipleConfigs
     */
    public function setWarnOnMultipleConfigs(bool $warnOnMultipleConfigs)
    {
        $this->warnOnMultipleConfigs = (bool) $warnOnMultipleConfigs;
    }

    /**
     * Check whether to warn on multiple configuration or data files.
     *
     * By default, PsySH will use the file with highest precedence, and will
     * silently ignore all others. With this enabled, a warning will be emitted
     * (but not an exception thrown) if multiple configuration or data files
     * are found.
     *
     * This will default to true in a future release, but is false for now.
     */
    public function warnOnMultipleConfigs(): bool
    {
        return $this->warnOnMultipleConfigs;
    }

    /**
     * Set the current color mode.
     *
     * @throws \InvalidArgumentException if the color mode isn't auto, forced or disabled
     *
     * @param string $colorMode
     */
    public function setColorMode(string $colorMode)
    {
        $validColorModes = [
            self::COLOR_MODE_AUTO,
            self::COLOR_MODE_FORCED,
            self::COLOR_MODE_DISABLED,
        ];

        if (!\in_array($colorMode, $validColorModes)) {
            throw new \InvalidArgumentException('Invalid color mode: '.$colorMode);
        }

        $this->colorMode = $colorMode;
    }

    /**
     * Get the current color mode.
     */
    public function colorMode(): string
    {
        return $this->colorMode;
    }

    /**
     * Set the shell's interactive mode.
     *
     * @throws \InvalidArgumentException if interactive mode isn't disabled, forced, or auto
     *
     * @param string $interactiveMode
     */
    public function setInteractiveMode(string $interactiveMode)
    {
        $validInteractiveModes = [
            self::INTERACTIVE_MODE_AUTO,
            self::INTERACTIVE_MODE_FORCED,
            self::INTERACTIVE_MODE_DISABLED,
        ];

        if (!\in_array($interactiveMode, $validInteractiveModes)) {
            throw new \InvalidArgumentException('Invalid interactive mode: '.$interactiveMode);
        }

        $this->interactiveMode = $interactiveMode;
    }

    /**
     * Get the current interactive mode.
     */
    public function interactiveMode(): string
    {
        return $this->interactiveMode;
    }

    /**
     * Set an update checker service instance.
     *
     * @param Checker $checker
     */
    public function setChecker(Checker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * Get an update checker service instance.
     *
     * If none has been explicitly defined, this will create a new instance.
     */
    public function getChecker(): Checker
    {
        if (!isset($this->checker)) {
            $interval = $this->getUpdateCheck();
            switch ($interval) {
                case Checker::ALWAYS:
                    $this->checker = new GitHubChecker();
                    break;

                case Checker::DAILY:
                case Checker::WEEKLY:
                case Checker::MONTHLY:
                    $checkFile = $this->getUpdateCheckCacheFile();
                    if ($checkFile === false) {
                        $this->checker = new NoopChecker();
                    } else {
                        $this->checker = new IntervalChecker($checkFile, $interval);
                    }
                    break;

                case Checker::NEVER:
                    $this->checker = new NoopChecker();
                    break;
            }
        }

        return $this->checker;
    }

    /**
     * Get the current update check interval.
     *
     * One of 'always', 'daily', 'weekly', 'monthly' or 'never'. If none is
     * explicitly set, default to 'weekly'.
     */
    public function getUpdateCheck(): string
    {
        return isset($this->updateCheck) ? $this->updateCheck : Checker::WEEKLY;
    }

    /**
     * Set the update check interval.
     *
     * @throws \InvalidArgumentException if the update check interval is unknown
     *
     * @param string $interval
     */
    public function setUpdateCheck(string $interval)
    {
        $validIntervals = [
            Checker::ALWAYS,
            Checker::DAILY,
            Checker::WEEKLY,
            Checker::MONTHLY,
            Checker::NEVER,
        ];

        if (!\in_array($interval, $validIntervals)) {
            throw new \InvalidArgumentException('Invalid update check interval: '.$interval);
        }

        $this->updateCheck = $interval;
    }

    /**
     * Get a cache file path for the update checker.
     *
     * @return string|false Return false if config file/directory is not writable
     */
    public function getUpdateCheckCacheFile()
    {
        $configDir = $this->configPaths->currentConfigDir();
        if ($configDir === null) {
            return false;
        }

        return ConfigPaths::touchFileWithMkdir($configDir.'/update_check.json');
    }

    /**
     * Get the current manual update check interval.
     *
     * One of 'always', 'daily', 'weekly', 'monthly' or 'never'. If none is
     * explicitly set, default to 'weekly'.
     */
    public function getUpdateManualCheck(): string
    {
        return isset($this->updateManualCheck) ? $this->updateManualCheck : ManualUpdater\Checker::WEEKLY;
    }

    /**
     * Set the manual update check interval.
     *
     * @throws \InvalidArgumentException if the update check interval is unknown
     *
     * @param string $interval
     */
    public function setUpdateManualCheck(string $interval)
    {
        $validIntervals = [
            ManualUpdater\Checker::ALWAYS,
            ManualUpdater\Checker::DAILY,
            ManualUpdater\Checker::WEEKLY,
            ManualUpdater\Checker::MONTHLY,
            ManualUpdater\Checker::NEVER,
        ];

        if (!\in_array($interval, $validIntervals)) {
            throw new \InvalidArgumentException('Invalid manual update check interval: '.$interval);
        }

        $this->updateManualCheck = $interval;
    }

    /**
     * Get a manual update checker.
     *
     * If none has been explicitly defined, this will create a new instance.
     *
     * @param string|null $lang   Override language (otherwise uses current manual's language or 'en')
     * @param bool        $always Force immediate check, ignoring interval setting
     *
     * @return ManualUpdater\Checker|null
     */
    public function getManualChecker(?string $lang = null, bool $always = false): ?ManualUpdater\Checker
    {
        // Get current manual info
        $manualFile = $this->getManualDbFile();
        $currentMeta = null;
        if ($manualFile && \file_exists($manualFile)) {
            $manual = $this->getManual();
            if ($manual) {
                $currentMeta = $manual->getMeta();
            }
        }

        $currentVersion = $currentMeta['version'] ?? null;
        $currentLang = $currentMeta['lang'] ?? null;

        // Determine language (priority: explicit param, current manual, default to English)
        if ($lang === null) {
            $lang = $currentLang ?? 'en';
        }

        // Determine format from current manual file extension, default to v3
        $format = 'php';
        if ($manualFile && \substr($manualFile, -7) === '.sqlite') {
            $format = 'sqlite';
        }

        $interval = $always ? ManualUpdater\Checker::ALWAYS : $this->getUpdateManualCheck();
        switch ($interval) {
            case ManualUpdater\Checker::ALWAYS:
                return new ManualUpdater\GitHubChecker($lang, $format, $currentVersion, $currentLang);

            case ManualUpdater\Checker::DAILY:
            case ManualUpdater\Checker::WEEKLY:
            case ManualUpdater\Checker::MONTHLY:
                $checkFile = $this->getManualUpdateCheckCacheFile();
                if ($checkFile === false) {
                    return null; // No writable cache file
                }

                $baseChecker = new ManualUpdater\GitHubChecker($lang, $format, $currentVersion, $currentLang);

                return new ManualUpdater\IntervalChecker($baseChecker, $checkFile, $interval);

            case ManualUpdater\Checker::NEVER:
            default:
                return null;
        }
    }

    /**
     * Get a cache file path for the manual update checker.
     *
     * @return string|false Return false if config file/directory is not writable
     */
    public function getManualUpdateCheckCacheFile()
    {
        $configDir = $this->configPaths->currentConfigDir();
        if ($configDir === null) {
            return false;
        }

        return ConfigPaths::touchFileWithMkdir($configDir.'/manual_update_check.json');
    }

    /**
     * Get the manual installation directory path.
     *
     * @return string|false Return false if data directory is not writable
     */
    public function getManualInstallDir()
    {
        $dataDir = $this->configPaths->currentDataDir();
        if ($dataDir === null) {
            return false;
        }

        if (!ConfigPaths::ensureDir($dataDir)) {
            return false;
        }

        return $dataDir;
    }

    /**
     * Set the startup message.
     *
     * @param string $message
     */
    public function setStartupMessage(string $message)
    {
        $this->startupMessage = $message;
    }

    /**
     * Get the startup message.
     *
     * @return string|null
     */
    public function getStartupMessage()
    {
        return $this->startupMessage;
    }

    /**
     * Set the prompt.
     *
     * @deprecated The `prompt` configuration has been replaced by Themes and support will
     * eventually be removed. In the meantime, prompt is applied first by the Theme, then overridden
     * by any explicitly defined prompt.
     *
     * Note that providing a prompt but not a theme config will implicitly use the `classic` theme.
     */
    public function setPrompt(string $prompt)
    {
        $this->prompt = $prompt;

        if (isset($this->theme)) {
            $this->theme->setPrompt($prompt);
        }
    }

    /**
     * Get the prompt.
     *
     * @return string|null
     */
    public function getPrompt()
    {
        return $this->prompt;
    }

    /**
     * Get the force array indexes.
     */
    public function forceArrayIndexes(): bool
    {
        return $this->forceArrayIndexes;
    }

    /**
     * Set the force array indexes.
     *
     * @param bool $forceArrayIndexes
     */
    public function setForceArrayIndexes(bool $forceArrayIndexes)
    {
        $this->forceArrayIndexes = $forceArrayIndexes;
        $this->invalidatePresenter();
    }

    /**
     * @deprecated temporary compatibility flag for triple-quoted multiline strings.
     *
     * This will be removed in the next stable release.
     */
    public function useDeprecatedMultilineStrings(): bool
    {
        return $this->useDeprecatedMultilineStrings;
    }

    /**
     * @deprecated temporary compatibility flag for triple-quoted multiline strings
     *
     * This will be removed in the next stable release
     */
    public function setUseDeprecatedMultilineStrings(bool $useDeprecatedMultilineStrings): void
    {
        $this->useDeprecatedMultilineStrings = $useDeprecatedMultilineStrings;
        $this->invalidatePresenter();
    }

    /**
     * Set the current output Theme.
     *
     * @param Theme|string|array $theme Theme (or Theme config)
     */
    public function setTheme($theme)
    {
        if (!$theme instanceof Theme) {
            $theme = new Theme($theme);
        }

        if (isset($this->prompt)) {
            $theme->setPrompt($this->prompt);
        }

        if (isset($this->theme) && $this->theme->equals($theme)) {
            return;
        }

        $this->theme = $theme;

        if (isset($this->output)) {
            $this->output->setTheme($this->theme);
            $this->applyFormatterStyles();
        }
    }

    /**
     * Get the current output Theme.
     */
    public function theme(): Theme
    {
        if (!isset($this->theme)) {
            // If a prompt is explicitly set, and a theme is not, base it on the `classic` theme.
            $this->theme = $this->prompt ? new Theme('classic') : new Theme();
        }

        if (isset($this->prompt)) {
            $this->theme->setPrompt($this->prompt);
        }

        return $this->theme;
    }

    private function invalidatePresenter(): void
    {
        $this->presenter = null;
    }

    /**
     * Set the shell output formatter styles.
     *
     * Accepts a map from style name to [fg, bg, options], for example:
     *
     *     [
     *         'error' => ['white', 'red', ['bold']],
     *         'warning' => ['black', 'yellow'],
     *     ]
     *
     * Foreground, background or options can be null, or even omitted entirely.
     *
     * @deprecated The `formatterStyles` configuration has been replaced by Themes and support will
     * eventually be removed. In the meantime, styles are applied first by the Theme, then
     * overridden by any explicitly defined formatter styles.
     */
    public function setFormatterStyles(array $formatterStyles)
    {
        foreach ($formatterStyles as $name => $style) {
            $this->formatterStyles[$name] = new OutputFormatterStyle(...$style);
        }

        if (isset($this->output)) {
            $this->applyFormatterStyles();
        }
    }

    /**
     * Internal method for applying output formatter style customization.
     *
     * This is called on initialization of the shell output, and again if the
     * formatter styles config is updated.
     *
     * @deprecated The `formatterStyles` configuration has been replaced by Themes and support will
     * eventually be removed. In the meantime, styles are applied first by the Theme, then
     * overridden by any explicitly defined formatter styles.
     */
    private function applyFormatterStyles()
    {
        $formatter = $this->output->getFormatter();
        foreach ($this->formatterStyles as $name => $style) {
            $formatter->setStyle($name, $style);
        }

        $errorFormatter = $this->output->getErrorOutput()->getFormatter();
        foreach (Theme::ERROR_STYLES as $name) {
            if (isset($this->formatterStyles[$name])) {
                $errorFormatter->setStyle($name, $this->formatterStyles[$name]);
            }
        }
    }

    /**
     * Get the configured output verbosity.
     */
    public function verbosity(): string
    {
        return $this->verbosity;
    }

    /**
     * Set the shell output verbosity.
     *
     * Accepts OutputInterface verbosity constants.
     *
     * @throws \InvalidArgumentException if verbosity level is invalid
     *
     * @param string $verbosity
     */
    public function setVerbosity(string $verbosity)
    {
        $validVerbosityLevels = [
            self::VERBOSITY_QUIET,
            self::VERBOSITY_NORMAL,
            self::VERBOSITY_VERBOSE,
            self::VERBOSITY_VERY_VERBOSE,
            self::VERBOSITY_DEBUG,
        ];

        if (!\in_array($verbosity, $validVerbosityLevels)) {
            throw new \InvalidArgumentException('Invalid verbosity level: '.$verbosity);
        }

        $this->verbosity = $verbosity;

        if (isset($this->output)) {
            $this->output->setVerbosity($this->getOutputVerbosity());
        }
    }

    /**
     * Map the verbosity configuration to OutputInterface verbosity constants.
     *
     * @return int OutputInterface verbosity level
     */
    public function getOutputVerbosity(): int
    {
        switch ($this->verbosity()) {
            case self::VERBOSITY_QUIET:
                return OutputInterface::VERBOSITY_QUIET;
            case self::VERBOSITY_VERBOSE:
                return OutputInterface::VERBOSITY_VERBOSE;
            case self::VERBOSITY_VERY_VERBOSE:
                return OutputInterface::VERBOSITY_VERY_VERBOSE;
            case self::VERBOSITY_DEBUG:
                return OutputInterface::VERBOSITY_DEBUG;
            case self::VERBOSITY_NORMAL:
            default:
                return OutputInterface::VERBOSITY_NORMAL;
        }
    }

    /**
     * Guess whether stdin is piped.
     *
     * This is mostly useful for deciding whether to use non-interactive mode.
     */
    public function inputIsPiped(): bool
    {
        if ($this->pipedInput === null) {
            $this->pipedInput = \defined('STDIN') && self::looksLikeAPipe(\STDIN);
        }

        return $this->pipedInput;
    }

    /**
     * Guess whether shell output is piped.
     *
     * This is mostly useful for deciding whether to use non-decorated output.
     */
    public function outputIsPiped(): bool
    {
        if ($this->pipedOutput === null) {
            $this->pipedOutput = self::looksLikeAPipe($this->getOutput()->getStream());
        }

        return $this->pipedOutput;
    }

    /**
     * Guess whether an input or output stream is piped.
     *
     * @param resource|int $stream
     */
    private static function looksLikeAPipe($stream): bool
    {
        return !Tty::isatty($stream);
    }
}
