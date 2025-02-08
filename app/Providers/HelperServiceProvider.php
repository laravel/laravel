<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * An array of helper directory paths.
     *
     * @var array
     */
    private array $helperPaths;

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct($app)
    {
        parent::__construct($app);
        
        // Initialize helperPaths with one or more directories.
        $this->helperPaths = [
            app_path('Helpers'),
            // You can add additional directories here, e.g.:
            // base_path('custom_helpers')
        ];
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // No bindings required.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ($this->helperPaths as $path) {
            // Skip this path if it does not exist.
            if (! File::exists($path)) {
                continue;
            }

            // Load all PHP files from the current helper directory recursively.
            collect(File::allFiles($path))
                ->each(fn($file) => require_once $file->getRealPath());
        }
    }
}
