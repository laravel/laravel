<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleDatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $modulesPath = base_path('Modules');
        $modules = array_filter(glob("$modulesPath/*"), 'is_dir');

        foreach ($modules as $module) {
            $moduleName = basename($module);
            $configPath = "$module/Config/database.php";

            if (file_exists($configPath)) {
                $moduleConfig = require $configPath;
                config()->set("database.connections.{$moduleName}", $moduleConfig['connection']);
            }
        }
    }
}
