<?php namespace Laravel;

// --------------------------------------------------------------
// Bootstrap the application instance.
// --------------------------------------------------------------
require SYS_PATH.'application'.EXT;

$application = new Application;

// --------------------------------------------------------------
// Load the configuration manager and auto-loader.
// --------------------------------------------------------------
require SYS_PATH.'loader'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'arr'.EXT;

$application->config = new Config;

$paths = array(BASE_PATH, APP_PATH.'models/', APP_PATH.'libraries/');

$application->loader = new Loader($application->config->get('aliases'), $paths);

spl_autoload_register(array($application->loader, 'load'));

unset($paths);

// --------------------------------------------------------------
// Bootstrap the IoC container.
// --------------------------------------------------------------
require SYS_PATH.'container'.EXT;

$application->container = new Container($application->config->get('container'));

// --------------------------------------------------------------
// Register the core application components in the container.
// --------------------------------------------------------------
$application->container->instance('laravel.application', $application);

$application->container->instance('laravel.config', $application->config);

$application->container->instance('laravel.loader', $application->loader);

// --------------------------------------------------------------
// Set the global IoC container instance for emergency use.
// --------------------------------------------------------------
IoC::$container = $application->container;