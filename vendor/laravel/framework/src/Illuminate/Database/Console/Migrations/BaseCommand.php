<?php namespace Illuminate\Database\Console\Migrations;

use Illuminate\Console\Command;

class BaseCommand extends Command {

	/**
	 * Get the path to the migration directory.
	 *
	 * @return string
	 */
	protected function getMigrationPath()
	{
		$path = $this->input->getOption('path');

		// First, we will check to see if a path option has been defined. If it has
		// we will use the path relative to the root of this installation folder
		// so that migrations may be run for any path within the applications.
		if ( ! is_null($path))
		{
			return $this->laravel['path.base'].'/'.$path;
		}

		$package = $this->input->getOption('package');

		// If the package is in the list of migration paths we received we will put
		// the migrations in that path. Otherwise, we will assume the package is
		// is in the package directories and will place them in that location.
		if ( ! is_null($package))
		{
			return $this->packagePath.'/'.$package.'/src/migrations';
		}

		$bench = $this->input->getOption('bench');

		// Finally we will check for the workbench option, which is a shortcut into
		// specifying the full path for a "workbench" project. Workbenches allow
		// developers to develop packages along side a "standard" app install.
		if ( ! is_null($bench))
		{
			$path = "/workbench/{$bench}/src/migrations";

			return $this->laravel['path.base'].$path;
		}

		return $this->laravel['path'].'/database/migrations';
	}

}
