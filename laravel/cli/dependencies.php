<?php namespace Laravel\CLI; use Laravel\IoC;

/**
 * The migrate task is responsible for running database migrations
 * as well as migration rollbacks. We will also create an instance
 * of the migration resolver and database classes, which are used
 * to perform various support functions for the migrator.
 */
if(! IoC::registered('task: migrate'))
{
	IoC::register('task: migrate', function()
	{
		$database = new Tasks\Migrate\Database;

		$resolver = new Tasks\Migrate\Resolver($database);

		return new Tasks\Migrate\Migrator($resolver, $database);
	});	
}


/**
 * The bundle task is responsible for the installation of bundles
 * and their dependencies. It utilizes the bundles API to get the
 * meta-data for the available bundles.
 */
if(! IoC::registered('task: bundle'))
{
	IoC::register('task: bundle', function()
	{
		$repository = IoC::resolve('bundle.repository');

		return new Tasks\Bundle\Bundler($repository);
	});
}

/**
 * The key task is responsible for generating a secure, random
 * key for use by the application when encrypting strings or
 * setting the hash values on cookie signatures.
 */
if(! IoC::registered('task: key'))
{
	IoC::singleton('task: key', function()
	{
		return new Tasks\Key;
	});
}

/**
 * The session task is responsible for performing tasks related
 * to the session store of the application. It can do things
 * such as generating the session table or clearing expired
 * sessions from storage.
 */
if(! IoC::registered('task: session'))
{
	IoC::singleton('task: session', function()
	{
		return new Tasks\Session\Manager;
	});
}

/**
 * The route task is responsible for calling routes within the
 * application and dumping the result. This allows for simple
 * testing of APIs and JSON based applications.
 */
if(! IoC::registered('task: route'))
{
	IoC::singleton('task: route', function()
	{
		return new Tasks\Route;
	});
}

/**
 * The "test" task is responsible for running the unit tests for
 * the application, bundles, and the core framework itself.
 * It provides a nice wrapper around PHPUnit.
 */
if(! IoC::registered('task: test'))
{
	IoC::singleton('task: test', function()
	{
		return new Tasks\Test\Runner;
	});
}

/**
 * The bundle repository is responsible for communicating with
 * the Laravel bundle sources to get information regarding any
 * bundles that are requested for installation.
 */
if(! IoC::registered('bundle.repository'))
{
	IoC::singleton('bundle.repository', function()
	{
		return new Tasks\Bundle\Repository;
	});
}

/**
 * The bundle publisher is responsible for publishing bundle
 * assets to their correct directories within the install,
 * such as the web accessible directory.
 */
if(! IoC::registered('bundle.publisher'))
{
	IoC::singleton('bundle.publisher', function()
	{
		return new Tasks\Bundle\Publisher;
	});
}

/**
 * The Github bundle provider installs bundles that live on
 * Github. This provider will add the bundle as a submodule
 * and will update the submodule so that the bundle is
 * installed into the bundle directory.
 */
if(! IoC::registered('bundle.provider: github'))
{
	IoC::singleton('bundle.provider: github', function()
	{
		return new Tasks\Bundle\Providers\Github;
	});
}

/**
 * The "help" task provides information about 
 * artisan usage.
 */
if(! IoC::registered('task: help'))
{
	IoC::singleton('task: help', function()
	{
		return new Tasks\Help;
	});
}