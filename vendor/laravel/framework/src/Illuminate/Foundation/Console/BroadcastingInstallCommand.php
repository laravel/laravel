<?php

namespace Illuminate\Foundation\Console;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Attribute\AsCommand;

use function Illuminate\Support\artisan_binary;
use function Illuminate\Support\php_binary;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[AsCommand(name: 'install:broadcasting')]
class BroadcastingInstallCommand extends Command
{
    use InteractsWithComposerPackages;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:broadcasting
                    {--composer=global : Absolute path to the Composer binary which should be used to install packages}
                    {--force : Overwrite any existing broadcasting routes file}
                    {--without-reverb : Do not prompt to install Laravel Reverb}
                    {--reverb : Install Laravel Reverb as the default broadcaster}
                    {--pusher : Install Pusher as the default broadcaster}
                    {--ably : Install Ably as the default broadcaster}
                    {--without-node : Do not prompt to install Node dependencies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a broadcasting channel routes file';

    /**
     * The broadcasting driver to use.
     *
     * @var string|null
     */
    protected $driver = null;

    /**
     * The framework packages to install.
     *
     * @var array
     */
    protected $frameworkPackages = [
        'react' => '@laravel/echo-react',
        'vue' => '@laravel/echo-vue',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('config:publish', ['name' => 'broadcasting']);

        // Install channel routes file...
        if (! file_exists($broadcastingRoutesPath = $this->laravel->basePath('routes/channels.php')) || $this->option('force')) {
            $this->components->info("Published 'channels' route file.");

            copy(__DIR__.'/stubs/broadcasting-routes.stub', $broadcastingRoutesPath);
        }

        $this->uncommentChannelsRoutesFile();
        $this->enableBroadcastServiceProvider();

        $this->driver = $this->resolveDriver();

        Env::writeVariable('BROADCAST_CONNECTION', $this->driver, $this->laravel->basePath('.env'), true);

        $this->collectDriverConfig();
        $this->installDriverPackages();

        if ($this->isUsingSupportedFramework()) {
            // If this is a supported framework, we will use the framework-specific Echo helpers...
            $this->injectFrameworkSpecificConfiguration();
        } else {
            // Standard JavaScript implementation...
            if (! file_exists($echoScriptPath = $this->laravel->resourcePath('js/echo.js'))) {
                if (! is_dir($directory = $this->laravel->resourcePath('js'))) {
                    mkdir($directory, 0755, true);
                }

                $stubPath = __DIR__.'/stubs/echo-js-'.$this->driver.'.stub';

                if (! file_exists($stubPath)) {
                    $stubPath = __DIR__.'/stubs/echo-js-reverb.stub';
                }

                copy($stubPath, $echoScriptPath);
            }

            // Only add the bootstrap import for the standard JS implementation...
            if (file_exists($bootstrapScriptPath = $this->laravel->resourcePath('js/bootstrap.js'))) {
                $bootstrapScript = file_get_contents(
                    $bootstrapScriptPath
                );

                if (! str_contains($bootstrapScript, './echo')) {
                    file_put_contents(
                        $bootstrapScriptPath,
                        trim($bootstrapScript.PHP_EOL.file_get_contents(__DIR__.'/stubs/echo-bootstrap-js.stub')).PHP_EOL,
                    );
                }
            } elseif (file_exists($appScriptPath = $this->laravel->resourcePath('js/app.js'))) {
                // If no bootstrap.js, try app.js...
                $appScript = file_get_contents(
                    $appScriptPath
                );

                if (! str_contains($appScript, './echo')) {
                    file_put_contents(
                        $appScriptPath,
                        trim($appScript.PHP_EOL.file_get_contents(__DIR__.'/stubs/echo-bootstrap-js.stub')).PHP_EOL,
                    );
                }
            }
        }

        $this->installReverb();

        $this->installNodeDependencies();
    }

    /**
     * Uncomment the "channels" routes file in the application bootstrap file.
     *
     * @return void
     */
    protected function uncommentChannelsRoutesFile()
    {
        $appBootstrapPath = $this->laravel->bootstrapPath('app.php');

        $content = file_get_contents($appBootstrapPath);

        if (str_contains($content, '// channels: ')) {
            (new Filesystem)->replaceInFile(
                '// channels: ',
                'channels: ',
                $appBootstrapPath,
            );
        } elseif (str_contains($content, 'channels: ')) {
            return;
        } elseif (str_contains($content, 'commands: __DIR__.\'/../routes/console.php\',')) {
            (new Filesystem)->replaceInFile(
                'commands: __DIR__.\'/../routes/console.php\',',
                'commands: __DIR__.\'/../routes/console.php\','.PHP_EOL.'        channels: __DIR__.\'/../routes/channels.php\',',
                $appBootstrapPath,
            );
        } elseif (str_contains($content, '->withRouting(')) {
            (new Filesystem)->replaceInFile(
                '->withRouting(',
                '->withRouting('.PHP_EOL.'        channels: __DIR__.\'/../routes/channels.php\',',
                $appBootstrapPath,
            );
        } else {
            $this->components->error('Unable to register broadcast routes. Please register them manually in ['.$appBootstrapPath.'].');
        }
    }

    /**
     * Uncomment the "BroadcastServiceProvider" in the application configuration.
     *
     * @return void
     */
    protected function enableBroadcastServiceProvider()
    {
        $filesystem = new Filesystem;

        if (
            ! $filesystem->exists(app()->configPath('app.php')) ||
            ! $filesystem->exists('app/Providers/BroadcastServiceProvider.php')
        ) {
            return;
        }

        $config = $filesystem->get(app()->configPath('app.php'));

        if (str_contains($config, '// App\Providers\BroadcastServiceProvider::class')) {
            $filesystem->replaceInFile(
                '// App\Providers\BroadcastServiceProvider::class',
                'App\Providers\BroadcastServiceProvider::class',
                app()->configPath('app.php'),
            );
        }
    }

    /**
     * Collect the driver configuration.
     *
     * @return void
     */
    protected function collectDriverConfig()
    {
        $envPath = $this->laravel->basePath('.env');

        if (! file_exists($envPath)) {
            return;
        }

        match ($this->driver) {
            'pusher' => $this->collectPusherConfig(),
            'ably' => $this->collectAblyConfig(),
            default => null,
        };
    }

    /**
     * Install the driver packages.
     *
     * @return void
     */
    protected function installDriverPackages()
    {
        $package = match ($this->driver) {
            'pusher' => 'pusher/pusher-php-server',
            'ably' => 'ably/ably-php',
            default => null,
        };

        if (! $package || InstalledVersions::isInstalled($package)) {
            return;
        }

        $this->requireComposerPackages($this->option('composer'), [$package]);
    }

    /**
     * Collect the Pusher configuration.
     *
     * @return void
     */
    protected function collectPusherConfig()
    {
        $appId = text('Pusher App ID', 'Enter your Pusher app ID');
        $key = password('Pusher App Key', 'Enter your Pusher app key');
        $secret = password('Pusher App Secret', 'Enter your Pusher app secret');

        $cluster = select('Pusher App Cluster', [
            'mt1',
            'us2',
            'us3',
            'eu',
            'ap1',
            'ap2',
            'ap3',
            'ap4',
            'sa1',
        ]);

        Env::writeVariables([
            'PUSHER_APP_ID' => $appId,
            'PUSHER_APP_KEY' => $key,
            'PUSHER_APP_SECRET' => $secret,
            'PUSHER_APP_CLUSTER' => $cluster,
            'PUSHER_PORT' => 443,
            'PUSHER_SCHEME' => 'https',
            'VITE_PUSHER_APP_KEY' => '${PUSHER_APP_KEY}',
            'VITE_PUSHER_APP_CLUSTER' => '${PUSHER_APP_CLUSTER}',
            'VITE_PUSHER_HOST' => '${PUSHER_HOST}',
            'VITE_PUSHER_PORT' => '${PUSHER_PORT}',
            'VITE_PUSHER_SCHEME' => '${PUSHER_SCHEME}',
        ], $this->laravel->basePath('.env'));
    }

    /**
     * Collect the Ably configuration.
     *
     * @return void
     */
    protected function collectAblyConfig()
    {
        $this->components->warn('Make sure to enable "Pusher protocol support" in your Ably app settings.');

        $key = password('Ably Key', 'Enter your Ably key');

        $publicKey = explode(':', $key)[0] ?? $key;

        Env::writeVariables([
            'ABLY_KEY' => $key,
            'ABLY_PUBLIC_KEY' => $publicKey,
            'VITE_ABLY_PUBLIC_KEY' => '${ABLY_PUBLIC_KEY}',
        ], $this->laravel->basePath('.env'));
    }

    /**
     * Inject Echo configuration into the application's main file.
     *
     * @return void
     */
    protected function injectFrameworkSpecificConfiguration()
    {
        if ($this->appUsesVue()) {
            $importPath = $this->frameworkPackages['vue'];

            $filePaths = [
                $this->laravel->resourcePath('js/app.ts'),
                $this->laravel->resourcePath('js/app.js'),
            ];
        } else {
            $importPath = $this->frameworkPackages['react'];

            $filePaths = [
                $this->laravel->resourcePath('js/app.tsx'),
                $this->laravel->resourcePath('js/app.jsx'),
            ];
        }

        $filePath = array_filter($filePaths, function ($path) {
            return file_exists($path);
        })[0] ?? null;

        if (! $filePath) {
            $this->components->warn("Could not find file [{$filePaths[0]}]. Skipping automatic Echo configuration.");

            return;
        }

        $contents = file_get_contents($filePath);

        $echoCode = <<<JS
        import { configureEcho } from '{$importPath}';

        configureEcho({
            broadcaster: '{$this->driver}',
        });
        JS;

        preg_match_all('/^import .+;$/m', $contents, $matches);

        if (empty($matches[0])) {
            // Add the Echo configuration to the top of the file if no import statements are found...
            $newContents = $echoCode.PHP_EOL.$contents;

            file_put_contents($filePath, $newContents);
        } else {
            // Add Echo configuration after the last import...
            $lastImport = array_last($matches[0]);

            $positionOfLastImport = strrpos($contents, $lastImport);

            if ($positionOfLastImport !== false) {
                $insertPosition = $positionOfLastImport + strlen($lastImport);
                $newContents = substr($contents, 0, $insertPosition).PHP_EOL.$echoCode.substr($contents, $insertPosition);

                file_put_contents($filePath, $newContents);
            }
        }

        $this->components->info('Echo configuration added to ['.basename($filePath).'].');
    }

    /**
     * Install Laravel Reverb into the application if desired.
     *
     * @return void
     */
    protected function installReverb()
    {
        if ($this->driver !== 'reverb' || $this->option('without-reverb') || InstalledVersions::isInstalled('laravel/reverb')) {
            return;
        }

        if (! confirm('Would you like to install Laravel Reverb?', default: true)) {
            return;
        }

        $this->requireComposerPackages($this->option('composer'), [
            'laravel/reverb:^1.0',
        ]);

        Process::run([
            php_binary(),
            artisan_binary(),
            'reverb:install',
        ]);

        $this->components->info('Reverb installed successfully.');
    }

    /**
     * Install and build Node dependencies.
     *
     * @return void
     */
    protected function installNodeDependencies()
    {
        if ($this->option('without-node') || ! confirm('Would you like to install and build the Node dependencies required for broadcasting?', default: true)) {
            return;
        }

        $this->components->info('Installing and building Node dependencies.');

        if (file_exists(base_path('pnpm-lock.yaml'))) {
            $commands = [
                'pnpm add --save-dev laravel-echo pusher-js',
                'pnpm run build',
            ];
        } elseif (file_exists(base_path('yarn.lock'))) {
            $commands = [
                'yarn add --dev laravel-echo pusher-js',
                'yarn run build',
            ];
        } elseif (file_exists(base_path('bun.lock')) || file_exists(base_path('bun.lockb'))) {
            $commands = [
                'bun add --dev laravel-echo pusher-js',
                'bun run build',
            ];
        } else {
            $commands = [
                'npm install --save-dev laravel-echo pusher-js',
                'npm run build',
            ];
        }

        if ($this->appUsesVue()) {
            $commands[0] .= ' '.$this->frameworkPackages['vue'];
        } elseif ($this->appUsesReact()) {
            $commands[0] .= ' '.$this->frameworkPackages['react'];
        }

        $command = Process::command(implode(' && ', $commands))
            ->path(base_path());

        if (! windows_os()) {
            $command->tty(true);
        }

        if ($command->run()->failed()) {
            $this->components->warn("Node dependency installation failed. Please run the following commands manually: \n\n".implode(' && ', $commands));
        } else {
            $this->components->info('Node dependencies installed successfully.');
        }
    }

    /**
     * Resolve the provider to use based on the user's choice.
     *
     * @return string
     */
    protected function resolveDriver(): string
    {
        if ($this->option('reverb')) {
            return 'reverb';
        }

        if ($this->option('pusher')) {
            return 'pusher';
        }

        if ($this->option('ably')) {
            return 'ably';
        }

        return select('Which broadcasting driver would you like to use?', [
            'reverb' => 'Laravel Reverb',
            'pusher' => 'Pusher',
            'ably' => 'Ably',
        ]);
    }

    /**
     * Detect if the user is using a supported framework (React or Vue).
     *
     * @return bool
     */
    protected function isUsingSupportedFramework(): bool
    {
        return $this->appUsesReact() || $this->appUsesVue();
    }

    /**
     * Detect if the user is using React.
     *
     * @return bool
     */
    protected function appUsesReact(): bool
    {
        return $this->packageDependenciesInclude('react');
    }

    /**
     * Detect if the user is using Vue.
     *
     * @return bool
     */
    protected function appUsesVue(): bool
    {
        return $this->packageDependenciesInclude('vue');
    }

    /**
     * Detect if the package is installed.
     *
     * @return bool
     */
    protected function packageDependenciesInclude(string $package): bool
    {
        $packageJsonPath = $this->laravel->basePath('package.json');

        if (! file_exists($packageJsonPath)) {
            return false;
        }

        $packageJson = json_decode(file_get_contents($packageJsonPath), true);

        return isset($packageJson['dependencies'][$package]) ||
            isset($packageJson['devDependencies'][$package]);
    }
}
