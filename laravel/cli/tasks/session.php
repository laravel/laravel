<?php namespace Laravel\CLI\Tasks;

use Laravel\File;
use Laravel\Config;
use Laravel\Database\Schema;
use Laravel\Session\Drivers\Sweeper;

class Session extends Task {

	/**
	 * Generate the session table on the database.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function table($arguments = array())
	{
		Schema::table(Config::get('session.table'), function($table)
		{
			$table->create();

			// The session table consists simply of a ID, a UNIX timestamp to
			// indicate the expiration time, and a blob field which will hold
			// the serialized form of the session payload.
			$table->string('id')->length(40)->primary('session_primary');

			$table->integer('last_activity');

			$table->text('data');
		});

		// By default no session driver is specified in the configuration.
		// Since the developer is requesting that the session table be
		// created on the database, we'll set the driver to database
		// to save an extra step for the developer.
		$config = File::get(APP_PATH.'config/session'.EXT);

		$config = str_replace("'driver' => '',", "'driver' => 'database',", $config);

		File::put(APP_PATH.'config/session'.EXT, $config);

		echo "The table has been created! Database set as session driver.";
	}

	/**
	 * Sweep the expired sessions from storage.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function sweep($arguments = array())
	{
		$driver = \Laravel\Session::factory(Config::get('session.driver'));

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