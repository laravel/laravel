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

use Psy\Exception\BreakException;
use Psy\Exception\InvalidManualException;
use Psy\ExecutionLoop\ProcessForker;
use Psy\ManualUpdater\ManualUpdate;
use Psy\Util\DependencyChecker;
use Psy\VersionUpdater\GitHubChecker;
use Psy\VersionUpdater\Installer;
use Psy\VersionUpdater\SelfUpdate;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

if (!\function_exists('Psy\\sh')) {
    /**
     * Command to return the eval-able code to startup PsySH.
     *
     *     eval(\Psy\sh());
     */
    function sh(): string
    {
        if (\PHP_VERSION_ID < 80000) {
            return '\extract(\Psy\debug(\get_defined_vars(), isset($this) ? $this : @\get_called_class()));';
        }

        return <<<'EOS'
if (isset($this)) {
    \extract(\Psy\debug(\get_defined_vars(), $this));
} else {
    try {
        static::class;
        \extract(\Psy\debug(\get_defined_vars(), static::class));
    } catch (\Error $e) {
        \extract(\Psy\debug(\get_defined_vars()));
    }
}
EOS;
    }
}

if (!\function_exists('Psy\\debug')) {
    /**
     * Invoke a Psy Shell from the current context.
     *
     * For example:
     *
     *     foreach ($items as $item) {
     *         \Psy\debug(get_defined_vars());
     *     }
     *
     * If you would like your shell interaction to affect the state of the
     * current context, you can extract() the values returned from this call:
     *
     *     foreach ($items as $item) {
     *         extract(\Psy\debug(get_defined_vars()));
     *         var_dump($item); // will be whatever you set $item to in Psy Shell
     *     }
     *
     * Optionally, supply an object as the `$bindTo` parameter. This determines
     * the value `$this` will have in the shell, and sets up class scope so that
     * private and protected members are accessible:
     *
     *     class Foo {
     *         function bar() {
     *             \Psy\debug(get_defined_vars(), $this);
     *         }
     *     }
     *
     * For the static equivalent, pass a class name as the `$bindTo` parameter.
     * This makes `self` work in the shell, and sets up static scope so that
     * private and protected static members are accessible:
     *
     *     class Foo {
     *         static function bar() {
     *             \Psy\debug(get_defined_vars(), get_called_class());
     *         }
     *     }
     *
     * @param array         $vars   Scope variables from the calling context (default: [])
     * @param object|string $bindTo Bound object ($this) or class (self) value for the shell
     *
     * @return array Scope variables from the debugger session
     */
    function debug(array $vars = [], $bindTo = null): array
    {
        echo \PHP_EOL;

        $sh = new Shell();
        $sh->setScopeVariables($vars);

        // Show a couple of lines of call context for the debug session.
        //
        // @todo come up with a better way of doing this which doesn't involve injecting input :-P
        if ($sh->has('whereami')) {
            $sh->addInput('whereami -n2', true);
        }

        if (\is_string($bindTo)) {
            $sh->setBoundClass($bindTo);
        } elseif ($bindTo !== null) {
            $sh->setBoundObject($bindTo);
        }

        $sh->run();

        return $sh->getScopeVariables(false);
    }
}

