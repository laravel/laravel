<?php namespace Laravel\CLI\Tasks\Migrate;

use Laravel\Bundle;

class Resolver {

	/**
	 * The migration database instance.
	 *
	 * @var Database
	 */
	protected $database;

	/**
	 * Create a new instance of the migration resolver.
	 *
	 * @param  Database  $datbase
	 * @return void
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	/**
	 * Resolve all of the outstanding migrations for a bundle.
	 *
	 * @param  string  $bundle
	 * @return array
	 */
	public function outstanding($bundle = null)
	{
		$migrations = array();

		// If no bundle was given to the command, we'll grab every bundle for
		// the application, including the "application" bundle, which is not
		// returned by "all" method on the Bundle class.
		if (is_null($bundle))
		{
			$bundles = array_merge(Bundle::names(), array('application'));
		}
		else
		{
			$bundles = array($bundle);
		}

		foreach ($bundles as $bundle)
		{
			// First we need to grab all of the migrations that have already
			// run for this bundle, as well as all of the migration files
			// for the bundle. Once we have these, we can determine which
			// migrations are still outstanding.
			$ran = $this->database->ran($bundle);

			$files = $this->migrations($bundle);

			// To find outstanding migrations, we will simply iterate over
			// the migration files and add the files that do not exist in
			// the array of ran migrations to the outstanding array.
			foreach ($files as $key => $name)
			{
				if ( ! in_array($name, $ran))
				{
					$migrations[] = compact('bundle', 'name');
				}
			}
		}

		return $this->resolve($migrations);
	}

	/**
	 * Resolve an array of the last batch of migrations.
	 *
	 * @return array
	 */
	public function last()
	{
		return $this->resolve($this->database->last());
	}

	/**
	 * Resolve an array of migration instances.
	 *
	 * @param  array  $migrations
	 * @return array
	 */
	protected function resolve($migrations)
	{
		$instances = array();

		foreach ($migrations as $migration)
		{
			$migration = (array) $migration;

			// The migration array contains the bundle name, so we will get the
			// path to the bundle's migrations and resolve an instance of the
			// migration using the name.
			$bundle = $migration['bundle'];

			$path = Bundle::path($bundle).'migrations/';

			// Migrations are not resolved through the auto-loader, so we will
			// manually instantiate the migration class instances for each of
			// the migration names we're given.
			$name = $migration['name'];

			require_once $path.$name.EXT;

			// Since the migration name will begin with the numeric ID, we'll
			// slice off the ID so we are left with the migration class name.
			// The IDs are for sorting when resolving outstanding migrations.
			//
			// Migrations that exist within bundles other than the default
			// will be prefixed with the bundle name to avoid any possible
			// naming collisions with other bundle's migrations.
			$prefix = Bundle::class_prefix($bundle);

			$class = $prefix.substr($name, 11);

			$migration = new $class;

			// When adding to the array of instances, we will actually
			// add the migration instance, the bundle, and the name.
			// This allows the migrator to log the bundle and name
			// when the migration is executed.
			$instances[] = compact('bundle', 'name', 'migration');
		}

		// At this point the migrations are only sorted within their
		// bundles so we need to re-sort them by name to ensure they
		// are in a consistent order.
		usort($migrations, function($a, $b)
		{
			return strcmp($a['name'], $b['name']);
		});

		return $instances;
	}

	/**
	 * Grab all of the migration filenames for a bundle.
	 *
	 * @param  string  $bundle
	 * @return array
	 */
	protected function migrations($bundle)
	{
		$files = glob(Bundle::path($bundle).'migrations/*_*'.EXT);

		// When open_basedir is enabled, glob will return false on an
		// empty directory, so we will return an empty array in this
		// case so the application doesn't bomb out.
		if ($files === false)
		{
			return array();
		}

		// Once we have the array of files in the migration directory,
		// we'll take the basename of the file and remove the PHP file
		// extension, which isn't needed.
		foreach ($files as &$file)
		{
			$file = str_replace(EXT, '', basename($file));
		}

		// We'll also sort the files so that the earlier migrations
		// will be at the front of the array and will be resolved
		// first by this class' resolve method.
		sort($files);

		return $files;
	}

}