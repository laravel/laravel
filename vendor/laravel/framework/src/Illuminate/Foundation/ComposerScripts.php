<?php

namespace Illuminate\Foundation;

use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use Illuminate\Concurrency\ProcessDriver;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Throwable;

class ComposerScripts
{
    /**
     * Handle the post-install Composer event.
     *
     * @param  \Composer\Script\Event  $event
     * @return void
     */
    public static function postInstall(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::clearCompiled();
    }

    /**
     * Handle the post-update Composer event.
     *
     * @param  \Composer\Script\Event  $event
     * @return void
     */
    public static function postUpdate(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::clearCompiled();
    }

    /**
     * Handle the post-autoload-dump Composer event.
     *
     * @param  \Composer\Script\Event  $event
     * @return void
     */
    public static function postAutoloadDump(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::clearCompiled();
    }

    /**
     * Handle the pre-package-uninstall Composer event.
     *
     * @param  \Composer\Installer\PackageEvent  $event
     * @return void
     */
    public static function prePackageUninstall(PackageEvent $event)
    {
        // Package uninstall events are only applicable when uninstalling packages in dev environments...
        if (! $event->isDevMode()) {
            return;
        }

        $eventName = null;
        try {
            require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

            $laravel = new Application(getcwd());

            $laravel->bootstrapWith([
                LoadEnvironmentVariables::class,
                LoadConfiguration::class,
            ]);

            // Ensure we can encrypt our serializable closure...
            (new EncryptionServiceProvider($laravel))->register();

            $name = $event->getOperation()->getPackage()->getName();
            $eventName = "composer_package.{$name}:pre_uninstall";

            $laravel->make(ProcessDriver::class)->run(
                static fn () => app()['events']->dispatch($eventName)
            );
        } catch (Throwable $e) {
            // Ignore any errors to allow the package removal to complete...
            $event->getIO()->write('There was an error dispatching or handling the ['.($eventName ?? 'unknown').'] event. Continuing with package removal...');
            $event->getIO()->writeError('Exception message: '.$e->getMessage(), verbosity: IOInterface::VERBOSE); // @phpstan-ignore class.notFound (Composer exists if this is running)
        }
    }

    /**
     * Clear the cached Laravel bootstrapping files.
     *
     * @return void
     */
    protected static function clearCompiled()
    {
        $laravel = new Application(getcwd());

        if (is_file($configPath = $laravel->getCachedConfigPath())) {
            @unlink($configPath);
        }

        if (is_file($servicesPath = $laravel->getCachedServicesPath())) {
            @unlink($servicesPath);
        }

        if (is_file($packagesPath = $laravel->getCachedPackagesPath())) {
            @unlink($packagesPath);
        }
    }
}