if (!\function_exists('Psy\\info')) {
    /**
     * Get a bunch of debugging info about the current PsySH environment and
     * configuration.
     *
     * If a Configuration param is passed, that configuration is stored and
     * used for the current shell session, and no debugging info is returned.
     *
     * @param Configuration|null $config
     *
     * @return array|null
     */
    function info(?Configuration $config = null)
    {
        static $lastConfig;
        if ($config !== null) {
            $lastConfig = $config;

            return null;
        }

        $config = $lastConfig ?: new Configuration();
        $configEnv = (isset($_SERVER['PSYSH_CONFIG']) && $_SERVER['PSYSH_CONFIG']) ? $_SERVER['PSYSH_CONFIG'] : false;
        if ($configEnv === false && \PHP_SAPI === 'cli-server') {
            $configEnv = \getenv('PSYSH_CONFIG');
        }

        $shellInfo = [
            'PsySH version' => Shell::VERSION,
        ];

        $core = [
            'PHP version'         => \PHP_VERSION,
            'OS'                  => \PHP_OS,
            'default includes'    => $config->getDefaultIncludes(),
            'require semicolons'  => $config->requireSemicolons(),
            'strict types'        => $config->strictTypes(),
            'error logging level' => $config->errorLoggingLevel(),
            'config file'         => [
                'default config file' => ConfigPaths::prettyPath($config->getConfigFile()),
                'local config file'   => ConfigPaths::prettyPath($config->getLocalConfigFile()),
                'PSYSH_CONFIG env'    => ConfigPaths::prettyPath($configEnv),
            ],
            // 'config dir'  => $config->getConfigDir(),
            // 'data dir'    => $config->getDataDir(),
            // 'runtime dir' => $config->getRuntimeDir(),
        ];

        // Use an explicit, fresh update check here, rather than relying on whatever is in $config.
        $checker = new GitHubChecker();
        $updateAvailable = null;
        $latest = null;
        try {
            $updateAvailable = !$checker->isLatest();
            $latest = $checker->getLatest();
        } catch (\Throwable $e) {
        }

        $updates = [
            'update available'       => $updateAvailable,
            'latest release version' => $latest,
            'update check interval'  => $config->getUpdateCheck(),
            'update cache file'      => ConfigPaths::prettyPath($config->getUpdateCheckCacheFile()),
        ];

        $input = [
            'interactive mode'  => $config->interactiveMode(),
            'input interactive' => $config->getInputInteractive(),
            'bracketed paste'   => $config->useBracketedPaste(),
            'yolo'              => $config->yolo(),
        ];

        $readlineService = $config->getReadline();
        $interactiveReadline = $readlineService instanceof Readline\InteractiveReadlineInterface;

        $readline = [
            'readline available' => $config->hasReadline(),
            'readline enabled'   => $config->useReadline(),
            'readline service'   => \get_class($readlineService),
        ];

        // Only show system readline library info when actually using it
        if ($config->hasReadline() && !$interactiveReadline) {
            $info = \readline_info();

            if (isset($info['library_version'])) {
                $readline['readline library'] = $info['library_version'];
            }

            if (isset($info['readline_name']) && $info['readline_name'] !== '') {
                $readline['readline name'] = $info['readline_name'];
            }
        }

        $readline['interactive readline requested'] = $config->useExperimentalReadline();

        if ($interactiveReadline) {
            $readline['syntax highlighting'] = $config->useSyntaxHighlighting();
        }

        // Show supported diagnostic when requested but not active
        if (!$interactiveReadline) {
            $readline['interactive readline supported'] = Readline\InteractiveReadline::isSupported();
        }

        $output = [
            'color mode'       => $config->colorMode(),
            'output decorated' => $config->getOutputDecorated(),
            'output verbosity' => $config->verbosity(),
            'output pager'     => $config->getPager(),
        ];

        $theme = $config->theme();
        $output['theme'] = $theme->getName() ?? [
            // @todo show styles (but only if they're different than default?)
            'compact'      => $theme->compact(),
            'prompt'       => $theme->prompt(),
            'bufferPrompt' => $theme->bufferPrompt(),
            'replayPrompt' => $theme->replayPrompt(),
            'returnValue'  => $theme->returnValue(),
        ];

        $pcntl = [
            'pcntl available' => DependencyChecker::functionsAvailable(ProcessForker::PCNTL_FUNCTIONS),
            'posix available' => DependencyChecker::functionsAvailable(ProcessForker::POSIX_FUNCTIONS),
        ];

        if ($disabledPcntl = DependencyChecker::functionsDisabled(ProcessForker::PCNTL_FUNCTIONS)) {
            $pcntl['disabled pcntl functions'] = $disabledPcntl;
        }

        if ($disabledPosix = DependencyChecker::functionsDisabled(ProcessForker::POSIX_FUNCTIONS)) {
            $pcntl['disabled posix functions'] = $disabledPosix;
        }

        $pcntl['use pcntl'] = $config->usePcntl();

        $history = [
            'history file'     => ConfigPaths::prettyPath($config->getHistoryFile()),
            'history format'   => $interactiveReadline ? 'jsonl' : 'plain text',
            'history size'     => $config->getHistorySize(),
            'erase duplicates' => $config->getEraseDuplicates(),
        ];

        $manualDbFile = $config->getManualDbFile();
        $manual = null;
        $manualError = null;

        try {
            $manual = $config->getManual();
        } catch (InvalidManualException $e) {
            $manualError = $e->getMessage();
        }

        // If we have a manual but no db file path, it's bundled in the PHAR
        if ($manual && !$manualDbFile && \Phar::running(false)) {
            $docs = [
                'manual db file' => '<bundled>',
            ];
        } else {
            $docs = [
                'manual db file' => ConfigPaths::prettyPath($manualDbFile),
            ];
        }

        if ($manualError) {
            $docs['manual error'] = $manualError;
        } elseif ($manual) {
            $meta = $manual->getMeta();

            foreach ($meta as $key => $val) {
                switch ($key) {
                    case 'built_at':
                        $d = new \DateTime('@'.$val);
                        $val = $d->format(\DateTime::RFC2822);
                        break;
                }
                $key = 'manual '.\str_replace('_', ' ', $key);
                $docs[$key] = $val;
            }
        }

        $completionIntegration = 'disabled';
        if ($config->useTabCompletion()) {
            $completionIntegration = $interactiveReadline ? 'interactive readline' : 'legacy readline shim';
        }

        $autocomplete = [
            'tab completion enabled'  => $config->useTabCompletion(),
            'completion integration'  => $completionIntegration,
            'inline suggestions'      => $interactiveReadline && $config->useSuggestions(),
        ];

        $warmers = $config->getAutoloadWarmers();
        $autoload = [
            'autoload warming enabled' => !empty($warmers),
            'warmers configured'       => \count($warmers),
        ];

        if (!empty($warmers)) {
            $autoload['warmer types'] = \array_map('get_class', $warmers);

            // Add extended info for ComposerAutoloadWarmer
            foreach ($warmers as $warmer) {
                if ($warmer instanceof TabCompletion\AutoloadWarmer\ComposerAutoloadWarmer) {
                    try {
                        $autoload['composer warmer config'] = [
                            'include vendor' => Sudo::fetchProperty($warmer, 'includeVendor'),
                            'include tests'  => Sudo::fetchProperty($warmer, 'includeTests'),
                            'vendor dir'     => Sudo::fetchProperty($warmer, 'vendorDir'),
                            'phar prefix'    => Sudo::fetchProperty($warmer, 'pharPrefix'),
                        ];

                        $includeNamespaces = Sudo::fetchProperty($warmer, 'includeNamespaces');
                        $excludeNamespaces = Sudo::fetchProperty($warmer, 'excludeNamespaces');
                        $includeVendorNamespaces = Sudo::fetchProperty($warmer, 'includeVendorNamespaces');
                        $excludeVendorNamespaces = Sudo::fetchProperty($warmer, 'excludeVendorNamespaces');

                        if (!empty($includeNamespaces)) {
                            $autoload['composer warmer config']['include namespaces'] = $includeNamespaces;
                        }
                        if (!empty($excludeNamespaces)) {
                            $autoload['composer warmer config']['exclude namespaces'] = $excludeNamespaces;
                        }
                        if (!empty($includeVendorNamespaces)) {
                            $autoload['composer warmer config']['include vendor namespaces'] = $includeVendorNamespaces;
                        }
                        if (!empty($excludeVendorNamespaces)) {
                            $autoload['composer warmer config']['exclude vendor namespaces'] = $excludeVendorNamespaces;
                        }
                    } catch (\ReflectionException $e) {
                        // shrug
                    }
                    break; // Only show info for the first ComposerAutoloadWarmer
                }
            }
        }

        $implicitUse = [];
        $implicitUseConfig = $config->getImplicitUse();
        if (\is_array($implicitUseConfig)) {
            if (!empty($implicitUseConfig['includeNamespaces'])) {
                $implicitUse['include namespaces'] = $implicitUseConfig['includeNamespaces'];
            }
            if (!empty($implicitUseConfig['excludeNamespaces'])) {
                $implicitUse['exclude namespaces'] = $implicitUseConfig['excludeNamespaces'];
            }
        }
        if (empty($implicitUse)) {
            $implicitUse = false;
        }

        // Shenanigans, but totally justified.
        try {
            if ($shell = Sudo::fetchProperty($config, 'shell')) {
                $shellClass = \get_class($shell);
                if ($shellClass !== 'Psy\\Shell') {
                    $shellInfo = [
                        'PsySH version' => $shell::VERSION,
                        'Shell class'   => $shellClass,
                    ];
                }

                try {
                    $core['loop listeners'] = \array_map('get_class', Sudo::fetchProperty($shell, 'loopListeners'));
                } catch (\ReflectionException $e) {
                    // shrug
                }

                $core['commands'] = \array_map('get_class', $shell->all());

                try {
                    $autocomplete['custom legacy matchers'] = \array_map('get_class', Sudo::fetchProperty($shell, 'matchers'));
                } catch (\ReflectionException $e) {
                    // shrug
                }

                try {
                    $completionEngine = Sudo::fetchProperty($shell, 'completionEngine');
                    if ($completionEngine !== null) {
                        $autocomplete['completion sources'] = \array_map('get_class', Sudo::fetchProperty($completionEngine, 'sources'));
                        $autocomplete['completion refiners'] = \array_map('get_class', Sudo::fetchProperty($completionEngine, 'refiners'));
                    }
                } catch (\ReflectionException $e) {
                    // shrug
                }

                try {
                    $pendingCompletionSources = Sudo::fetchProperty($shell, 'pendingCompletionSources');
                    if (!empty($pendingCompletionSources)) {
                        $autocomplete['pending completion sources'] = \array_map('get_class', $pendingCompletionSources);
                    }
                } catch (\ReflectionException $e) {
                    // shrug
                }
            }
        } catch (\ReflectionException $e) {
            // shrug
        }

        // @todo Show Presenter / custom casters.

        return \array_merge(
            $shellInfo,
            $core,
            \compact(
                'updates',
                'pcntl',
                'input',
                'readline',
                'output',
                'history',
                'docs',
                'autocomplete',
                'autoload'
            ),
            [
                'implicit use' => $implicitUse,
            ],
        );
    }
}

