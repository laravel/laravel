<?php namespace Laravel\CLI; use Laravel\IoC;

/**
 * The migrate task is responsible for running database migrations
 * as well as migration rollbacks. We will also create an instance
 * of the migration resolver and database classes, which are used
 * to perform various support functions for the migrator.
 */
IoC::register('task: migrate', function()
{
	$database = new Tasks\Migrate\Database;

	$resolver = new Tasks\Migrate\Resolver($database);

	return new Tasks\Migrate\Migrator($resolver, $database);
});

/**
 * The bundle task is responsible for the installation of bundles
 * and their dependencies. It utilizes the bundles API to get the
 * meta-data for the available bundles.
 */
IoC::register('task: bundle', function()
{
	return new Tasks\Bundle\Bundler;
});

/**
 * The bundle repository is responsible for communicating with
 * the Laravel bundle sources to get information regarding any
 * bundles that are requested for installation.
 */
IoC::singleton('bundle.repository', function()
{
	return new Tasks\Bundle\Repository;
});

/**
 * The bundle publisher is responsible for publishing bundle
 * assets and tests to their correct directories within the
 * application, such as the web accessible directory.
 */
IoC::singleton('bundle.publisher', function()
{
	return new Tasks\Bundle\Publisher;
});

/**
 * The Github bundle provider installs bundles that live on
 * Github. This provider will add the bundle as a submodule
 * and will update the submodule so that the bundle is
 * installed into the bundle directory.
 */
IoC::singleton('bundle.provider: github', function()
{
	return new Tasks\Bundle\Providers\Github;
});