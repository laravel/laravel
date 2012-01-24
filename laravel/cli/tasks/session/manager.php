<?php namespace Laravel\CLI\Tasks\Session;

use Laravel\IoC;
use Laravel\File;
use Laravel\Config;
use Laravel\Session;
use Laravel\CLI\Tasks\Task;
use Laravel\Database\Schema;
use Laravel\Session\Drivers\Sweeper;
use Laravel\CLI\Tasks\Migrate\Migrator;

class Manager extends Task {

	/**
	 * Generate the session table on the database.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function table($arguments = array())
	{
		$migrator = IoC::resolve('task: migrate');

		// To create the session table, we will actually create a database
		// migration and then run it. This allows the application to stay
		// portable through migrations while still having a session table
		// generated on the database.
		$migration = $migrator->make(array('create_session_table'));

		$stub = SYS_PATH.'cli/tasks/session/migration'.EXT;

		File::put($migration, File::get($stub));

		// By default no session driver is specified in the configuration.
		// Since the developer is requesting that the session table be
		// created on the database, we'll set the driver to database
		// to save an extra step for the developer.
		$config = File::get(APP_PATH.'config/session'.EXT);

		$config = str_replace(
			"'driver' => '',",
			"'driver' => 'database',",
			$config
		);

		File::put(APP_PATH.'config/session'.EXT, $config);

		echo PHP_EOL;

		$migrator->run();
	}

	/**
	 * Sweep the expired sessions from storage.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function sweep($arguments = array())
	{
		$driver = Session::factory(Config::get('session.driver'));

		// If the driver implements the "Sweeper" interface, we know that
		// it can sweep expired sessions from storage. Not all drivers
		// need be sweepers, as stores like Memcached and APC will
		// perform their own garbage collection.
		if ($driver instanceof Sweeper)
		{
			$lifetime = Config::get('session.lifetime');

			$driver->sweep(time() - ($lifetime * 60));
		}

		echo "The session table has been swept!";
	}

}