if (!\function_exists('Psy\\bin')) {
    /**
     * `psysh` command line executable.
     *
     * @return \Closure
     */
    function bin(): \Closure
    {
        return function () {
            // Ensure exit() works when uopz extension is loaded with uopz.exit=0
            if (\function_exists('uopz_allow_exit')) {
                \uopz_allow_exit(true);
            }

            if (!isset($_SERVER['PSYSH_IGNORE_ENV']) || !$_SERVER['PSYSH_IGNORE_ENV']) {
                if (\defined('HHVM_VERSION_ID')) {
                    \fwrite(\STDERR, 'PsySH v0.11 and higher does not support HHVM. Install an older version, or set the environment variable PSYSH_IGNORE_ENV=1 to override this restriction and proceed anyway.'.\PHP_EOL);
                    exit(1);
                }

                if (\PHP_VERSION_ID < 70400) {
                    \fwrite(\STDERR, 'PHP 7.4.0 or higher is required. You can set the environment variable PSYSH_IGNORE_ENV=1 to override this restriction and proceed anyway.'.\PHP_EOL);
                    exit(1);
                }

                if (\PHP_VERSION_ID > 89999) {
                    \fwrite(\STDERR, 'PHP 9 or higher is not supported. You can set the environment variable PSYSH_IGNORE_ENV=1 to override this restriction and proceed anyway.'.\PHP_EOL);
                    exit(1);
                }

                if (!\function_exists('json_encode')) {
                    \fwrite(\STDERR, 'The JSON extension is required. Please install it. You can set the environment variable PSYSH_IGNORE_ENV=1 to override this restriction and proceed anyway.'.\PHP_EOL);
                    exit(1);
                }

                if (!\function_exists('token_get_all')) {
                    \fwrite(\STDERR, 'The Tokenizer extension is required. Please install it. You can set the environment variable PSYSH_IGNORE_ENV=1 to override this restriction and proceed anyway.'.\PHP_EOL);
                    exit(1);
                }
            }

            $usageException = null;
            $shellIsPhar = Shell::isPhar();

            $input = new ArgvInput();
            try {
                $input->bind(new InputDefinition(\array_merge(Configuration::getInputOptions(), [
                    new InputOption('help', 'h', InputOption::VALUE_NONE),
                    new InputOption('version', 'V', InputOption::VALUE_NONE),
                    new InputOption('self-update', 'u', InputOption::VALUE_NONE),
                    new InputOption('update-manual', null, InputOption::VALUE_OPTIONAL, '', false),
                    new InputOption('info', null, InputOption::VALUE_NONE),

                    new InputArgument('include', InputArgument::IS_ARRAY),
                ])));
            } catch (\RuntimeException $e) {
                $usageException = $e;
            }

            if ($usageException === null) {
                $cwd = null;
                if ($input->hasOption('cwd')) {
                    $cwd = $input->getOption('cwd');
                }
                if ($cwd === null || $cwd === '') {
                    $cwd = $input->getParameterOption('--cwd', null, true);
                }
                if ($cwd !== null && $cwd !== '') {
                    if (!@\chdir($cwd)) {
                        \fwrite(\STDERR, 'Invalid --cwd directory: '.$cwd.\PHP_EOL);
                        exit(1);
                    }
                }
            }

            try {
                $config = Configuration::fromInput($input);
            } catch (\InvalidArgumentException $e) {
                $usageException = $e;
            }

            // Handle --help
            if (!isset($config) || $usageException !== null || $input->getOption('help')) {
                // Determine if we should use colors
                $useColors = true;
                if ($input->hasParameterOption(['--no-color'])) {
                    $useColors = false;
                } elseif (!$input->hasParameterOption(['--color']) && !\stream_isatty(\STDOUT)) {
                    $useColors = false;
                }

                // Create output formatter for proper tag rendering
                $formatter = new OutputFormatter($useColors);

                if ($usageException !== null) {
                    echo $usageException->getMessage().\PHP_EOL.\PHP_EOL;
                }

                $version = Shell::getVersionHeader(false);
                $argv = isset($_SERVER['argv']) ? $_SERVER['argv'] : [];
                $name = $argv ? \basename(\reset($argv)) : 'psysh';

                $selfUpdateOption = $shellIsPhar ? "\n  <info>-u, --self-update</info>       Install a newer version if available" : '';

                $helpText = <<<EOL
$version

<comment>Description:</>
  A runtime developer console, interactive debugger and REPL for PHP

<comment>Usage:</>
  $name [options] [--] [<files>...]

<comment>Arguments:</>
  <info>files</info>                        PHP file(s) to load before starting the shell

<comment>Options:</>
  <info>-h, --help</info>                   Display this help message
      <info>--info</info>                   Display PsySH environment and configuration info
  <info>-V, --version</info>                Display the PsySH version{$selfUpdateOption}
      <info>--update-manual[=LANG]</info>   Download and install the latest PHP manual (optional language code)

      <info>--experimental-readline</info>  Use experimental interactive readline implementation
      <info>--warm-autoload</info>          Enable autoload warming for better tab completion
      <info>--yolo</info>                   Run PsySH without input validation (you don't want this)

  <info>-c, --config=FILE</info>            Use an alternate PsySH config file location
      <info>--cwd=PATH</info>               Use an alternate working directory
      <info>--trust-project</info>          Trust the current project for this run
      <info>--no-trust-project</info>       Run in Restricted Mode for this project
      <info>--color|--no-color</info>       Force (or disable with --no-color) colors in output
  <info>-i, --interactive</info>            Force PsySH to run in interactive mode
  <info>-n, --no-interactive</info>         Run PsySH without interactive input (requires input from stdin)
      <info>--pager[=PAGER]</info>          Use an alternate output pager command (without a value, use the default pager)
      <info>--no-pager</info>               Disable paging output for this run
  <info>-r, --raw-output</info>             Print var_export-style return values (for non-interactive input)
      <info>--compact</info>                Run PsySH with compact output
  <info>-q, --quiet</info>                  Shhhhhh
  <info>-v|vv|vvv, --verbose</info>         Increase the verbosity of messages

<comment>Help:</>
  PsySH is an interactive runtime developer console for PHP. Use it as a REPL
  for quick experiments, or drop into your code with <info>eval(\Psy\sh());</info> or
  <info>\Psy\debug();</info> to inspect application state and debug interactively.

  For more information, see <info>https://psysh.org</info>

  <comment>Examples:</>

  $name                            <comment># Start interactive shell</comment>
  $name -c ~/.config/psysh.php     <comment># Use custom config</comment>
  $name --warm-autoload            <comment># Enable autoload warming</comment>
  $name index.php                  <comment># Load file before starting</comment>

EOL;

                echo $formatter->format($helpText);

                exit($usageException === null ? 0 : 1);
            }

            // Handle --version
            if ($input->getOption('version')) {
                echo Shell::getVersionHeader($config->useUnicode()).\PHP_EOL;
                exit(0);
            }

            // Handle --info
            if ($input->getOption('info')) {
                // Store config for info() function
                info($config);
                $infoData = info();

                // Format and display the info
                $output = $config->getOutput();
                if ($config->rawOutput()) {
                    $output->writeln(\var_export($infoData, true));
                } else {
                    $presenter = $config->getPresenter();
                    $output->writeln($presenter->present($infoData, null, VarDumper\Presenter::RAW), OutputInterface::OUTPUT_RAW);
                }
                exit(0);
            }

            // Handle --self-update
            if ($input->getOption('self-update')) {
                if (!$shellIsPhar) {
                    \fwrite(\STDERR, 'The --self-update option can only be used with with a phar based install.'.\PHP_EOL);
                    exit(1);
                }
                $selfUpdate = new SelfUpdate(new GitHubChecker(), new Installer());
                $result = $selfUpdate->run($input, $config->getOutput());
                exit($result);
            }

            // Handle --update-manual
            if ($input->getOption('update-manual') !== false) {
                try {
                    $manualUpdate = ManualUpdate::fromConfig($config, $input, $config->getOutput());
                    $result = $manualUpdate->run($input, $config->getOutput());
                    exit($result);
                } catch (\RuntimeException $e) {
                    \fwrite(\STDERR, $e->getMessage().\PHP_EOL);
                    exit(1);
                }
            }

            $shell = new Shell($config);

            // Pass additional arguments to Shell as 'includes'
            $shell->setIncludes($input->getArgument('include'));

            try {
                // And go!
                $exitCode = $shell->run();
                if ($exitCode !== 0) {
                    exit($exitCode);
                }
            } catch (BreakException $e) {
                // BreakException can escape if thrown before the execution loop starts
                // (though it shouldn't in normal operation)
                exit($e->getCode());
            } catch (\Throwable $e) {
                \fwrite(\STDERR, $e->getMessage().\PHP_EOL);
                exit(1);
            }
        };
    }
}
